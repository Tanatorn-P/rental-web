{{--
    Layout ฝั่งลูกค้าสำหรับหน้า dress (find / category / product / availability / booking)
    ใช้ layout และ CSS ส่วนกลาง (layouts.app + css/style.css)
    แล้วโหลด css/customer.css เพิ่มเฉพาะสไตล์ของหน้าเลือกชุด
--}}
@extends('layouts.app')

@section('page-name', 'Find a Dress')

@push('styles')
    <link href="{{ asset('css/customer.css') }}" rel="stylesheet" />
@endpush