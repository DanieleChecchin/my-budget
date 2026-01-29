@extends('layouts.app')

@section('content')
    <div class="app-dashboard-container">
        <div class="row g-4 app-dashboard">
        <div class="col-12">
            <livewire:transaction-summary />
        </div>

        <div class="col-12 col-lg-5">
            <livewire:transaction-form />
        </div>

        <div class="col-12 col-lg-7">
            <livewire:transaction-list />
        </div>
        </div>
    </div>
@endsection
