@extends('layouts.customer')

@section('title', 'ข้อมูลการจอง')

@section('content')

<div class="booking-information-page">

    <!-- Page Header -->
    <div class="booking-information-head">

        <p class="booking-information-eyebrow">
            BOOKING INFORMATION
        </p>

        <h1 class="booking-information-title">
            ข้อมูลการจอง
        </h1>

        <p class="booking-information-subtitle">
            กรุณากรอกข้อมูลและชำระเงินเพื่อยืนยันการจอง
        </p>

    </div>


    <!-- Main Layout -->
    <div class="booking-information-layout">


        <!-- Left : Customer Information -->
        <div class="booking-information-card">

            <h2>
                ข้อมูลลูกค้า
            </h2>

            <p class="booking-information-card-subtitle">
                กรุณาตรวจสอบและกรอกข้อมูลของคุณให้ครบถ้วน
            </p>


            <form
                method="POST"
                action="{{ route('dress.booking.confirm', $product->product_id) }}"
                enctype="multipart/form-data"
            >

                @csrf


                <!-- Hidden Booking Data -->
                <input
                    type="hidden"
                    name="pickup_date"
                    value="{{ $pickupDate }}"
                >

                <input
                    type="hidden"
                    name="return_date"
                    value="{{ $returnDate }}"
                >


                <!-- Fullname -->
                <div class="booking-form-group">

                    <label for="fullname">
                        ชื่อ-นามสกุล
                    </label>

                    <input
                        type="text"
                        id="fullname"
                        name="fullname"
                        value="{{ old('fullname', auth('customer')->user()->fullname ?? '') }}"
                        placeholder="กรอกชื่อ-นามสกุล"
                        required
                    >

                </div>


                <!-- Phone -->
                <div class="booking-form-group">

                    <label for="phone">
                        เบอร์โทรศัพท์
                    </label>

                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        value="{{ old('phone', auth('customer')->user()->phone ?? '') }}"
                        placeholder="กรอกเบอร์โทรศัพท์"
                        required
                    >

                </div>


                <!-- PromptPay -->
                <div class="booking-form-group">

                    <label for="bank_account">
                        หมายเลข PromptPay
                    </label>

                    <input
                        type="text"
                        id="bank_account"
                        name="bank_account"
                        value="{{ old('bank_account', auth('customer')->user()->bank_account ?? '') }}"
                        placeholder="กรอกหมายเลข PromptPay"
                    >

                    <small>
                        ใช้สำหรับรับเงินคืนเงินมัดจำ
                    </small>

                </div>


                <!-- Address -->
                <div class="booking-form-group">

                    <label for="address">
                        ที่อยู่
                    </label>

                    <textarea
                        id="address"
                        name="address"
                        rows="4"
                        placeholder="กรอกที่อยู่"
                        required
                    >{{ old('address', auth('customer')->user()->address ?? '') }}</textarea>

                </div>


                <!-- Payment -->
                <div class="payment-section">

                    <div class="payment-section-head">

                        <p class="booking-information-eyebrow">
                            PAYMENT
                        </p>

                        <h2>
                            ชำระเงิน
                        </h2>

                        <p>
                            สแกน QR Code เพื่อชำระเงินตามยอดที่แสดง
                        </p>

                    </div>


                    <!-- QR -->
                    <div class="promptpay-box">

                        <div class="promptpay-label">
                            PromptPay
                        </div>

                        <img
                            src="{{ asset('/payment/IMG_4108.jpg') }}"
                            alt="PromptPay QR Code"
                        >

                        <p>
                            กรุณาตรวจสอบชื่อบัญชีก่อนชำระเงิน
                        </p>

                    </div>


                    <!-- Slip -->
                    <div class="booking-form-group slip-group">

                        <label for="slip_image">
                            หลักฐานการชำระเงิน
                        </label>

                        <input
                            type="file"
                            id="slip_image"
                            name="slip_image"
                            accept="image/*"
                            required
                        >

                        <small>
                            กรุณาอัปโหลดรูปสลิปการโอนเงิน
                        </small>

                    </div>


                    <!-- Submit -->
                    <button
                        type="submit"
                        id="submitBookingButton"
                        class="submit-booking-button"
                        disabled
                    >
                        ยืนยันการชำระเงินและจอง
                    </button>


                    <!-- Back -->
                    <a
                        href="{{ route('dress.booking.summary', [
                            'product_id' => $product->product_id,
                            'pickup_date' => $pickupDate,
                            'return_date' => $returnDate
                        ]) }}"
                        class="booking-information-back"
                    >
                        ← กลับไปดูสรุปการจอง
                    </a>

                </div>

            </form>

        </div>


        <!-- Right : Booking Summary -->
        <div class="booking-information-side">

            <div class="booking-mini-card">

                <p class="booking-information-eyebrow">
                    YOUR BOOKING
                </p>

                <h2>
                    {{ $product->product_name }}
                </h2>

                <p class="booking-mini-id">
                    รหัสสินค้า: {{ $product->product_id }}
                </p>


                <div class="booking-mini-image">

                    @if (!empty($product->image))

                        @foreach ($product->image as $image)

                            <img
                                src="{{ asset($image) }}"
                                alt="{{ $product->product_name }}"
                            >

                            @break

                        @endforeach

                    @else

                        <span>
                            ไม่มีรูปภาพ
                        </span>

                    @endif

                </div>


                <div class="booking-mini-details">

                    <div>
                        <span>วันที่รับชุด</span>
                        <strong>{{ $pickupDate }}</strong>
                    </div>

                    <div>
                        <span>วันที่คืนชุด</span>
                        <strong>{{ $returnDate }}</strong>
                    </div>

                    <div>
                        <span>ค่าเช่า</span>
                        <strong>
                           {{ number_format($price['total']) }} บาท
                        </strong>
                    </div>

                    <div>
                        <span>เงินมัดจำ</span>
                        <strong>
                            {{ number_format($product->deposit, 2) }} บาท
                        </strong>
                    </div>

                </div>


                <div class="booking-mini-total">

                    <span>
                        ยอดชำระทั้งหมด
                    </span>
                     
                   
                     
                    <strong>{{ number_format($price['total'] + $product->deposit) }} บาท  </strong>
           
                   

                </div>

            </div>

        </div>

    </div>

</div>


<style>

/* =========================
   Page
========================= */

.booking-information-page {
    max-width: 1200px;
    margin: 0 auto;
}


.booking-information-head {
    margin-bottom: 30px;
}


.booking-information-eyebrow {
    margin: 0 0 8px;

    color: var(--mauve);

    font-size: 12px;
    font-weight: 700;

    letter-spacing: 2px;
}


.booking-information-title {
    margin: 0;

    color: var(--charcoal);

    font-size: 34px;
    font-weight: 700;
}


.booking-information-subtitle {
    margin: 8px 0 0;

    color: var(--gray);

    font-size: 15px;
}


/* =========================
   Layout
========================= */

.booking-information-layout {
    display: grid;

    grid-template-columns: minmax(0, 1.4fr) minmax(320px, 0.8fr);

    gap: 24px;

    align-items: start;
}


/* =========================
   Main Card
========================= */

.booking-information-card {
    background: var(--white);

    border: 1px solid var(--line);

    border-radius: 18px;

    padding: 30px;
}


.booking-information-card h2 {
    margin: 0;

    color: var(--charcoal);

    font-size: 22px;
}


.booking-information-card-subtitle {
    margin: 7px 0 26px;

    color: var(--gray);

    font-size: 14px;
}


/* =========================
   Form
========================= */

.booking-form-group {
    margin-bottom: 20px;
}


.booking-form-group label {
    display: block;

    margin-bottom: 8px;

    color: var(--charcoal);

    font-size: 14px;
    font-weight: 600;
}


.booking-form-group input,
.booking-form-group textarea {
    width: 100%;

    box-sizing: border-box;

    padding: 12px 14px;

    border: 1px solid var(--line);

    border-radius: 9px;

    background: #fff;

    color: var(--charcoal);

    font-family: inherit;
    font-size: 14px;

    outline: none;

    transition: 0.2s ease;
}


.booking-form-group input:focus,
.booking-form-group textarea:focus {
    border-color: var(--mauve);

    box-shadow: 0 0 0 3px rgba(155, 124, 131, 0.10);
}


.booking-form-group textarea {
    resize: vertical;
}


.booking-form-group small {
    display: block;

    margin-top: 7px;

    color: var(--gray);

    font-size: 12px;
}


/* =========================
   Payment
========================= */

.payment-section {
    margin-top: 32px;

    padding-top: 28px;

    border-top: 1px solid var(--line);
}


.payment-section-head h2 {
    margin: 0 0 6px;

    color: var(--charcoal);

    font-size: 22px;
}


.payment-section-head p:last-child {
    margin: 0 0 22px;

    color: var(--gray);

    font-size: 14px;
}


/* =========================
   PromptPay
========================= */

.promptpay-box {
    padding: 24px;

    border: 1px solid var(--line);

    border-radius: 14px;

    background: var(--ivory);

    text-align: center;
}


.promptpay-label {
    margin-bottom: 15px;

    color: var(--charcoal);

    font-size: 17px;
    font-weight: 700;
}


.promptpay-box img {
    display: block;

    width: 240px;
    height: 240px;

    margin: 0 auto;

    object-fit: contain;

    background: white;

    border-radius: 8px;
}


.promptpay-box p {
    margin: 15px 0 0;

    color: var(--gray);

    font-size: 12px;
}


/* =========================
   Slip
========================= */

.slip-group {
    margin-top: 22px;
}


.slip-group input[type="file"] {
    padding: 10px;

    background: white;

    cursor: pointer;
}


/* =========================
   Submit
========================= */

.submit-booking-button {
    width: 100%;

    margin-top: 8px;

    padding: 14px 20px;

    border: none;

    border-radius: 10px;

    background: var(--mauve);

    color: white;

    font-family: inherit;

    font-size: 15px;
    font-weight: 700;

    cursor: pointer;

    transition: 0.2s ease;
}


.submit-booking-button:hover:not(:disabled) {
    opacity: 0.9;

    transform: translateY(-1px);
}


.submit-booking-button:disabled {
    background: #c9c2c0;

    cursor: not-allowed;

    opacity: 0.7;
}


.booking-information-back {
    display: block;

    margin-top: 18px;

    color: var(--gray);

    text-align: center;

    font-size: 14px;

    text-decoration: none;
}


.booking-information-back:hover {
    color: var(--mauve);
}


/* =========================
   Right Card
========================= */

.booking-information-side {
    position: sticky;

    top: 24px;
}


.booking-mini-card {
    background: var(--white);

    border: 1px solid var(--line);

    border-radius: 18px;

    padding: 24px;
}


.booking-mini-card h2 {
    margin: 0 0 5px;

    color: var(--charcoal);

    font-size: 22px;
}


.booking-mini-id {
    margin: 0 0 18px;

    color: var(--gray);

    font-size: 13px;
}


/* =========================
   Product Image
========================= */

.booking-mini-image {
    width: 100%;
    height: 300px;

    overflow: hidden;

    border-radius: 12px;

    background: var(--beige);

    display: flex;

    align-items: center;
    justify-content: center;
}


.booking-mini-image img {
    width: 100%;
    height: 100%;

    object-fit: cover;

    display: block;
}


.booking-mini-image span {
    color: var(--gray);

    font-size: 13px;
}


/* =========================
   Mini Details
========================= */

.booking-mini-details {
    margin-top: 18px;
}


.booking-mini-details > div {
    display: flex;

    justify-content: space-between;

    gap: 15px;

    padding: 13px 0;

    border-bottom: 1px solid var(--line);
}


.booking-mini-details span {
    color: var(--gray);

    font-size: 13px;
}


.booking-mini-details strong {
    color: var(--charcoal);

    font-size: 13px;

    text-align: right;
}


/* =========================
   Mini Total
========================= */

.booking-mini-total {
    display: flex;

    justify-content: space-between;

    align-items: center;

    gap: 15px;

    padding-top: 20px;
}


.booking-mini-total span {
    color: var(--charcoal);

    font-size: 14px;
    font-weight: 700;
}


.booking-mini-total strong {
    color: var(--mauve);

    font-size: 21px;
}


/* =========================
   Responsive
========================= */

@media (max-width: 900px) {

    .booking-information-layout {
        grid-template-columns: 1fr;
    }

    .booking-information-side {
        position: static;
    }

}


@media (max-width: 600px) {

    .booking-information-title {
        font-size: 28px;
    }

    .booking-information-card {
        padding: 20px;
    }

    .booking-mini-card {
        padding: 20px;
    }

    .booking-mini-image {
        height: 330px;
    }

    .promptpay-box img {
        width: 210px;
        height: 210px;
    }

}

</style>


<script>

const slipInput = document.getElementById('slip_image');

const submitButton = document.getElementById('submitBookingButton');


slipInput.addEventListener('change', function () {

    if (slipInput.files.length > 0) {

        submitButton.disabled = false;

    } else {

        submitButton.disabled = true;

    }

});

</script>

@endsection