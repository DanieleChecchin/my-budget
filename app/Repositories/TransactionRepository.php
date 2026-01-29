<?php

namespace App\Repositories;

use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Carbon;

class TransactionRepository
{
    public function paginateForUser(
        ?int $userId,
        ?string $month,
        ?string $type,
        ?int $categoryId,
        ?int $subcategoryId,
        ?string $paymentMethod,
        ?int $tagId,
        int $perPage = 20
    ): LengthAwarePaginator
    {
        if (!$userId) {
            return new Paginator([], 0, $perPage, 1, [
                'path' => Paginator::resolveCurrentPath(),
            ]);
        }

        $query = Transaction::query()
            ->with(['category', 'subcategory', 'tags'])
            ->where('user_id', $userId);

        if (!empty($month)) {
            try {
                $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth()->toDateString();
                $end = Carbon::createFromFormat('Y-m', $month)->endOfMonth()->toDateString();

                $query->whereBetween('date', [$start, $end]);
            } catch (\Exception $e) {
                // Ignore invalid month format.
            }
        }

        if (!empty($type) && $type !== 'all') {
            $query->where('type', $type);
        }

        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        if (!empty($subcategoryId)) {
            $query->where('subcategory_id', $subcategoryId);
        }

        if (!empty($paymentMethod) && $paymentMethod !== 'all') {
            $query->where('payment_method', $paymentMethod);
        }

        if (!empty($tagId)) {
            $query->whereHas('tags', function ($tagQuery) use ($tagId) {
                $tagQuery->where('tags.id', $tagId);
            });
        }

        return $query
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate($perPage);
    }
}
