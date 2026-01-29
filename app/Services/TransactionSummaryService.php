<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Carbon;

class TransactionSummaryService
{
    public function forUserMonth(int $userId, Carbon $month): array
    {
        $start = $month->copy()->startOfMonth()->toDateString();
        $end = $month->copy()->endOfMonth()->toDateString();

        $income = Transaction::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$start, $end])
            ->where('type', 'income')
            ->sum('amount');

        $expense = Transaction::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$start, $end])
            ->where('type', 'expense')
            ->sum('amount');

        return [
            'income' => (float) $income,
            'expense' => (float) $expense,
            'balance' => (float) $income - (float) $expense,
        ];
    }

    public function dailyNetSeriesForUserMonth(int $userId, Carbon $month): array
    {
        $start = $month->copy()->startOfMonth()->toDateString();
        $end = $month->copy()->endOfMonth()->toDateString();

        $rows = Transaction::query()
            ->selectRaw('date, type, sum(amount) as total')
            ->where('user_id', $userId)
            ->whereBetween('date', [$start, $end])
            ->groupBy('date', 'type')
            ->get();

        $byDate = [];
        foreach ($rows as $row) {
            $dateKey = $row->date instanceof Carbon ? $row->date->toDateString() : (string) $row->date;
            $value = (float) $row->total;
            $byDate[$dateKey] = ($byDate[$dateKey] ?? 0.0) + ($row->type === 'income' ? $value : -$value);
        }

        $series = [];
        $running = 0.0;
        $cursor = $month->copy()->startOfMonth();
        $lastDay = $month->copy()->endOfMonth();

        while ($cursor->lte($lastDay)) {
            $dateKey = $cursor->toDateString();
            $running += $byDate[$dateKey] ?? 0.0;
            $series[] = $running;
            $cursor->addDay();
        }

        return $series;
    }
}
