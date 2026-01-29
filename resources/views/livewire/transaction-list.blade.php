<div>
    <div class="card app-card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 mb-0">Ultime transazioni</h2>

                @if (session('status'))
                    <span class="badge text-bg-success">{{ session('status') }}</span>
                @endif
            </div>

            <div class="row g-2 align-items-end mb-3">
                <div class="col-12 col-md-2">
                    <label class="form-label">Mese</label>
                    <input type="month" class="form-control" wire:model="month">
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label">Tipo</label>
                    <select class="form-select" wire:model="type">
                        <option value="all">Tutti</option>
                        <option value="expense">Uscita</option>
                        <option value="income">Entrata</option>
                    </select>
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label">Pagamento</label>
                    <select class="form-select" wire:model="paymentMethod">
                        <option value="all">Tutti</option>
                        <option value="card">Carta</option>
                        <option value="cash">Contanti</option>
                    </select>
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label">Categoria</label>
                    <select class="form-select" wire:model.live.number="categoryId">
                        <option value="">Tutte</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label">Sottocategoria</label>
                    <select class="form-select" wire:model.live.number="subcategoryId" @disabled($subcategories->isEmpty())>
                        <option value="">Tutte</option>
                        @foreach ($subcategories as $subcategory)
                            <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label">Tag</label>
                    <select class="form-select" wire:model.live.number="tagId">
                        <option value="">Tutti</option>
                        @foreach ($tags as $tag)
                            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if ($transactions->isEmpty())
                <p class="text-muted mb-0">Nessuna transazione ancora. Aggiungine una sopra.</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Data</th>
                            <th>Categoria</th>
                            <th>Tipo</th>
                            <th>Pagamento</th>
                            <th class="text-end">Importo</th>
                            <th>Tag</th>
                            <th>Nota</th>
                            <th class="text-end">Azioni</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($transactions as $t)
                            <tr
                                wire:key="tx-{{ $t->id }}"
                                wire:transition.enter="transaction-row"
                                wire:transition.enter-start="transaction-row-enter-start"
                                wire:transition.enter-end="transaction-row-enter-end"
                                wire:transition.leave="transaction-row"
                                wire:transition.leave-start="transaction-row-leave-start"
                                wire:transition.leave-end="transaction-row-leave-end"
                            >
                                <td>{{ $t->date->format('d/m/Y') }}</td>
                                <td>
                                    <div class="fw-semibold d-flex align-items-center gap-2">
                                        @if ($t->category && isset($categoryMetaById[$t->category->id]))
                                            @php($meta = $categoryMetaById[$t->category->id])
                                            <span class="app-category-dot" style="--chip-color: {{ $meta['color'] }}">
                                                <i class="fa-solid {{ $meta['icon'] }}"></i>
                                            </span>
                                        @endif
                                        {{ $t->category?->name ?? '-' }}
                                    </div>
                                    @if ($t->subcategory)
                                        <div class="text-muted small d-flex align-items-center gap-2">
                                            @if (isset($categoryMetaById[$t->subcategory->id]))
                                                @php($subMeta = $categoryMetaById[$t->subcategory->id])
                                                <span class="app-category-dot" style="--chip-color: {{ $subMeta['color'] }}">
                                                    <i class="fa-solid fa-circle"></i>
                                                </span>
                                            @endif
                                            {{ $t->subcategory->name }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if ($t->type === 'expense')
                                        <span class="badge text-bg-danger">Uscita</span>
                                    @else
                                        <span class="badge text-bg-success">Entrata</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($t->payment_method === 'card')
                                        <span class="badge text-bg-primary">Carta</span>
                                    @else
                                        <span class="badge text-bg-secondary">Contanti</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    {{ $t->type === 'expense' ? '-' : '+' }}
                                    EUR {{ number_format((float) $t->amount, 2, ',', '.') }}
                                </td>
                                <td>
                                    @if ($t->tags->isNotEmpty())
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach ($t->tags as $tag)
                                                <span class="badge rounded-pill text-bg-light border">{{ $tag->name }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $t->note }}</td>
                                <td class="text-end">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        wire:click="confirmDelete({{ $t->id }})"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteTransactionModal"
                                        aria-label="Elimina transazione"
                                    >
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>

    <div
        class="modal fade"
        id="deleteTransactionModal"
        tabindex="-1"
        aria-labelledby="deleteTransactionModalLabel"
        aria-hidden="true"
        wire:ignore.self
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteTransactionModalLabel">Conferma eliminazione</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
                </div>
                <div class="modal-body">
                    Sei sicuro di voler eliminare questa transazione?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteSelected">Elimina</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                const modalEl = document.getElementById('deleteTransactionModal');
                if (!modalEl) {
                    return;
                }

                const modal = new bootstrap.Modal(modalEl);

                Livewire.on('close-delete-modal', () => {
                    modal.hide();
                });
            });
        </script>
    @endpush
</div>
