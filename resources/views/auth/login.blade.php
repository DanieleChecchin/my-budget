@extends('layouts.guest')

@section('title', 'Login - '.config('app.name', 'MyBudget'))

@section('content')
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h1 class="h4 mb-3">Accedi</h1>

            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" type="password" name="password" class="form-control" required>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Ricordami</label>
                    </div>

                    @if (Route::has('password.request'))
                        <a class="link-secondary" href="{{ route('password.request') }}">Password dimenticata?</a>
                    @endif
                </div>

                <button type="submit" class="btn btn-dark w-100">Login</button>

                @if (Route::has('register'))
                    <div class="text-center mt-3">
                        <span class="text-muted">Non hai un account?</span>
                        <a href="{{ route('register') }}">Registrati</a>
                    </div>
                @endif
            </form>
        </div>
    </div>
@endsection
