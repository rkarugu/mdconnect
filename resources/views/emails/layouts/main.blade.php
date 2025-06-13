<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>@yield('title', 'MediConnect Email')</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        .logo {
            max-width: 180px;
            height: auto;
            margin-bottom: 10px;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #777;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin: 15px 0;
            font-weight: bold;
        }
        .button:hover {
            background-color: #2563eb;
        }
        .text-center {
            text-align: center;
        }
        .text-primary {
            color: #3b82f6;
        }
        .text-success {
            color: #10b981;
        }
        .text-danger {
            color: #ef4444;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('images/mediconnect_logo.svg') }}" alt="MediConnect Logo" class="logo">
        <h2 style="margin-top: 0; color: #3b82f6;">@yield('header', 'MediConnect')</h2>
    </div>
    
    <div class="content">
        @yield('content')
    </div>
    
    <div class="footer">
        <p>&copy; {{ date('Y') }} MediConnect. All rights reserved.</p>
        <p style="margin-top: 5px; font-weight: bold; color: #3b82f6;">CARE IS JUST A TAP AWAY!</p>
        <p style="margin-top: 10px; font-size: 11px;">
            If you have any questions, please contact us at <a href="mailto:support@uptownnvintage.com">support@uptownnvintage.com</a>
        </p>
    </div>
</body>
</html>
