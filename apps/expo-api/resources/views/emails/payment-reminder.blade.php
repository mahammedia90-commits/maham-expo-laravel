@extends('emails.layouts.base')

@section('title', 'تذكير بموعد الدفع')

@section('header_title', 'تذكير بموعد الدفع')
@section('header_subtitle', 'لديك دفعة مستحقة قريبًا.')

@section('content')
    <p style="margin: 0 0 20px; font-size: 15px; color: #555555; line-height: 1.8;">
        نود تذكيرك بوجود مبلغ مستحق بقيمة <strong style="color: #1a1a1a;">{{ $amount ?? '0' }} ريال</strong>، ويجب سداده قبل تاريخ
        <strong style="color: #1a1a1a;">{{ $dueDate ?? '' }}</strong>.
    </p>

    <p style="margin: 0 0 20px; font-size: 15px; color: #555555; line-height: 1.8;">
        يرجى إتمام عملية الدفع في الوقت المحدد لتجنب أي تأخير أو إلغاء الإيجار.
    </p>

    <p style="margin: 0; font-size: 15px; color: #555555; line-height: 1.8;">
        يمكنك الدفع بسهولة من خلال حسابك على المنصة.
    </p>
@endsection
