<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * K-Academy lesson quizzes: a quiz can belong to one lesson (quizzes.lesson_id), while a quiz
 * with no lesson stays the course's final quiz. Additive only: no row is changed or removed.
 * Safe to run more than once. Same SQL for phpMyAdmin: database/sql/academy_lesson_quizzes.sql
 */
return new class extends Migration
{
    public function up(): void
    {
        $ka = DB::connection('kacademy');
        if (Schema::connection('kacademy')->hasColumn('quizzes', 'lesson_id')) {
            return;
        }
        // One statement: the new (course_id, lesson_id) index takes over from the old
        // one-quiz-per-course index, which the course foreign key needs in the meantime.
        $ka->statement('ALTER TABLE quizzes
            ADD COLUMN lesson_id INT(11) NULL DEFAULT NULL AFTER course_id,
            ADD UNIQUE KEY uq_quiz_course_lesson (course_id, lesson_id),
            ADD KEY idx_quiz_lesson (lesson_id),
            DROP INDEX course_id');
        $ka->statement('ALTER TABLE quizzes
            ADD CONSTRAINT quizzes_lesson_fk FOREIGN KEY (lesson_id) REFERENCES lessons (id) ON DELETE CASCADE');
    }

    public function down(): void
    {
        // Left in place on purpose: removing it would delete lesson quizzes and their results.
    }
};
