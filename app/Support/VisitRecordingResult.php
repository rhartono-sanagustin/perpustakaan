<?php

namespace App\Support;

use App\Enums\VisitRecordingStatus;
use App\Models\LibraryVisit;
use App\Models\User;

final readonly class VisitRecordingResult
{
    public function __construct(
        public VisitRecordingStatus $status,
        public ?User $user = null,
        public ?LibraryVisit $visit = null,
    ) {}
}
