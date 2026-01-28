@extends('layouts.base')

@section('title', 'Benvenuto - '.config('app.name', 'MyBudget'))

@section('body')
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/') }}">MyBudget</a>

            <div class="ms-auto">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm me-2">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-warning btn-sm">Registrati</a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="p-4 p-md-5 bg-white border rounded-3 shadow-sm">
                    <h1 class="display-6 fw-bold mb-3">Tieni traccia dei soldi, senza complicarti la vita.</h1>
                    <p class="lead text-muted mb-4">
                        Inserisci le spese in pochi secondi e capisci dove finiscono i tuoi soldi.
                    </p>

                    <div class="d-flex flex-wrap gap-2">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-dark">Vai alla Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-dark">Accedi</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-outline-secondary">Crea account</a>
                            @endif
                        @endauth
                    </div>
                </div>

                <p class="text-center text-muted small mt-4 mb-0">
                    © {{ now()->year }} MyBudget
                </p>
            </div>
        </div>
    </main>
@endsection
