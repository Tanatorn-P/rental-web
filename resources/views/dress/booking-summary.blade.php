@extends('layouts.customer')

@section('title', 'สรุปการจอง')
@section('nav-find', 'active')

@section('content')

<div class="booking-summary-page">

    <!-- Header -->
    <div class="booking-page-head">
        <div>
            <p class="booking-eyebrow">BOOKING SUMMARY</p>

            <h1 class="booking-page-title">สรุปการจอง</h1>

            <p class="booking-page-subtitle">
                ตรวจสอบรายละเอียดการจองของคุณก่อนดำเนินการต่อ
            </p>
        </div>
    </div>


    <!-- Main Content -->
    <div class="booking-summary-layout">

        <!-- Product Card -->
        <div class="booking-product-card">

            <div class="booking-product-image">
                @if (!empty($product->image))
                    <img
                        src="{{ asset($product->image[0]) }}"
                        alt="{{ $product->product_name }}"
                    >
                @else
                    <div class="booking-no-image">ไม่มีรูปภาพ</div>
                @endif
            </div>

            <div class="booking-product-info">

                <p class="booking-product-category">{{ $product->category }}</p>

                <h2>{{ $product->product_name }}</h2>

                <p class="booking-product-id">รหัสสินค้า: {{ $product->product_id }}</p>

                @if ($product->size)
                    <span class="booking-size">Size {{ $product->size }}</span>
                @endif

            </div>

        </div>


        <!-- Booking Details -->
        <div class="booking-detail-card">

            <h2>รายละเอียดการจอง</h2>

            <div class="booking-detail-row">
                <span>วันที่รับชุด</span>
                <strong>{{ $pickupDate }}</strong>
            </div>

            <div class="booking-detail-row">
                <span>วันที่คืนชุด</span>
                <strong>{{ $returnDate }}</strong>
            </div>

            <div class="booking-detail-row">
                <span>ระยะเวลาเช่า</span>
                <strong>{{ $price['days'] }} วัน</strong>
            </div>

            <div class="booking-divider"></div>

            <div class="booking-price-row">
                <span>ค่าเช่าชุด (แพ็กเกจ {{ $price['baseDays'] }} วัน)</span>
                <strong>{{ number_format($product->rental_fee) }} บาท</strong>
            </div>

            @if ($price['extraDays'] > 0)
                <div class="booking-price-row">
                    <span>
                        ค่าเช่าเพิ่ม {{ $price['extraDays'] }} วัน
                        × {{ number_format($price['extraFee']) }} บาท
                    </span>
                    <strong>{{ number_format($price['extraCost']) }} บาท</strong>
                </div>
            @endif

            <div class="booking-price-row">
                <span>เงินมัดจำ</span>
                <strong>{{ number_format($product->deposit) }} บาท</strong>
            </div>

            <div class="booking-total">
                <span>ยอดที่ต้องชำระ</span>
                <strong>{{ number_format($price['total'] + $product->deposit) }} บาท</strong>
            </div>


            <!-- Continue -->
            <a
                href="{{ route('dress.booking.information', [
                    'product_id' => $product->product_id,
                    'pickup_date' => $pickupDate,
                    'return_date' => $returnDate
                ]) }}"
                class="booking-confirm-button"
            >
                ดำเนินการจอง
            </a>


            <!-- Back -->
            <a
                href="{{ route('dress.availability', [
                    'product_id' => $product->product_id,
                    'pickup_date' => $pickupDate,
                    'return_date' => $returnDate
                ]) }}"
                class="booking-back-link"
            >
                ← กลับไปเลือกวันที่
            </a>

        </div>

    </div>

</div>


<style>

.booking-summary-page {
    max-width: 1200px;
    margin: 0 auto;
}


/* ===== Header ===== */

.booking-page-head { margin-bottom: 30px; }

.booking-eyebrow {
    margin: 0 0 8px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 2px;
    color: var(--mauve);
}

.booking-page-title {
    margin: 0;
    font-size: 34px;
    font-weight: 700;
    color: var(--charcoal);
}

.booking-page-subtitle {
    margin: 8px 0 0;
    color: var(--gray);
    font-size: 15px;
}


/* ===== Layout ===== */

.booking-summary-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    align-items: start;
}


/* ===== Product Card ===== */

.booking-product-card {
    background: var(--white);
    border: 1px solid var(--line);
    border-radius: 18px;
    overflow: hidden;
}

.booking-product-image {
    width: 100%;
    height: 430px;
    background: var(--beige);
    overflow: hidden;
}

.booking-product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.booking-no-image {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gray);
    font-size: 14px;
}

.booking-product-info { padding: 24px; }

.booking-product-category {
    margin: 0 0 8px;
    color: var(--mauve);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
}

.booking-product-info h2 {
    margin: 0 0 8px;
    font-size: 25px;
    color: var(--charcoal);
}

.booking-product-id {
    margin: 0 0 16px;
    color: var(--gray);
    font-size: 14px;
}

.booking-size {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    background: var(--beige);
    color: var(--charcoal);
    font-size: 13px;
    font-weight: 600;
}


/* ===== Booking Detail Card ===== */

.booking-detail-card {
    background: var(--white);
    border: 1px solid var(--line);
    border-radius: 18px;
    padding: 28px;
}

.booking-detail-card h2 {
    margin: 0 0 22px;
    font-size: 21px;
    color: var(--charcoal);
}

.booking-detail-row,
.booking-price-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    padding: 15px 0;
    border-bottom: 1px solid var(--line);
}

.booking-detail-row span,
.booking-price-row span {
    color: var(--gray);
    font-size: 14px;
}

.booking-detail-row strong,
.booking-price-row strong {
    color: var(--charcoal);
    font-size: 14px;
    text-align: right;
    white-space: nowrap;
}

.booking-divider { height: 20px; }


/* ===== Total ===== */

.booking-total {
    margin-top: 8px;
    padding: 20px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
}

.booking-total span {
    font-size: 16px;
    font-weight: 700;
    color: var(--charcoal);
}

.booking-total strong {
    font-size: 23px;
    color: var(--mauve);
}


/* ===== Buttons ===== */

.booking-confirm-button {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    margin-top: 10px;
    padding: 14px 20px;
    border-radius: 10px;
    background: var(--mauve);
    color: white;
    font-size: 15px;
    font-weight: 700;
    text-decoration: none;
    transition: 0.2s ease;
}

.booking-confirm-button:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

.booking-back-link {
    display: block;
    margin-top: 18px;
    text-align: center;
    color: var(--gray);
    font-size: 14px;
    text-decoration: none;
}

.booking-back-link:hover { color: var(--mauve); }


/* ===== Responsive ===== */

@media (max-width: 900px) {
    .booking-summary-layout { grid-template-columns: 1fr; }
    .booking-product-image { height: 400px; }
}

@media (max-width: 600px) {
    .booking-page-title { font-size: 28px; }
    .booking-product-image { height: 330px; }
    .booking-detail-card { padding: 20px; }

    .booking-detail-row,
    .booking-price-row,
    .booking-total { align-items: flex-start; }

    .booking-total strong { font-size: 20px; }
}

</style>

@endsection