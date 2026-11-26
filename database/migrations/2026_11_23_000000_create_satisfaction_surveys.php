<?php

use App\Support\Surveys;
use Illuminate\Database\Migrations\Migration;

/**
 * Satisfaction surveys: adds the four survey tables if they are missing (nothing existing is changed).
 * The admin page also creates them on first visit, and database/sql/satisfaction_surveys.sql does the same in phpMyAdmin.
 */
return new class extends Migration
{
    public function up(): void
    {
        if ($error = Surveys::ensureTables()) {
            throw new RuntimeException($error);
        }
    }

    public function down(): void
    {
        // Left in place on purpose: these tables hold survey answers.
    }
};
