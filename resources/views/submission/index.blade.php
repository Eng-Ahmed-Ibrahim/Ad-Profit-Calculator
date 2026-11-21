@extends('layout.app')

@section('content')
@php
    $fields = [
        ['name' => 'ad_spend',       'label' => 'Monthly ad spend',      'step' => '0.01', 'hint' => 'Total spent on ads this month'],
        ['name' => 'leads',          'label' => 'Leads per month',       'step' => '1',    'hint' => 'People who showed interest'],
        ['name' => 'customers',      'label' => 'Customers from ads',    'step' => '1',    'hint' => 'Leads who actually bought'],
        ['name' => 'customer_value', 'label' => 'Average customer value', 'step' => '0.01', 'hint' => 'What one customer pays you'],
        ['name' => 'gross_margin',   'label' => 'Gross margin',          'step' => '0.01', 'hint' => 'Profit kept after product/service cost', 'suffix' => '%'],
        ['name' => 'close_rate',     'label' => 'Lead-to-customer close rate', 'step' => '0.01', 'hint' => 'Out of 100 leads, how many buy', 'suffix' => '%'],
    ];
@endphp

<div class="mx-auto w-full max-w-xl px-4 py-8 sm:py-12">
    <header class="mb-8 text-center">
        <h1 class="text-2xl font-bold text-slate-900 sm:text-3xl">Ad Profit Calculator</h1>
    </header>

    @if (session('error'))
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('submission.store') }}" method="POST" novalidate
          class="space-y-5 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 sm:p-8">
        @csrf

        @foreach ($fields as $f)
            <div>
                <label for="{{ $f['name'] }}" class="block text-sm font-medium text-slate-800">
                    {{ $f['label'] }}
                </label>

                <div class="relative mt-1">
                    <input
                        type="number"
                        inputmode="decimal"
                        id="{{ $f['name'] }}"
                        name="{{ $f['name'] }}"
                        value="{{ old($f['name']) }}"
                        step="{{ $f['step'] }}"
                        min="0"
                        @isset($f['suffix']) max="100" @endisset
                        class="block w-full rounded-lg border px-3 py-2.5 text-base text-slate-900 shadow-sm
                               focus:outline-none focus:ring-2 focus:ring-indigo-500
                               {{ $errors->has($f['name']) ? 'border-red-500' : 'border-slate-300' }}"
                    >
                    @isset($f['suffix'])
                        <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-500">
                            {{ $f['suffix'] }}
                        </span>
                    @endisset
                </div>

                <p class="mt-1 text-xs text-slate-500">{{ $f['hint'] }}</p>
                @error($f['name'])
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            @if ($f['name'] === 'leads')
                <div class="flex items-center justify-between rounded-lg bg-indigo-50 px-4 py-3">
                    <span class="text-sm font-medium text-indigo-900">Cost per lead </span>
                    <span id="cpl-preview" class="text-lg font-bold text-indigo-700">—</span>
                </div>
            @endif
        @endforeach
        <div>
            <label for="response_time" class="block text-sm font-medium text-slate-800">
                Lead response time
            </label>
            <select id="response_time" name="response_time"
                    class="mt-1 block w-full rounded-lg border bg-white px-3 py-2.5 text-base text-slate-900 shadow-sm
                           focus:outline-none focus:ring-2 focus:ring-indigo-500
                           {{ $errors->has('response_time') ? 'border-red-500' : 'border-slate-300' }}">
                <option value="">Select...</option>
                <option value="under_5_min"       @selected(old('response_time') === 'under_5_min')>Under 5 minutes</option>
                <option value="same_day"          @selected(old('response_time') === 'same_day')>Same day</option>
                <option value="next_day_or_later" @selected(old('response_time') === 'next_day_or_later')>Next day or later</option>
            </select>
            @error('response_time')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <fieldset>
            <legend class="block text-sm font-medium text-slate-800">Do you have a follow-up process?</legend>
            <div class="mt-2 grid grid-cols-2 gap-3">
                @foreach (['1' => 'Yes', '0' => 'No'] as $val => $text)
                    <label class="flex cursor-pointer items-center justify-center rounded-lg border border-slate-300 px-3 py-2.5
                                  text-sm font-medium text-slate-700 has-[:checked]:border-indigo-600
                                  has-[:checked]:bg-indigo-50 has-[:checked]:text-indigo-700">
                        <input type="radio" name="has_follow_up" value="{{ $val }}" class="sr-only"
                               @checked((string) old('has_follow_up') === (string) $val)>
                        {{ $text }}
                    </label>
                @endforeach
            </div>
            @error('has_follow_up')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </fieldset>

        <button type="submit"
                class="w-full rounded-lg bg-indigo-600 px-4 py-3 text-base font-semibold text-white shadow-sm
                       hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Calculate
        </button>
    </form>
</div>

<script>
    const spend = document.getElementById('ad_spend');
    const leads = document.getElementById('leads');
    const out = document.getElementById('cpl-preview');

    function updateCpl() {
        const s = parseFloat(spend.value);
        const l = parseFloat(leads.value);
        out.textContent = (l > 0 && !isNaN(s)) ? (s / l).toFixed(2) : '—';
    }

    spend.addEventListener('input', updateCpl);
    leads.addEventListener('input', updateCpl);
    updateCpl(); 
</script>
@endsection