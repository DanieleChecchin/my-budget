<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Tag;
use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionList extends Component
{
    use WithPagination;

    public string $month;
    public string $type = 'all';
    public string $paymentMethod = 'all';
    public ?int $categoryId = null;
    public ?int $subcategoryId = null;
    public ?int $tagId = null;
    public ?int $deleteId = null;

    protected string $paginationTheme = 'bootstrap';
    protected bool $suppressMonthSync = false;

    public function mount(): void
    {
        if (empty($this->month)) {
            $this->month = now()->format('Y-m');
        }
    }

    private function categoryMeta(): array
    {
        return [
            'Casa' => ['color' => '#38bdf8', 'icon' => 'fa-house'],
            'Lavoro' => ['color' => '#22c55e', 'icon' => 'fa-briefcase'],
            'Spese personali' => ['color' => '#f97316', 'icon' => 'fa-user'],
            'Rimborsi' => ['color' => '#a855f7', 'icon' => 'fa-rotate-left'],
        ];
    }

    public function updatedCategoryId(): void
    {
        $this->subcategoryId = null;
        $this->resetPage();
    }

    public function updatedSubcategoryId(): void
    {
        $this->resetPage();
    }

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function updatedMonth(): void
    {
        if ($this->suppressMonthSync) {
            return;
        }

        $this->resetPage();
    }

    public function updatedPaymentMethod(): void
    {
        $this->resetPage();
    }

    public function updatedTagId(): void
    {
        $this->resetPage();
    }

    #[On('transaction-created')]
    public function refresh(): void
    {
        // render() verra rieseguito mantenendo filtri/pagina attuali
    }

    // Month is provided by the parent dashboard component.

    public function confirmDelete(int $transactionId): void
    {
        $this->deleteId = $transactionId;
    }

    public function deleteSelected(): void
    {
        if ($this->deleteId === null) {
            return;
        }

        Transaction::query()
            ->where('id', $this->deleteId)
            ->where('user_id', auth()->id())
            ->delete();

        $this->deleteId = null;
        $this->resetPage();
        $this->dispatch('close-delete-modal');
        session()->flash('status', 'Transazione eliminata');
    }

    public function render(TransactionRepository $repository)
    {
        $transactions = $repository->paginateForUser(
            auth()->id(),
            $this->month,
            $this->type,
            $this->categoryId,
            $this->subcategoryId,
            $this->paymentMethod,
            $this->tagId,
            20
        );

        $categories = Category::query()
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        $subcategories = $this->categoryId
            ? Category::query()->where('parent_id', $this->categoryId)->orderBy('name')->get()
            : collect();

        $tags = Tag::query()->orderBy('name')->get();

        $meta = $this->categoryMeta();
        $categoriesForMap = Category::query()->with('parent')->get();
        $categoryMetaById = [];

        foreach ($categoriesForMap as $category) {
            $baseName = $category->parent?->name ?? $category->name;
            if (isset($meta[$baseName])) {
                $categoryMetaById[$category->id] = $meta[$baseName];
            }
        }

        return view('livewire.transaction-list', compact(
            'transactions',
            'categories',
            'subcategories',
            'tags',
            'categoryMetaById'
        ));
    }
}
