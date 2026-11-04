-- K-Academy lesson quizzes (run once on the kiboko_academy database, e.g. in phpMyAdmin).
-- Additive only: adds quizzes.lesson_id. A quiz without a lesson stays the course final quiz.
-- No existing row is changed or removed. Same change as the Laravel migration
-- 2026_11_04_000000_add_lesson_id_to_academy_quizzes (php artisan migrate does it for you).

ALTER TABLE quizzes
    ADD COLUMN lesson_id INT(11) NULL DEFAULT NULL AFTER course_id,
    ADD UNIQUE KEY uq_quiz_course_lesson (course_id, lesson_id),
    ADD KEY idx_quiz_lesson (lesson_id),
    DROP INDEX course_id;

ALTER TABLE quizzes
    ADD CONSTRAINT quizzes_lesson_fk FOREIGN KEY (lesson_id) REFERENCES lessons (id) ON DELETE CASCADE;
