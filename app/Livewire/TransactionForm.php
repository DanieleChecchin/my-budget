<?php

namespace App\Livewire;

use App\Livewire\TransactionList;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Transaction;
use Livewire\Component;

class TransactionForm extends Component
{
    public string $type = 'expense';
    public string $amount = '';
    public string $date;
    public ?string $note = null;
    public string $paymentMethod = 'card';
    public ?int $categoryId = null;
    public ?int $subcategoryId = null;
    public ?int $tagId = null;

    public function mount(): void
    {
        $this->date = now()->toDateString();
    }

    public function updatedCategoryId(): void
    {
        $this->subcategoryId = null;
    }

    protected function rules(): array
    {
        return [
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:255'],
            'paymentMethod' => ['required', 'in:card,cash'],
            'categoryId' => ['required', 'integer', 'exists:categories,id'],
            'subcategoryId' => ['nullable', 'integer', 'exists:categories,id'],
            'tagId' => ['nullable', 'integer', 'exists:tags,id'],
        ];
    }

    protected array $validationAttributes = [
        'type' => 'tipo',
        'amount' => 'importo',
        'date' => 'data',
        'note' => 'nota',
        'paymentMethod' => 'metodo di pagamento',
        'categoryId' => 'categoria',
        'subcategoryId' => 'sottocategoria',
        'tagId' => 'tag',
    ];

    private function categoryMeta(): array
    {
        return [
            'Casa' => ['color' => '#38bdf8', 'icon' => 'fa-house'],
            'Lavoro' => ['color' => '#22c55e', 'icon' => 'fa-briefcase'],
            'Spese personali' => ['color' => '#f97316', 'icon' => 'fa-user'],
            'Rimborsi' => ['color' => '#a855f7', 'icon' => 'fa-rotate-left'],
        ];
    }

    private function ensureLookups(): void
    {
        if (Category::query()->count() === 0) {
            $categories = [
                'Casa' => ['Affitto', 'Bollette', 'Spese condominio'],
                'Lavoro' => ['Stipendio', 'Freelance'],
                'Spese personali' => ['Cibo', 'Trasporti', 'Salute'],
                'Rimborsi' => ['Rimborsi'],
            ];

            foreach ($categories as $parentName => $children) {
                $parent = Category::firstOrCreate([
                    'name' => $parentName,
                    'parent_id' => null,
                ]);

                foreach ($children as $childName) {
                    Category::firstOrCreate([
                        'name' => $childName,
                        'parent_id' => $parent->id,
                    ]);
                }
            }
        }

        if (Tag::query()->count() === 0) {
            $tags = [
                'casa',
                'lavoro',
                'rimborsi',
            ];

            foreach ($tags as $tagName) {
                Tag::firstOrCreate(['name' => $tagName]);
            }
        }
    }

    public function save(): void
    {
        $validated = $this->validate();

        $categoryOk = Category::query()
            ->where('id', $validated['categoryId'])
            ->whereNull('parent_id')
            ->exists();

        if (!$categoryOk) {
            $this->addError('categoryId', 'Seleziona una categoria valida.');
            return;
        }

        if (!empty($validated['subcategoryId'])) {
            $subOk = Category::query()
                ->where('id', $validated['subcategoryId'])
                ->where('parent_id', $validated['categoryId'])
                ->exists();

            if (!$subOk) {
                $this->addError('subcategoryId', 'La sottocategoria non appartiene alla categoria selezionata.');
                return;
            }
        }

        $transaction = Transaction::create([
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'note' => $validated['note'],
            'payment_method' => $validated['paymentMethod'],
            'category_id' => $validated['categoryId'],
            'subcategory_id' => $validated['subcategoryId'] ?? null,
        ]);

        $transaction->tags()->sync($validated['tagId'] ? [$validated['tagId']] : []);

        // reset campi
        $this->reset(['amount', 'note', 'tagId']);
        $this->type = 'expense';
        $this->date = now()->toDateString();

        // refresh lista e riepilogo
        $this->dispatch('transaction-created')->to(TransactionList::class);

        session()->flash('status', 'Transazione salvata');
    }

    public function render()
    {
        $this->ensureLookups();

        $categories = Category::query()
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        $subcategories = $this->categoryId
            ? Category::query()->where('parent_id', $this->categoryId)->orderBy('name')->get()
            : collect();

        $availableTags = Tag::query()->orderBy('name')->get();

        $meta = $this->categoryMeta();
        $selectedCategory = $categories->firstWhere('id', $this->categoryId);
        $selectedCategoryMeta = $selectedCategory ? ($meta[$selectedCategory->name] ?? null) : null;

        $selectedSubcategoryMeta = null;
        if ($this->subcategoryId) {
            $sub = Category::query()->with('parent')->find($this->subcategoryId);
            $parentName = $sub?->parent?->name;
            $selectedSubcategoryMeta = $parentName ? ($meta[$parentName] ?? null) : null;
        }

        return view('livewire.transaction-form', compact(
            'categories',
            'subcategories',
            'availableTags',
            'selectedCategory',
            'selectedCategoryMeta',
            'selectedSubcategoryMeta'
        ));
    }

}
