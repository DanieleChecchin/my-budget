@extends('layouts.app')

@section('content')
    <div class="row g-3">
        <div class="col-12">
            <livewire:transaction-form />
        </div>

        <div class="col-12">
            <livewire:transaction-list />
        </div>
    </div>
@endsection
