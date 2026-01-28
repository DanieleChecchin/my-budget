<div class="card shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 mb-0">Aggiungi transazione</h2>

            @if (session('status'))
                <span class="badge text-bg-success">{{ session('status') }}</span>
            @endif
        </div>

        <form wire:submit.prevent="save" class="row g-3">
            <div class="col-12 col-md-3">
                <label class="form-label">Tipo</label>
                <select class="form-select" wire:model="type">
                    <option value="expense">Uscita</option>
                    <option value="income">Entrata</option>
                </select>
                @error('type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label">Importo (€)</label>
                <input
                        type="number"
                        step="0.01"
                        min="0.01"
                        class="form-control"
                        wire:model="amount"
                        placeholder="12.50"
                >
                @error('amount') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label">Data</label>
                <input type="date" class="form-control" wire:model="date">
                @error('date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label">Nota (opzionale)</label>
                <input type="text" class="form-control" wire:model="note" placeholder="es. pizza">
                @error('note') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <button class="btn btn-dark" type="submit">
                    Salva
                </button>
            </div>
        </form>
    </div>
</div>
