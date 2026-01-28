<?php

namespace App\Livewire;

use App\Models\Transaction;
use Livewire\Component;

class TransactionForm extends Component
{
    public string $type = 'expense';
    public string $amount = '';
    public string $date;
    public ?string $note = null;

    public function mount(): void
    {
        $this->date = now()->toDateString();
    }

    protected function rules(): array
    {
        return [
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected array $validationAttributes = [
        'type' => 'tipo',
        'amount' => 'importo',
        'date' => 'data',
        'note' => 'nota',
    ];

    public function save(): void
    {
        $validated = $this->validate();

        Transaction::create([
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'note' => $validated['note'],
        ]);

        // reset campi
        $this->reset(['amount', 'note']);
        $this->type = 'expense';
        $this->date = now()->toDateString();

        // refresh lista
        $this->dispatch('transaction-created');

        session()->flash('status', 'Transazione salvata ✅');
    }

    public function render()
    {
        return view('livewire.transaction-form');
    }
}
