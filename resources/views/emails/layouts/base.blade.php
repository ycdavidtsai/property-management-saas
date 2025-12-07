<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $subject ?? config('app.name') }}</title>
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
        /* Reset styles */
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }
        body {
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            background-color: #f4f6f9;
        }
        a {
            color: #3b82f6;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        
        /* Responsive styles */
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                max-width: 100% !important;
            }
            .content-padding {
                padding-left: 20px !important;
                padding-right: 20px !important;
            }
            .stack-column {
                display: block !important;
                width: 100% !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    
    <!-- Preview text -->
    <div style="display: none; max-height: 0; overflow: hidden;">
        {{ $previewText ?? '' }}
    </div>
    
    <!-- Hidden preheader spacer -->
    <div style="display: none; max-height: 0px; overflow: hidden;">
        &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
    </div>
    
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f6f9;">
        <tr>
            <td style="padding: 40px 20px;">
                
                <!-- Email Container -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" class="email-container" style="margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                    
                    <!-- Header -->
                    <tr>
                        {{-- <td style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); padding: 30px 40px; text-align: center;"> ** the gradient background caused issues in some email clients, so using a solid color instead --}}
                        <td style="background-color: #1e40af; padding: 30px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700; letter-spacing: -0.5px;">
                                {{ config('app.name', 'MyRentals') }}
                            </h1>
                            @if(isset($headerSubtitle))
                                <p style="margin: 8px 0 0 0; color: rgba(255,255,255,0.85); font-size: 14px;">
                                    {{ $headerSubtitle }}
                                </p>
                            @endif
                        </td>
                    </tr>
                    
                    <!-- Body Content -->
                    <tr>
                        <td class="content-padding" style="padding: 40px;">
                            {{ $slot }}
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 30px 40px; border-top: 1px solid #e5e7eb;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="text-align: center;">
                                        <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 14px;">
                                            {{ config('app.name', 'MyRentals') }} - Property Management Platform
                                        </p>
                                        <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                                        </p>
                                        @if(isset($footerLinks) && count($footerLinks) > 0)
                                            <p style="margin: 15px 0 0 0;">
                                                @foreach($footerLinks as $label => $url)
                                                    <a href="{{ $url }}" style="color: #6b7280; font-size: 12px; margin: 0 10px;">{{ $label }}</a>
                                                    @if(!$loop->last) @endif
                                                @endforeach
                                            </p>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                </table>
                
            </td>
        </tr>
    </table>
    
</body>
</html>
