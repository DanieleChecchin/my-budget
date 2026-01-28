<div class="card shadow-sm">
    <div class="card-body">
        <h2 class="h5 mb-3">Ultime transazioni</h2>

        @if ($transactions->isEmpty())
            <p class="text-muted mb-0">Nessuna transazione ancora. Aggiungine una sopra 👆</p>
        @else
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Data</th>
                        <th>Tipo</th>
                        <th class="text-end">Importo</th>
                        <th>Nota</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($transactions as $t)
                        <tr>
                            <td>{{ $t->date->format('d/m/Y') }}</td>
                            <td>
                                @if ($t->type === 'expense')
                                    <span class="badge text-bg-danger">Uscita</span>
                                @else
                                    <span class="badge text-bg-success">Entrata</span>
                                @endif
                            </td>
                            <td class="text-end">
                                {{ $t->type === 'expense' ? '-' : '+' }}
                                € {{ number_format((float) $t->amount, 2, ',', '.') }}
                            </td>
                            <td class="text-muted">{{ $t->note }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
