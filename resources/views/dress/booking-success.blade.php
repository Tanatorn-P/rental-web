@extends('layouts.customer')

@section('title', 'จองสำเร็จ')

@section('content')

<div class="booking-success-page">

    <div class="booking-success-card">

        <!-- Success Icon -->
        <div class="success-icon">
            ✓
        </div>


        <!-- Title -->
        <p class="success-eyebrow">
            BOOKING SUCCESSFUL
        </p>

        <h1>
            จองชุดสำเร็จแล้ว
        </h1>

        <p class="success-message">
            ระบบได้รับข้อมูลการจองและหลักฐานการชำระเงินของคุณเรียบร้อยแล้ว
        </p>


        <!-- Status -->
        <div class="success-status">

            <span class="success-status-dot"></span>

            <span>
                รอการตรวจสอบการชำระเงิน
            </span>

        </div>


        <!-- Information -->
        <div class="success-info">

            <div class="success-info-row">

                <span>
                    สถานะการจอง
                </span>

                <strong>
                    รอตรวจสอบ
                </strong>

            </div>


            <div class="success-info-row">

                <span>
                    หลักฐานการชำระเงิน
                </span>

                <strong>
                    อัปโหลดแล้ว
                </strong>

            </div>

        </div>


        <!-- Notice -->
        <div class="success-notice">

            <strong>
                กรุณารอการตรวจสอบ
            </strong>

            <p>
                ทางร้านจะตรวจสอบข้อมูลการชำระเงินและยืนยันการจองของคุณ
                เมื่อดำเนินการเรียบร้อยแล้ว สถานะการจองจะได้รับการอัปเดต
            </p>

        </div>


        <!-- Buttons -->
        <div class="success-actions">

            <a
                href="{{ route('customer.dashboard') }}"
                class="success-primary-button"
            >
                กลับไปหน้า Dashboard
            </a>


            <a
                href="{{ route('dress.find') }}"
                class="success-secondary-button"
            >
                เลือกชุดเพิ่มเติม
            </a>

        </div>

    </div>

</div>


<style>

/* =========================
   Page
========================= */

.booking-success-page {
    min-height: calc(100vh - 48px);

    display: flex;

    align-items: center;
    justify-content: center;

    padding: 40px 20px;
}


/* =========================
   Card
========================= */

.booking-success-card {
    width: 100%;
    max-width: 650px;

    padding: 45px;

    background: var(--white);

    border: 1px solid var(--line);

    border-radius: 22px;

    text-align: center;
}


/* =========================
   Success Icon
========================= */

.success-icon {
    width: 72px;
    height: 72px;

    margin: 0 auto 22px;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: #E7F1EA;

    color: #4F8A67;

    font-size: 34px;
    font-weight: 700;
}


/* =========================
   Header
========================= */

.success-eyebrow {
    margin: 0 0 8px;

    color: var(--mauve);

    font-size: 12px;
    font-weight: 700;

    letter-spacing: 2px;
}


.booking-success-card h1 {
    margin: 0;

    color: var(--charcoal);

    font-size: 32px;
    font-weight: 700;
}


.success-message {
    max-width: 480px;

    margin: 12px auto 24px;

    color: var(--gray);

    font-size: 15px;

    line-height: 1.7;
}


/* =========================
   Status
========================= */

.success-status {
    display: inline-flex;

    align-items: center;

    gap: 9px;

    padding: 9px 15px;

    border-radius: 30px;

    background: #F4EDE5;

    color: var(--charcoal);

    font-size: 13px;
    font-weight: 600;
}


.success-status-dot {
    width: 8px;
    height: 8px;

    border-radius: 50%;

    background: #C08A3E;
}


/* =========================
   Information
========================= */

.success-info {
    margin-top: 28px;

    border-top: 1px solid var(--line);
    border-bottom: 1px solid var(--line);
}


.success-info-row {
    display: flex;

    justify-content: space-between;

    align-items: center;

    gap: 20px;

    padding: 16px 5px;

    border-bottom: 1px solid var(--line);
}


.success-info-row:last-child {
    border-bottom: none;
}


.success-info-row span {
    color: var(--gray);

    font-size: 14px;
}


.success-info-row strong {
    color: var(--charcoal);

    font-size: 14px;
}


/* =========================
   Notice
========================= */

.success-notice {
    margin-top: 24px;

    padding: 18px;

    border-radius: 12px;

    background: var(--ivory);

    text-align: left;
}


.success-notice strong {
    display: block;

    margin-bottom: 6px;

    color: var(--charcoal);

    font-size: 14px;
}


.success-notice p {
    margin: 0;

    color: var(--gray);

    font-size: 13px;

    line-height: 1.7;
}


/* =========================
   Buttons
========================= */

.success-actions {
    display: flex;

    gap: 12px;

    margin-top: 28px;
}


.success-primary-button,
.success-secondary-button {
    flex: 1;

    padding: 13px 18px;

    border-radius: 10px;

    font-size: 14px;
    font-weight: 700;

    text-decoration: none;

    transition: 0.2s ease;
}


.success-primary-button {
    background: var(--mauve);

    color: white;
}


.success-primary-button:hover {
    opacity: 0.9;

    transform: translateY(-1px);
}


.success-secondary-button {
    background: var(--beige);

    color: var(--charcoal);
}


.success-secondary-button:hover {
    opacity: 0.85;
}


/* =========================
   Responsive
========================= */

@media (max-width: 600px) {

    .booking-success-page {
        padding: 25px 15px;
    }

    .booking-success-card {
        padding: 30px 20px;
    }

    .booking-success-card h1 {
        font-size: 27px;
    }

    .success-actions {
        flex-direction: column;
    }

    .success-info-row {
        align-items: flex-start;
    }

}

</style>

@endsection