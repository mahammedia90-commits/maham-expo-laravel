@extends('emails.layouts.base')

@section('title', 'فعاليات جديدة بالتذكارات')

@section('header_title', 'فعاليات جديدة بالتذكارات')
@section('header_subtitle', 'فعاليات ومعارض جديدة متاحة الآن على المنصة.')

@section('content')
    <p style="margin: 0 0 16px; font-size: 15px; color: #555555; line-height: 1.8;">
        تمت إضافة فعاليات ومعارض جديدة على منصة <strong style="color: #CCA12E;">معام اكسبو</strong>.
    </p>

    <p style="margin: 0 0 16px; font-size: 15px; color: #555555; line-height: 1.8;">
        اطّلع على أحدث الفعاليات والمعارض المتاحة واحجز مساحتك قبل نفاد الأماكن.
    </p>

    <p style="margin: 0 0 24px; font-size: 15px; color: #555555; line-height: 1.8;">
        لا تفوّت الفرصة وابدأ بالتسجيل الآن.
    </p>

    <!-- CTA Button -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" style="margin: 0 auto;">
        <tr>
            <td style="background-color: #1a1a1a; border-radius: 25px; padding: 12px 32px;">
                <a href="{{ $ctaUrl ?? '#' }}" style="color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 700; display: inline-block;">
                    استعرض الفعاليات
                </a>
            </td>
        </tr>
    </table>
@endsection
