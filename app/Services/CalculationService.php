<?php

namespace App\Services;

use App\Enums\ResponseTime;
use App\Enums\BottleneckResponse;

class CalculationService
{
    public function calculate(array $input): array
    {
        $data = $this->prepareInput($input);

        $metrics = $this->calculateMetrics($data);

        $score = $this->score(
            $metrics['cpl'],
            $metrics['cpa'],
            $metrics['break_even_cpl'],
            $metrics['break_even_cpa'],
            $data['response'],
            $data['has_follow_up'],
            $data['customers'],
            $metrics['net_profit']
        );

        return [
            ...$metrics,
            'score' => $score,
            'score_label' => $this->label($score),
            'bottleneck' => $this->bottleneck(
                $metrics['cpl'],
                $metrics['break_even_cpl'],
                $metrics['net_profit'],
                $data['has_follow_up'],
                $data['response']
            ),
        ];
    }
    private function prepareInput(array $input): array
    {
        return [
            'spend' => (float) $input['ad_spend'],
            'leads' => (int) $input['leads'],
            'customers' => (int) $input['customers'],
            'value' => (float) $input['customer_value'],
            'margin' => (float) $input['gross_margin'] / 100,
            'close_rate' => (float) $input['close_rate'] / 100,
            'has_follow_up' => (bool) $input['has_follow_up'],
            'response' => ResponseTime::from($input['response_time']),
        ];
    }
    private function calculateMetrics(array $data): array
    {
        $spend = $data['spend'];
        $leads = $data['leads'];
        $customers = $data['customers'];
        $value = $data['value'];

        $grossProfit = round($value * $data['margin'], 2);

        $cpl = $leads > 0
            ? round($spend / $leads, 2)
            : null;

        $cpa = $customers > 0
            ? round($spend / $customers, 2)
            : null;

        $breakEvenCpl = round($grossProfit * $data['close_rate'], 2);

        $revenue = round($customers * $value, 2);

        $netProfit = round($customers * $grossProfit - $spend, 2);

        return [
            'cpl' => $cpl,
            'cpa' => $cpa,
            'gross_profit_per_customer' => $grossProfit,
            'break_even_cpl' => $breakEvenCpl,
            'break_even_cpa' => $grossProfit,
            'revenue' => $revenue,
            'net_profit' => $netProfit,
            'roas' => $spend > 0
                ? round($revenue / $spend, 2)
                : null,
        ];
    }
    private function score(
        ?float $cpl,
        ?float $cpa,
        float $beCpl,
        float $beCpa,
        ResponseTime $response,
        bool $hasFollowUp,
        int $customers,
        float $netProfit
    ): int {
        $score = 50;

        if ($cpl !== null) {
            if ($cpl < $beCpl) {
                $score += 20;
                if ($cpl <= $beCpl * 0.8) {
                    $score += 10;
                }
            } elseif ($cpl > $beCpl) {
                $score -= 20;
            }
        }

        if ($cpa !== null) {
            if ($cpa < $beCpa) {
                $score += 20;
            } elseif ($cpa > $beCpa) {
                $score -= 20;
            }
        }

        $score += $response->score();

        if (! $hasFollowUp) {
            $score -= 15;
        }

        if ($customers === 0) {
            $score -= 25;
        }

        if ($netProfit > 0) {
            $score += 15;
        } elseif ($netProfit < 0) {
            $score -= 15;
        }

        return max(0, min(100, $score));
    }

    private function label(int $score): string
    {
        return match (true) {
            $score >= 80 => 'Healthy',
            $score >= 60 => 'Needs improvement',
            $score >= 40 => 'Risky',
            default => 'Losing money',
        };
    }

    private function bottleneck(
        ?float $cpl,
        float $beCpl,
        float $netProfit,
        bool $hasFollowUp,
        ResponseTime $response
    ): string {
        if ($cpl === null || $cpl > $beCpl) {
            return BottleneckResponse::BOTTLENECK_ADS->value;
        }

        if ($netProfit < 0) {
            return (! $hasFollowUp || $response === ResponseTime::NextDayOrLater)
                ? BottleneckResponse::BOTTLENECK_FOLLOW_UP->value
                : BottleneckResponse::BOTTLENECK_SALES->value;
        }

        return BottleneckResponse::BOTTLENECK_NONE->value;
    }
}
