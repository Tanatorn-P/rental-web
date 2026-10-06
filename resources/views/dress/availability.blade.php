@extends('layouts.customer')

@section('title', 'ตรวจสอบวันว่าง')
@section('nav-find', 'active')

@section('content')

    <!-- Page Header -->
    <div class="page-head">
        <div>
            <div class="eyebrow">AVAILABILITY CALENDAR</div>
            <h1 class="page-title">ตรวจสอบวันว่าง</h1>
            <p class="page-subtitle">เลือกวันที่รับชุด ระบบจะกำหนดวันคืนให้ตามระยะเวลาเช่าของชุด</p>
        </div>
    </div>


    <!-- Product Information -->
    <div class="availability-product">
        <div>
            <div class="eyebrow">SELECTED DRESS</div>
            <h2>{{ $product->product_name }}</h2>
            <p>รหัสสินค้า: {{ $product->product_id }}</p>
            <p>ระยะเวลาเช่า {{ $rentalDays }} วัน (ต่อวันเช่าได้ วันละ {{ number_format($extraFee) }} บาท)</p>
        </div>

        <a href="{{ route('dress.product', $product->product_id) }}" class="availability-back">
            ← กลับไปหน้ารายละเอียด
        </a>
    </div>


    <!-- Main Availability -->
    <div class="availability-layout">

        <!-- Calendar -->
        <div class="calendar-card">

            <div class="calendar-header">
                <button type="button" class="month-button" id="previousMonth">‹</button>
                <h2 id="monthYear"></h2>
                <button type="button" class="month-button" id="nextMonth">›</button>
            </div>

            <div class="weekdays">
                <div class="weekday">อา</div>
                <div class="weekday">จ</div>
                <div class="weekday">อ</div>
                <div class="weekday">พ</div>
                <div class="weekday">พฤ</div>
                <div class="weekday">ศ</div>
                <div class="weekday">ส</div>
            </div>

            <div class="calendar" id="calendar"></div>

            <div class="legend">
                <div class="legend-item">
                    <span class="legend-dot dot-available"></span>
                    <span>ว่าง</span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot dot-reserved"></span>
                    <span>จองแล้ว</span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot dot-selected"></span>
                    <span>วันที่เลือก</span>
                </div>
            </div>

        </div>


        <!-- Summary -->
        <div class="summary-card">

            <div class="eyebrow">RENTAL PERIOD</div>
            <h2>สรุปช่วงเวลาเช่า</h2>

            <div class="summary-row">
                <div>
                    <span class="summary-label">รับชุด</span>
                    <strong id="pickupDisplay">-</strong>
                </div>
            </div>

            <div class="summary-row">
                <div>
                    <span class="summary-label">ระยะเวลาเช่า</span>
                    <strong id="usageDisplay">-</strong>
                </div>
            </div>

            <div class="summary-row">
                <div>
                    <span class="summary-label">คืนชุด</span>
                    <strong id="returnDisplay">-</strong>
                </div>
            </div>

            <div class="status-box">
                <div id="statusMessage">กรุณาเลือกวันที่รับชุด</div>
            </div>

            <form method="GET">
                <input type="hidden" name="pickup_date" id="pickupDateInput" value="{{ $pickupDate }}">
                <input type="hidden" name="return_date" id="returnDateInput" value="{{ $returnDate }}">

                <button type="submit" class="availability-check-button" id="checkButton" disabled>
                    ตรวจสอบวันว่าง
                </button>
            </form>

            <button type="button" class="booking-button" id="bookingButton" disabled>
                ดำเนินการจอง
            </button>

        </div>

    </div>


    <script>

        /* ===== ข้อมูลจาก Laravel ===== */

        const bookedRanges = @json($bookedRanges);
        const bookingBaseUrl = "{{ route('dress.booking.summary', $product->product_id) }}";
        const serverSaysBooked = @json($isBooked);
        const rentalDays = {{ $rentalDays }};
        const extraDayFee = {{ (int) $extraFee }};


        /* ===== ตัวแปรปฏิทิน ===== */

        let currentDate = new Date();
        let selectedPickup = "{{ $pickupDate ?? '' }}";
        let selectedReturn = "{{ $returnDate ?? '' }}";
        let warningMessage = '';

        const todayString = formatDate(new Date());


        /* ===== ฟังก์ชันช่วย ===== */

        // Date -> YYYY-MM-DD (เวลาท้องถิ่น)
        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        // YYYY-MM-DD -> Date (เวลาท้องถิ่น กัน timezone เพี้ยน)
        function parseDate(dateString) {
            const [y, m, d] = dateString.split('-').map(Number);
            return new Date(y, m - 1, d);
        }

        // บวกวันให้วันที่ (YYYY-MM-DD -> YYYY-MM-DD)
        function addDays(dateString, days) {
            const date = parseDate(dateString);
            date.setDate(date.getDate() + days);
            return formatDate(date);
        }

        // นับจำนวนวันจากวันรับถึงวันคืน (รับ 6 -> คืน 10 = 4 วัน)
        function countDays(pickup, ret) {
            const a = Date.UTC(...pickup.split('-').map((v, i) => i === 1 ? Number(v) - 1 : Number(v)));
            const b = Date.UTC(...ret.split('-').map((v, i) => i === 1 ? Number(v) - 1 : Number(v)));
            return Math.round((b - a) / 86400000);
        }

        // วันที่นี้ถูกจองหรือไม่
        function isReserved(dateString) {
            return bookedRanges.some(range => {
                const start = String(range.pickup_date).substring(0, 10);
                const end = String(range.return_date).substring(0, 10);
                return dateString >= start && dateString <= end;
            });
        }

        // มีวันที่ถูกจองคั่นอยู่ในช่วงที่เลือกหรือไม่
        function hasReservedBetween(start, end) {
            return bookedRanges.some(range => {
                const rs = String(range.pickup_date).substring(0, 10);
                const re = String(range.return_date).substring(0, 10);
                return rs <= end && re >= start;
            });
        }

        // วันที่นี้อยู่ในช่วงที่เลือกหรือไม่
        function isSelected(dateString) {
            if (!selectedPickup) {
                return false;
            }

            if (!selectedReturn) {
                return dateString === selectedPickup;
            }

            return dateString >= selectedPickup && dateString <= selectedReturn;
        }


        /* ===== วาดปฏิทิน ===== */

        function renderCalendar() {

            const calendar = document.getElementById('calendar');
            const monthYear = document.getElementById('monthYear');

            calendar.innerHTML = '';

            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();

            const monthNames = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];

            monthYear.textContent = `${monthNames[month]} ${year}`;

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            // ช่องว่างก่อนวันที่ 1
            for (let i = 0; i < firstDay; i++) {
                const empty = document.createElement('div');
                empty.classList.add('calendar-day', 'empty');
                calendar.appendChild(empty);
            }

            // วันที่ในเดือน
            for (let day = 1; day <= daysInMonth; day++) {

                const button = document.createElement('button');
                button.type = 'button';
                button.classList.add('calendar-day');
                button.textContent = day;

                const dateString = formatDate(new Date(year, month, day));

                if (isReserved(dateString)) {

                    button.classList.add('reserved');
                    button.disabled = true;

                } else {

                    button.classList.add('available');

                    // วันที่ผ่านมาแล้วเลือกไม่ได้
                    if (dateString < todayString) {
                        button.disabled = true;
                        button.style.opacity = '0.4';
                        button.style.cursor = 'not-allowed';
                    }
                }

                if (isSelected(dateString)) {
                    button.classList.add('selected');
                }

                if (dateString === todayString) {
                    button.classList.add('today');
                }

                button.addEventListener('click', function () {
                    selectDate(dateString);
                });

                calendar.appendChild(button);
            }
        }


        /* ===== เลือกวันที่ ===== */

        // เริ่มช่วงใหม่: วันคืน = วันรับ + จำนวนวันเช่าของชุด
        function startRange(dateString) {
            selectedPickup = dateString;
            selectedReturn = addDays(dateString, rentalDays);
        }

        function selectDate(dateString) {

            warningMessage = '';

            const hasRange = selectedPickup && selectedReturn;
            const rangeOk = hasRange && !hasReservedBetween(selectedPickup, selectedReturn);
            const standardReturn = hasRange ? addDays(selectedPickup, rentalDays) : '';

            // มีช่วงเช่าที่ใช้ได้อยู่แล้ว และคลิกวันที่ไม่ก่อนวันคืนมาตรฐาน -> ปรับ/ต่อวันคืน
            if (rangeOk && dateString >= standardReturn) {

                if (hasReservedBetween(selectedPickup, dateString)) {

                    // มีวันที่ถูกจองคั่นกลาง -> เริ่มใหม่
                    warningMessage = 'ช่วงที่เลือกมีวันที่ถูกจองแล้ว กรุณาเลือกใหม่';
                    startRange(dateString);

                } else {

                    selectedReturn = dateString;

                }

            } else {

                // คลิกวันรับใหม่ (หรือคลิกก่อนวันคืนมาตรฐาน) -> เริ่มช่วงใหม่
                startRange(dateString);

            }

            updateSummary();
            renderCalendar();
        }


        /* ===== อัปเดตสรุป ===== */

        function updateSummary() {

            const pickupDisplay = document.getElementById('pickupDisplay');
            const returnDisplay = document.getElementById('returnDisplay');
            const usageDisplay = document.getElementById('usageDisplay');
            const pickupInput = document.getElementById('pickupDateInput');
            const returnInput = document.getElementById('returnDateInput');
            const checkButton = document.getElementById('checkButton');
            const bookingButton = document.getElementById('bookingButton');
            const statusMessage = document.getElementById('statusMessage');

            pickupDisplay.textContent = selectedPickup || '-';
            pickupInput.value = selectedPickup || '';

            returnDisplay.textContent = selectedReturn || '-';
            returnInput.value = selectedReturn || '';

            const hasRange = selectedPickup && selectedReturn;
            const conflict = hasRange && hasReservedBetween(selectedPickup, selectedReturn);

            // ระยะเวลาเช่า (นับจากวันรับถึงวันคืน) และจำนวนวันที่ต่อเพิ่ม
            const days = hasRange ? countDays(selectedPickup, selectedReturn) : 0;
            const extraDays = Math.max(0, days - rentalDays);

            usageDisplay.textContent = hasRange
                ? (extraDays > 0 ? `${days} วัน (ต่อเพิ่ม ${extraDays} วัน)` : `${days} วัน`)
                : '-';

            // ปุ่มและข้อความสถานะ
            checkButton.disabled = !hasRange;
            bookingButton.disabled = !hasRange || conflict;

            if (warningMessage) {

                statusMessage.textContent = warningMessage;

            } else if (conflict) {

                statusMessage.textContent = 'ช่วงที่เลือกมีวันที่ถูกจองแล้ว';

            } else if (hasRange) {

                const extraNote = extraDays > 0
                    ? `ต่อวันเช่าเพิ่ม ${extraDays} วัน (วันละ ${extraDayFee} บาท)`
                    : `คลิกวันที่หลังวันคืนหากต้องการต่อวันเช่า (วันละ ${extraDayFee} บาท)`;

                statusMessage.innerHTML =
                    '<span class="status-available">เช่า ' + days + ' วัน คืนชุดวันที่ ' + selectedReturn + '</span><br>' +
                    extraNote;

            } else {

                statusMessage.textContent = 'กรุณาเลือกวันที่รับชุด';

            }
        }


        /* ===== ปุ่มดำเนินการจอง ===== */

        document.getElementById('bookingButton').addEventListener('click', function () {

            if (!selectedPickup || !selectedReturn) {
                return;
            }

            window.location.href =
                bookingBaseUrl +
                '?pickup_date=' + encodeURIComponent(selectedPickup) +
                '&return_date=' + encodeURIComponent(selectedReturn);

        });


        /* ===== ปุ่มเดือนก่อน / ถัดไป ===== */

        document.getElementById('previousMonth').addEventListener('click', function () {
            currentDate.setDate(1);
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });

        document.getElementById('nextMonth').addEventListener('click', function () {
            currentDate.setDate(1);
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });


        /* ===== เริ่มต้น ===== */

        // มีแต่วันรับ (ไม่มีวันคืน) -> คำนวณวันคืนให้ตามจำนวนวันเช่าของชุด
        if (selectedPickup && !selectedReturn) {
            selectedReturn = addDays(selectedPickup, rentalDays);
        }

        if (selectedPickup) {
            const initialDate = parseDate(selectedPickup);

            if (!isNaN(initialDate.getTime())) {
                currentDate = initialDate;
            }
        }

        if (serverSaysBooked) {
            warningMessage = 'ช่วงที่เลือกมีวันที่ถูกจองแล้ว';
        }

        renderCalendar();
        updateSummary();

    </script>

@endsection