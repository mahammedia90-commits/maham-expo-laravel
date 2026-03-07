@extends('emails.layouts.base')

@section('title', 'إعادة تعيين كلمة المرور')

@section('header_title', 'إعادة تعيين كلمة المرور')
@section('header_subtitle', 'أدخل رمز التحقق لتعيين كلمة مرور جديدة.')

@section('content')
    <p style="margin: 0 0 20px; font-size: 15px; color: #555555; line-height: 1.8;">
        تلقينا طلبًا لإعادة تعيين كلمة المرور الخاصة بحسابك. لاستكمال العملية، يرجى إدخال رمز التحقق التالي:
    </p>

    <!-- OTP Code -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" style="margin: 0 auto 20px;">
        <tr>
            @foreach(str_split($code) as $digit)
                <td style="padding: 0 6px;">
                    <div style="width: 44px; height: 52px; background-color: #f7f5f1; border: 2px solid #e8e5e0; border-radius: 10px; font-size: 24px; font-weight: 800; color: #1a1a1a; line-height: 52px; text-align: center;">
                        {{ $digit }}
                    </div>
                </td>
            @endforeach
        </tr>
    </table>

    <p style="margin: 0 0 8px; font-size: 13px; color: #999999; line-height: 1.6;">
        هذا الرمز صالح لمدة <strong>4 دقائق</strong> فقط.
    </p>

    <p style="margin: 0 0 16px; font-size: 13px; color: #999999; line-height: 1.6;">
        يرجى عدم مشاركة هذا الرمز مع أي شخص للحفاظ على أمان حسابك.
    </p>

    <p style="margin: 0; font-size: 13px; color: #999999; line-height: 1.6;">
        إذا لم تطلب إعادة تعيين كلمة المرور، يمكنك تجاهل هذا البريد بأمان.
    </p>
@endsection
