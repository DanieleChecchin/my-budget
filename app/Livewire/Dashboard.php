<?php

namespace App\Livewire;

use App\Services\TransactionSummaryService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Component;

class Dashboard extends Component
{
    public string $month;
    public int $year;
    protected bool $suppressMonthDispatch = false;

    public function mount(): void
    {
        $now = now();
        $this->month = $now->format('Y-m');
        $this->year = (int) $now->format('Y');
    }

    private function resolveMonth(string $value): Carbon
    {
        try {
            return Carbon::createFromFormat('Y-m', $value)->startOfMonth();
        } catch (\Throwable $e) {
            return now()->startOfMonth();
        }
    }

    public function updatedMonth(): void
    {
        if ($this->suppressMonthDispatch) {
            return;
        }

        $this->year = (int) $this->resolveMonth($this->month)->year;
    }

    public function updatedYear(): void
    {
        $currentMonth = $this->resolveMonth($this->month)->month;

        $this->suppressMonthDispatch = true;
        $this->month = Carbon::create($this->year, $currentMonth, 1)->format('Y-m');
        $this->suppressMonthDispatch = false;
    }

    private function buildSparkline(array $series): array
    {
        $width = 140;
        $height = 44;

        if (count($series) === 0) {
            $series = [0, 0];
        } elseif (count($series) === 1) {
            $series[] = $series[0];
        }

        $min = min($series);
        $max = max($series);
        $range = ($max - $min) ?: 1.0;

        $points = [];
        $count = count($series);
        foreach ($series as $i => $value) {
            $x = ($count === 1) ? 0 : ($i / ($count - 1)) * $width;
            $y = $height - (($value - $min) / $range) * $height;
            $points[] = [$x, $y];
        }

        $pointString = implode(' ', array_map(
            fn ($p) => sprintf('%.1f,%.1f', $p[0], $p[1]),
            $points
        ));

        $fillString = $pointString . sprintf(' %.1f,%.1f 0,%.1f', $width, $height, $height);

        return [
            'points' => $pointString,
            'fill' => $fillString,
            'width' => $width,
            'height' => $height,
        ];
    }

    public function render(TransactionSummaryService $summaryService)
    {
        $userId = auth()->id();
        $month = $this->resolveMonth($this->month);
        $year = $this->year;

        $months = collect(range(1, 12))
            ->map(function (int $monthNumber) use ($year) {
                $monthDate = Carbon::create($year, $monthNumber, 1)->startOfMonth();

                return [
                    'value' => $monthDate->format('Y-m'),
                    'label' => Str::upper($monthDate->locale('it')->translatedFormat('F')),
                ];
            })
            ->all();

        $years = collect(range(now()->year - 5, now()->year + 5))
            ->map(fn (int $value) => ['value' => $value, 'label' => (string) $value])
            ->all();

        $summary = $summaryService->forUserMonth($userId, $month);
        $series = $summaryService->dailyNetSeriesForUserMonth($userId, $month);
        $sparkline = $this->buildSparkline($series);

        return view('livewire.dashboard', [
            'monthLabel' => Str::upper($month->locale('it')->translatedFormat('F Y')),
            'monthName' => Str::upper($month->locale('it')->translatedFormat('F')),
            'year' => $year,
            'months' => $months,
            'years' => $years,
            'income' => $summary['income'],
            'expense' => $summary['expense'],
            'balance' => $summary['balance'],
            'sparkline' => $sparkline,
        ]);
    }
}
