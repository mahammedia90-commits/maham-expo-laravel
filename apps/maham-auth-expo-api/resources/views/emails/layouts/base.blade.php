<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'Maham Expo')</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap');
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #d6d0c4; font-family: 'Cairo', 'Segoe UI', Tahoma, Arial, sans-serif; -webkit-font-smoothing: antialiased; direction: rtl;">
    @php
        $assetBaseUrl = rtrim(config('auth-service.asset_base_url', config('app.expo_api_url', 'http://localhost:8002')), '/');
    @endphp

    <!-- Outer wrapper -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #d6d0c4;">
        <tr>
            <td align="center" style="padding: 40px 16px;">

                <!-- Email card -->
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">

                    <!-- Header with background image -->
                    <tr>
                        <td style="background-image: url('{{ $assetBaseUrl }}/images/email_imgae.png'); background-size: cover; background-position: center top; background-repeat: no-repeat; padding: 40px 40px 36px; text-align: center;">

                            <!-- Logo -->
                            <img src="{{ $assetBaseUrl }}/images/maham_logo.png" alt="MAHAM EXPO" width="120" style="display: inline-block; max-width: 120px; height: auto; margin-bottom: 24px;" />

                            <!-- Title -->
                            @hasSection('header_icon')
                                <div style="font-size: 36px; margin-bottom: 8px;">
                                    @yield('header_icon')
                                </div>
                            @endif

                            <h1 style="margin: 0 0 8px; font-size: 24px; font-weight: 800; color: #1a1a1a; line-height: 1.4;">
                                @yield('header_title')
                            </h1>

                            <p style="margin: 0; font-size: 14px; color: #6b6b6b; line-height: 1.6;">
                                @yield('header_subtitle')
                            </p>
                        </td>
                    </tr>

                    <!-- Body content -->
                    <tr>
                        <td style="padding: 0 40px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="padding: 32px 0; text-align: center;">

                                        <!-- Greeting -->
                                        <h2 style="margin: 0 0 16px; font-size: 20px; font-weight: 700; color: #1a1a1a;">
                                            {{ $greeting ?? 'مرحباً' }} {{ $userName ?? '' }}
                                        </h2>

                                        <!-- Main content -->
                                        <div style="font-size: 15px; color: #555555; line-height: 1.8; text-align: center;">
                                            @yield('content')
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr>
                        <td style="padding: 0 40px;">
                            <div style="border-top: 1px solid #e8e5e0; margin: 0;"></div>
                        </td>
                    </tr>

                    <!-- Footer - Contact -->
                    <tr>
                        <td style="padding: 28px 40px 0; text-align: center;">
                            <p style="margin: 0 0 16px; font-size: 13px; color: #888888; line-height: 1.7;">
                                إذا كان لديك أي استفسار أو واجهتك أي مشكلة، لا تتردد في التواصل معنا عبر:
                                <br>
                                <a href="mailto:mahamexpo@example.com" style="color: #CCA12E; text-decoration: none; font-weight: 600;">mahamexpo@example.com</a>
                            </p>

                            <p style="margin: 0 0 24px; font-size: 13px; color: #888888; line-height: 1.7;">
                                نتمنى لك تجربة موفقة ومميزة معنا
                                <br>
                                مع أطيب التحيات،
                                <br>
                                <strong style="color: #555555;">فريق معام اكسبو</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr>
                        <td style="padding: 0 40px;">
                            <div style="border-top: 1px solid #e8e5e0; margin: 0;"></div>
                        </td>
                    </tr>

                    <!-- Footer - Social Icons -->
                    <tr>
                        <td style="padding: 24px 40px 16px; text-align: center;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center">
                                <tr>
                                    <td style="padding: 0 5px;">
                                        <a href="#" style="text-decoration: none;">
                                            <img src="{{ $assetBaseUrl }}/images/linkend.svg" alt="LinkedIn" width="28" height="28" style="display: block;" />
                                        </a>
                                    </td>
                                    <td style="padding: 0 5px;">
                                        <a href="#" style="text-decoration: none;">
                                            <img src="{{ $assetBaseUrl }}/images/facebook.svg" alt="Facebook" width="28" height="28" style="display: block;" />
                                        </a>
                                    </td>
                                    <td style="padding: 0 5px;">
                                        <a href="#" style="text-decoration: none;">
                                            <img src="{{ $assetBaseUrl }}/images/insta.svg" alt="Instagram" width="28" height="28" style="display: block;" />
                                        </a>
                                    </td>
                                    <td style="padding: 0 5px;">
                                        <a href="#" style="text-decoration: none;">
                                            <img src="{{ $assetBaseUrl }}/images/youtube.svg" alt="YouTube" width="28" height="28" style="display: block;" />
                                        </a>
                                    </td>
                                    <td style="padding: 0 5px;">
                                        <a href="#" style="text-decoration: none;">
                                            <img src="{{ $assetBaseUrl }}/images/x.svg" alt="X" width="28" height="28" style="display: block;" />
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer - Copyright -->
                    <tr>
                        <td style="padding: 0 40px 12px; text-align: center;">
                            <p style="margin: 0; font-size: 12px; color: #aaaaaa;">
                                جميع الحقوق محفوظة لشركة معام اكسبو &copy; {{ date('Y') }}
                            </p>
                        </td>
                    </tr>

                    <!-- Footer - Small Logo -->
                    <tr>
                        <td style="padding: 0 40px 32px; text-align: center;">
                            <img src="{{ $assetBaseUrl }}/images/maham_logo.png" alt="MAHAM EXPO" width="60" style="display: inline-block; max-width: 60px; height: auto; opacity: 0.7;" />
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>
</body>
</html>
