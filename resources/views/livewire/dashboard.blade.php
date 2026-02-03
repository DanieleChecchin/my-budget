<div class="app-dashboard-container">
    <div class="row g-4 app-dashboard">
        <div class="col-12">
            <div class="row g-3">
                <div class="col-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <div class="mb-2 d-flex flex-wrap gap-2">
                                <div>
                                    <label class="form-label small text-muted mb-1">Mese</label>
                                    <select class="form-select text-uppercase" wire:model.live="month">
                                        @foreach ($months as $item)
                                            <option value="{{ $item['value'] }}">
                                                {{ $item['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="form-label small text-muted mb-1">Anno</label>
                                    <select class="form-select" wire:model.live="year">
                                        @foreach ($years as $item)
                                            <option value="{{ $item['value'] }}">
                                                {{ $item['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <h2 class="h5 mb-1">Riepilogo - {{ $monthLabel }}</h2>
                            <div class="text-muted small">Andamento mese corrente</div>
                        </div>
                        <div class="app-sparkline">
                            <svg
                                width="{{ $sparkline['width'] }}"
                                height="{{ $sparkline['height'] }}"
                                viewBox="0 0 {{ $sparkline['width'] }} {{ $sparkline['height'] }}"
                                aria-hidden="true"
                            >
                                <defs>
                                    <linearGradient id="sparklineGradient" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#0ea5a4" stop-opacity="0.35" />
                                        <stop offset="100%" stop-color="#0ea5a4" stop-opacity="0" />
                                    </linearGradient>
                                </defs>
                                <polygon points="{{ $sparkline['fill'] }}" fill="url(#sparklineGradient)"></polygon>
                                <polyline
                                    points="{{ $sparkline['points'] }}"
                                    fill="none"
                                    stroke="#0ea5a4"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                ></polyline>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="card app-card app-summary-card shadow-sm metric-card">
                        <div class="card-body">
                            <div class="text-muted">Entrate</div>
                            <div class="h4 mb-0 metric-value">EUR {{ number_format($income, 2, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="card app-card app-summary-card shadow-sm metric-card">
                        <div class="card-body">
                            <div class="text-muted">Uscite</div>
                            <div class="h4 mb-0 metric-value">EUR {{ number_format($expense, 2, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="card app-card app-summary-card shadow-sm metric-card">
                        <div class="card-body">
                            <div class="text-muted">Saldo</div>
                            <div class="h4 mb-0 metric-value">EUR {{ number_format($balance, 2, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <livewire:transaction-form wire:key="transaction-form" />
        </div>

        <div class="col-12 col-lg-7">
            <livewire:transaction-list :month="$month" wire:key="transaction-list-{{ $month }}" />
        </div>
    </div>
</div>
