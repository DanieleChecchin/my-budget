<div class="card app-card shadow-sm">
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
                <label class="form-label">Importo (EUR)</label>
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
                <label class="form-label">Pagamento</label>
                <select class="form-select" wire:model="paymentMethod">
                    <option value="card">Carta</option>
                    <option value="cash">Contanti</option>
                </select>
                @error('paymentMethod') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label">Categoria</label>
                <select class="form-select" wire:model.live.number="categoryId">
                    <option value="">Seleziona</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('categoryId') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                @if ($selectedCategoryMeta)
                    <div class="mt-2">
                        <span class="app-category-chip" style="--chip-color: {{ $selectedCategoryMeta['color'] }}">
                            <i class="fa-solid {{ $selectedCategoryMeta['icon'] }}"></i>
                            {{ $selectedCategory?->name }}
                        </span>
                    </div>
                @endif
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label">Sottocategoria</label>
                <select class="form-select" wire:model.live.number="subcategoryId" @disabled($subcategories->isEmpty())>
                    <option value="">Seleziona</option>
                    @foreach ($subcategories as $subcategory)
                        <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                    @endforeach
                </select>
                @error('subcategoryId') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                @if ($selectedSubcategoryMeta)
                    <div class="mt-2">
                        <span class="app-subcategory-chip" style="--chip-color: {{ $selectedSubcategoryMeta['color'] }}">
                            <i class="fa-solid fa-circle"></i>
                            Sottocategoria
                        </span>
                    </div>
                @endif
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label">Tag</label>
                <select class="form-select" wire:model.live.number="tagId">
                    <option value="">Seleziona</option>
                    @foreach ($availableTags as $tag)
                        <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                    @endforeach
                </select>
                @if ($availableTags->isEmpty())
                    <div class="text-muted small mt-1">Nessun tag disponibile.</div>
                @endif
                @error('tagId') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label class="form-label">Nota (opzionale)</label>
                <input type="text" class="form-control" wire:model="note" placeholder="es. pizza">
                @error('note') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <button class="btn btn-primary" type="submit">
                    Salva
                </button>
            </div>
        </form>
    </div>
</div>
