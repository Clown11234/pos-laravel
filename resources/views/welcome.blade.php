<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>POS System</title>
</head>
<body>
<div>
    <!-- Language Switch Links -->
    <a href="{{ route('lang.switch', 'en') }}">🇬🇧 English</a> |
    <a href="{{ route('lang.switch', 'mm') }}">🇲🇲 မြန်မာ</a>
</div>

<hr>

<h1>{{ __('messages.welcome') }}</h1>
<p>{{ __('messages.success_save') }}</p>
</body>
</html>
