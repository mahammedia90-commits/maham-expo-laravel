@extends('emails.layouts.base')

@section('title', 'اكتشف الجديد على منصة معام اكسبو')

@section('header_title', 'اكتشف الجديد على منصة معام اكسبو')
@section('header_subtitle', 'ميزات وعروض جديدة بانتظارك.')

@section('content')
    <p style="margin: 0 0 16px; font-size: 15px; color: #555555; line-height: 1.8;">
        نعمل دائمًا على تطوير خدماتنا لتقديم تجربة أفضل لك.
    </p>

    <p style="margin: 0 0 16px; font-size: 15px; color: #555555; line-height: 1.8;">
        اطّلع على أحدث التحديثات والميزات الجديدة، بالإضافة إلى العروض والفعاليات
        التي قد تهمك.
    </p>

    <p style="margin: 0 0 24px; font-size: 15px; color: #555555; line-height: 1.8;">
        لا تفوّت الفرصة واستفد من كل ما هو جديد.
    </p>

    <!-- CTA Button -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" style="margin: 0 auto;">
        <tr>
            <td style="background-color: #1a1a1a; border-radius: 25px; padding: 12px 32px;">
                <a href="{{ $ctaUrl ?? '#' }}" style="color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 700; display: inline-block;">
                    اكتشف الآن
                </a>
            </td>
        </tr>
    </table>
@endsection
