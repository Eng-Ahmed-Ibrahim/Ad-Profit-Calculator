<?php

namespace App\Http\Controllers;

use App\Services\SubmissionService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\CalculateRequest;

class SubmissionController extends Controller
{
    public function __construct(private SubmissionService $submissionService) {}
    public function index()
    {
        return view('submission.index');
    }
    public function store(CalculateRequest $request)
    {
        $validated = $request->validated();

        try {
            $response = $this->submissionService->create($validated);
        } catch (\Exception $e) {
            Log::error('Error creating submission: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while processing your submission. Please try again.');  
        }

        return view('submission.result')
            ->with('success', 'Submission successful!')
            ->with('response', $response)
        ;
    }
}
