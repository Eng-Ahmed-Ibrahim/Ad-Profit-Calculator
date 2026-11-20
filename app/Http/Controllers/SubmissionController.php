<?php

namespace App\Http\Controllers;

use App\Services\CalculationService;
use App\Http\Requests\CalculateRequest;

class SubmissionController extends Controller
{
    public function __construct(private CalculationService $calculationService) {}
    public function index()
    {
        return view('submission.index');
    }
    public function store(CalculateRequest $request)
    {
        $validated = $request->validated();

        try {
            $response = $this->calculationService->calculate($validated);
        } catch (\Exception $e) {
            log('Error calculating submission: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while processing your submission. Please try again.');
        }

        return view('submission.result')
            ->with('success', 'Submission successful!')
            ->with('response', $response)
        ;
    }
}
