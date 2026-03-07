@extends('emails.layouts.base')

@section('title', 'تم تأكيد الحساب بنجاح')

@section('header_icon')
    &#127881;
@endsection

@section('header_title', 'تم تأكيد الحساب بنجاح')
@section('header_subtitle', 'يمكنك الآن استخدام جميع خدمات المنصة.')

@section('content')
    <p style="margin: 0 0 16px; font-size: 15px; color: #555555; line-height: 1.8;">
        تم تأكيد حسابك بنجاح، ويمكنك الآن الاستفادة من جميع الميزات والخدمات المتاحة
        على <strong style="color: #CCA12E;">معام اكسبو</strong>.
    </p>

    <p style="margin: 0; font-size: 15px; color: #555555; line-height: 1.8;">
        ابدأ الآن في استكشاف المنصة وتنفيذ المهام التي تهمك بسهولة.
    </p>
@endsection
