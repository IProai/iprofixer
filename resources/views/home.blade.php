<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>{{ config('app.name', 'IProFixer') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main>
        <h1>{{ app()->getLocale() === 'ar' ? 'آي برو فيكسر' : 'IProFixer' }}</h1>
        <p>{{ app()->getLocale() === 'ar' ? 'العناية المتخصصة بأصول الضيافة.' : 'Hospitality Asset Care Specialists.' }}</p>
    </main>
</body>
</html>
