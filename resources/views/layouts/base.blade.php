<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name', 'MyBudget'))</title>

    @vite(['resources/js/app.js'])
    @livewireStyles

    @stack('head')
</head>
<body class="app-body">
@yield('body')

@livewireScripts
@stack('scripts')
</body>
</html>
