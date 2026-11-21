@extends('layout.app')

@section('content')
@php
    $score = $response['score'];

    $theme = match ($response['score_label']) {
        'Healthy'           => ['text' => 'text-green-700',  'bg' => 'bg-green-50',  'bar' => 'bg-green-500',  'ring' => 'ring-green-200'],
        'Needs improvement' => ['text' => 'text-yellow-700', 'bg' => 'bg-yellow-50', 'bar' => 'bg-yellow-500', 'ring' => 'ring-yellow-200'],
        'Risky'             => ['text' => 'text-orange-700', 'bg' => 'bg-orange-50', 'bar' => 'bg-orange-500', 'ring' => 'ring-orange-200'],
        default             => ['text' => 'text-red-700',    'bg' => 'bg-red-50',    'bar' => 'bg-red-500',    'ring' => 'ring-red-200'],
    };

    $money = fn ($v) => $v === null ? 'N/A' : number_format($v, 2);

    $netProfit = $response['net_profit'];
    $netClass = $netProfit > 0 ? 'text-green-600' : ($netProfit < 0 ? 'text-red-600' : 'text-slate-900');

    $metrics = [
        ['label' => 'Cost per lead (CPL)',     'value' => $money($response['cpl']),          'sub' => 'Break-even: ' . $money($response['break_even_cpl'])],
        ['label' => 'Cost per customer (CPA)', 'value' => $money($response['cpa']),          'sub' => 'Break-even: ' . $money($response['break_even_cpa'])],
        ['label' => 'Revenue',                 'value' => $money($response['revenue']),      'sub' => null],
        ['label' => 'ROAS',                    'value' => $response['roas'] === null ? 'N/A' : number_format($response['roas'], 2) . 'x', 'sub' => 'Sales per 1 spent on ads'],
    ];
@endphp

<div class="mx-auto w-full max-w-xl px-4 py-8 sm:py-12">
    <header class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-slate-900 sm:text-3xl">Your Ad Profit Result</h1>
    </header>

    {{-- Score --}}
    <section class="rounded-2xl p-6 text-center ring-1 {{ $theme['bg'] }} {{ $theme['ring'] }}">
        <p class="text-sm font-medium text-slate-600">Profitability score</p>
        <p class="mt-1 text-6xl font-extrabold {{ $theme['text'] }}">
            {{ $score }}<span class="text-2xl font-semibold text-slate-400">/100</span>
        </p>
        <p class="mt-2 text-xl font-semibold {{ $theme['text'] }}">{{ $response['score_label'] }}</p>

        <div class="mt-4 h-3 w-full overflow-hidden rounded-full bg-white/70">
            <div class="h-full rounded-full {{ $theme['bar'] }}" style="width: {{ $score }}%"></div>
        </div>
    </section>

    {{-- Net profit --}}
    <section class="mt-4 rounded-2xl bg-white p-5 text-center shadow-sm ring-1 ring-slate-200">
        <p class="text-sm font-medium text-slate-600">Net profit</p>
        <p class="mt-1 text-3xl font-bold {{ $netClass }}">{{ $money($netProfit) }}</p>
    </section>

    {{-- Metrics --}}
    <section class="mt-4 grid grid-cols-2 gap-3">
        @foreach ($metrics as $m)
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-medium text-slate-500">{{ $m['label'] }}</p>
                <p class="mt-1 text-xl font-bold text-slate-900">{{ $m['value'] }}</p>
                @if ($m['sub'])
                    <p class="mt-1 text-xs text-slate-500">{{ $m['sub'] }}</p>
                @endif
            </div>
        @endforeach
    </section>

    {{-- Bottleneck --}}
    <section class="mt-4 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
        <p class="text-sm font-medium text-slate-600">Main bottleneck</p>
        <p class="mt-1 text-lg font-semibold text-slate-900">{{ $response['bottleneck'] }}</p>
    </section>

    @if ($response['cpl'] === null)
        <p class="mt-4 rounded-lg bg-slate-100 p-3 text-xs text-slate-600">
            No leads were entered, so cost per lead can't be calculated (N/A).
        </p>
    @endif

    <a href="{{ route('submission.index') }}"
       class="mt-6 block w-full rounded-lg bg-indigo-600 px-4 py-3 text-center text-base font-semibold text-white shadow-sm
              hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
        Calculate again
    </a>
</div>
@endsection