<?php

namespace App\Services;

use App\Models\Submission;
use App\Services\CalculationService;

class SubmissionService
{
    public function __construct(private CalculationService $calculator) {}

    public function create(array $validated): Submission
    {
        return Submission::create([
            ...$validated,
            ...$this->calculator->calculate($validated),
        ]);
    }
}
