<?php

namespace App\Enums;

enum VisitRecordingStatus: string
{
    case Recorded = 'recorded';
    case Duplicate = 'duplicate';
    case NotFound = 'not_found';
    case Inactive = 'inactive';
    case NotBorrower = 'not_borrower';
}
