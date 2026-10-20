<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * php artisan kconnect:verify-schema
 *
 * Read-only check: compares every table in the connected database with the K-Connect
 * schema the Laravel app was built against (database/migrations/..._create_kconnect_schema.php).
 * It never changes anything.
 */
class VerifySchema extends Command
{
    protected $signature = 'kconnect:verify-schema';

    protected $description = 'Check (read-only) that the database tables match the K-Connect schema exactly';

    public function handle(): int
    {
        $migration = require database_path('migrations/2026_10_19_000000_create_kconnect_schema.php');
        $expected = (fn () => $this->tables())->call($migration);

        $same = $different = $missing = 0;
        foreach ($expected as $table => $ddl) {
            $row = DB::select('SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?', [$table]);
            if (! $row) {
                $this->error("missing    $table");
                $missing++;

                continue;
            }
            $live = DB::selectOne("SHOW CREATE TABLE `$table`");
            $liveDdl = preg_replace('/ AUTO_INCREMENT=\d+/', '', array_values($live)[1]);
            if ($this->norm($liveDdl) === $this->norm($ddl)) {
                $same++;
            } else {
                $this->warn("different  $table");
                $different++;
            }
        }
        $extra = array_diff(array_map(fn ($r) => array_values($r)[0], DB::select('SHOW TABLES')), array_keys($expected), ['migrations']);
        foreach ($extra as $t) {
            $this->line("extra      $t (not used by K-Connect, left alone)");
        }
        $this->info("$same identical, $different different, $missing missing, ".count($extra).' extra');

        return $different || $missing ? self::FAILURE : self::SUCCESS;
    }

    private function norm(string $sql): string
    {
        return preg_replace('/\s+/', ' ', trim($sql));
    }
}
