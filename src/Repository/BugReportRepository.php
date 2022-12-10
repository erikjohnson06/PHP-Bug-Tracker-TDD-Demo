<?php

namespace App\Repository;

use App\Entity\BugReport;

/**
 * BugReportRepository
 *
 * @author erikjohnson06
 */
class BugReportRepository extends Repository {
    
    protected static string $table = "reports";
    protected static string $className = BugReport::class;
}
