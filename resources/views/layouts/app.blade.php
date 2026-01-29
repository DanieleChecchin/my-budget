@extends('layouts.base')

@section('body')
    @include('layouts.header')

    <main class="container app-container py-4">
        @yield('content')
    </main>
@endsection
