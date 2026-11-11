<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Laravel') }}</title>
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased bg-gray-100">
  <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
    {{-- Card del formulario --}}
    <div class="w-full sm:max-w-md mt-6 card">
      {{ $slot }}
    </div>

    {{-- Card social (no cambia funcionalidad) --}}
    <div class="w-full sm:max-w-md mt-6 card">
      <div class="grid gap-3">
        <a href="{{ route('google.redirect') }}" class="btn-social w-full"></a>
        <a href="{{ route('github.redirect') }}" class="btn-social w-full"></a>
      </div>
    </div>
  </div>
</body>
</html>
