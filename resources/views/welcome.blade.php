<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Padel Montepinar</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <!-- Styles -->
    <!-- normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.css  -->
    <link rel="stylesheet" href="css/styles.css">

    <style>
        body {
            font-family: 'Roboto Thin', sans-serif;
        }
    </style>
</head>
<body class="antialiased">
<div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">
    @if (Route::has('login'))
    <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
        @auth
        <a href="{{ url('/dashboard') }}" class="text-sm text-gray-700 underline">Mis reservas</a>
        @else
        <a href="{{ route('login') }}" class="text-sm text-gray-700 underline">Entrar</a>
        @if (Route::has('register'))
        <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 underline">Registrar</a>
        @endif
        @endauth
    </div>
    @endif

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-4">

        <div class="flex justify-center text-primary">
            <h1>Reserva de pista de urbanización Montepinar</h1>
        </div>
        <div class="flex justify-center text-success">
            <h3>Reserva tu pista</h3>
        </div>
        <div class="flex justify-center text-secondary">
            <h4>info@padelmontepinar.rnarvaiza.me</h4>
        </div>
        <div class="flex justify-center">
            <div class="ml-4 text-center text-sm text-gray-500">
                Regístrate y empieza a jugar!
            </div>
        </div>
    </div>
</div>
</body>
</html>