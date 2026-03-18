<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $subject ?? config('app.name') }}</title>
    <style>
        body { margin: 0; padding: 0; background: #f4f4f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color: #111827; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
        .header  { background: #1d4ed8; padding: 28px 32px; }
        .header h1 { margin: 0; color: #ffffff; font-size: 20px; font-weight: 600; }
        .body    { padding: 32px; }
        .body p  { margin: 0 0 16px; line-height: 1.6; }
        .meta    { margin-top: 24px; padding: 16px; background: #f9fafb; border-radius: 6px; font-size: 13px; color: #6b7280; }
        .meta p  { margin: 4px 0; }
        .footer  { padding: 20px 32px; font-size: 12px; color: #9ca3af; text-align: center; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        <div class="body">
            @yield('content')
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>

