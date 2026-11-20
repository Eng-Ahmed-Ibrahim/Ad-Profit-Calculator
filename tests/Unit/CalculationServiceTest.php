<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Enums\BottleneckResponse;
use App\Services\CalculationService;

class CalculationServiceTest extends TestCase
{
    private function calculate(array $overrides = []): array
    {
        $input = array_merge([
            'ad_spend' => 3000,
            'leads' => 60,
            'customers' => 6,
            'customer_value' => 2000,
            'gross_margin' => 60,
            'close_rate' => 10,
            'response_time' => 'under_5_min',
            'has_follow_up' => true,
        ], $overrides);

        return (new CalculationService())->calculate($input);
    }

    public function test_healthy_campaign_calculates_correctly(): void
    {
        $result = $this->calculate();

        $this->assertSame(50.0, $result['cpl']);
        $this->assertSame(500.0, $result['cpa']);
        $this->assertSame(120.0, $result['break_even_cpl']);
        $this->assertSame(1200.0, $result['break_even_cpa']);

        $this->assertSame(12000.0, $result['revenue']);
        $this->assertSame(4200.0, $result['net_profit']);
        $this->assertSame(4.0, $result['roas']);

        $this->assertSame(100, $result['score']);
        $this->assertSame('Healthy', $result['score_label']);

        $this->assertSame(
            BottleneckResponse::BOTTLENECK_NONE->value,
            $result['bottleneck']
        );
    }

    public function test_losing_campaign_identifies_follow_up_bottleneck(): void
    {
        $result = $this->calculate([
            'ad_spend' => 5000,
            'leads' => 50,
            'customers' => 2,
            'customer_value' => 1500,
            'gross_margin' => 50,
            'close_rate' => 20,
            'response_time' => 'next_day_or_later',
            'has_follow_up' => false,
        ]);

        $this->assertSame(100.0, $result['cpl']);
        $this->assertSame(2500.0, $result['cpa']);

        $this->assertSame(150.0, $result['break_even_cpl']);
        $this->assertSame(750.0, $result['break_even_cpa']);

        $this->assertSame(3000.0, $result['revenue']);
        $this->assertSame(-3500.0, $result['net_profit']);
        $this->assertSame(0.6, $result['roas']);

        $this->assertSame(15, $result['score']);
        $this->assertSame('Losing money', $result['score_label']);

        $this->assertSame(
            BottleneckResponse::BOTTLENECK_FOLLOW_UP->value,
            $result['bottleneck']
        );
    }

    public function test_zero_leads_and_customers_are_handled_safely(): void
    {
        $result = $this->calculate([
            'ad_spend' => 2000,
            'leads' => 0,
            'customers' => 0,
            'customer_value' => 1000,
            'gross_margin' => 40,
            'close_rate' => 10,
            'response_time' => 'same_day',
            'has_follow_up' => true,
        ]);

        $this->assertNull($result['cpl']);
        $this->assertNull($result['cpa']);

        $this->assertSame(0.0, $result['revenue']);
        $this->assertSame(-2000.0, $result['net_profit']);
        $this->assertSame(0.0, $result['roas']);

        $this->assertSame(10, $result['score']);
        $this->assertSame('Losing money', $result['score_label']);

        $this->assertSame(
            BottleneckResponse::BOTTLENECK_ADS->value,
            $result['bottleneck']
        );
    }
}
