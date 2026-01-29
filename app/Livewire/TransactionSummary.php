<?php

namespace App\Livewire;

use App\Services\TransactionSummaryService;
use Livewire\Component;

class TransactionSummary extends Component
{
    #[\Livewire\Attributes\On('transaction-created')]
    public function refresh(): void
    {
        // render() verrà rieseguito
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
        $month = now();

        $summary = $summaryService->forUserMonth($userId, $month);
        $series = $summaryService->dailyNetSeriesForUserMonth($userId, $month);
        $sparkline = $this->buildSparkline($series);

        return view('livewire.transaction-summary', [
            'monthLabel' => $month->translatedFormat('F Y'),
            'income' => $summary['income'],
            'expense' => $summary['expense'],
            'balance' => $summary['balance'],
            'sparkline' => $sparkline,
        ]);
    }
}
