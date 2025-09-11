<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Reset Password • Topang</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* Enhanced styling for better visual appeal */
        body {
            background: #ffffff;
            margin: 0;
            padding: 32px 16px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            color: #000000;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
        }

        .card {
            background: #ffffff;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border: 1px solid #000000;
        }

        .header {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo {
            display: inline-block;
            background: #000000;
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 16px;
        }

        h1 {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 16px;
            color: #000000;
            text-align: center;
        }

        .content {
            text-align: center;
            margin-bottom: 32px;
        }

        p {
            margin: 0 0 16px;
            font-size: 16px;
            color: #333333;
        }

        .btn-container {
            text-align: center;
            margin: 32px 0;
        }

        .btn {
            display: inline-block;
            background: #000000;
            color: #ffffff !important;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.2s ease;
            box-shadow: 0 4px 14px 0 rgba(0, 0, 0, 0.2);
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .security-notice {
            background: #f5f5f5;
            border: 1px solid #000000;
            border-radius: 12px;
            padding: 20px;
            margin: 24px 0;
        }

        .muted {
            color: #666666;
            font-size: 14px;
            line-height: 1.6;
        }

        .divider {
            border: none;
            border-top: 1px solid #000000;
            margin: 32px 0;
        }

        .footer {
            text-align: center;
            padding-top: 20px;
        }

        .url-fallback {
            background: #f5f5f5;
            border: 1px solid #000000;
            border-radius: 8px;
            padding: 16px;
            margin-top: 16px;
            word-break: break-all;
        }

        /* Responsive design */
        @media (max-width: 600px) {
            body {
                padding: 20px 16px;
            }

            .card {
                padding: 24px;
                margin: 16px;
            }

            h1 {
                font-size: 24px;
            }

            .btn {
                padding: 14px 28px;
                font-size: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>Reset Password Topang</h1>
            </div>

            <h1>Halo, {{ $user->name ?? 'Sobat Topang' }} 👋</h1>

            <div class="content">
                <p>Kami menerima permintaan reset password untuk akunmu. Klik tombol di bawah untuk membuat password
                    baru.</p>
            </div>

            <div class="btn-container">
                <a class="btn" href="{{ $url }}">Reset Password Sekarang</a>
            </div>

            <div class="security-notice">
                <p class="muted" style="margin-bottom: 8px;"><strong>⏰ Penting:</strong></p>
                <p class="muted" style="margin-bottom: 8px;">• Tautan reset berlaku selama 60 menit</p>
                <p class="muted">• Jika kamu tidak meminta reset password, abaikan email ini ya</p>
            </div>

            <hr class="divider">

            <div class="footer">
                <p class="muted"><strong>Tombol tidak berfungsi?</strong></p>
                <p class="muted">Salin dan tempel URL berikut ke browser Anda:</p>
                <div class="url-fallback">
                    <a href="{{ $url }}" style="color: #000000; text-decoration: none;">{{ $url }}</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
