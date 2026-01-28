<?php

namespace App\Livewire;

use App\Models\Transaction;
use Livewire\Attributes\On;
use Livewire\Component;

class TransactionList extends Component
{
    #[On('transaction-created')]
    public function refresh(): void
    {
        // basta ascoltare l'evento: render() verrà rieseguito
    }

    public function render()
    {
        $transactions = Transaction::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        return view('livewire.transaction-list', compact('transactions'));
    }
}
