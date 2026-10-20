<?php

/*
|--------------------------------------------------------------------------
| K-Academy sync (ported from config.php)
|--------------------------------------------------------------------------
| K-Academy is a separate database ("kacademy" connection). As before, K-Connect
| keeps working if it is unreachable: getKADB() then returns null.
*/

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;

if (! function_exists('getKADB')) {
    function getKADB(): ?ConnectionInterface
    {
        static $checked = false;
        static $conn = null;
        if (! $checked) {
            $checked = true;
            try {
                $conn = DB::connection('kacademy');
                $conn->getPdo();
            } catch (\Throwable $e) {
                error_log('K-Academy Database connection failed: '.$e->getMessage());
                $conn = null;
            }
        }

        return $conn;
    }
}

if (! function_exists('syncUserToKAcademy')) {
    function syncUserToKAcademy($employee_code, $full_name, $email, $password = null, $role = 'user'): array
    {
        $ka = getKADB();
        if (! $ka) {
            error_log('K-Academy connection not available for sync');

            return ['success' => false, 'message' => 'K-Academy connection not available'];
        }
        if (empty($email) || empty($employee_code)) {
            error_log("Skipping user sync - missing email or employee_code: $employee_code");

            return ['success' => false, 'message' => 'Missing email or employee code'];
        }

        try {
            $existing = $ka->selectOne('SELECT id, status FROM users WHERE employee_code = ? OR email = ?', [$employee_code, $email]);
            if ($existing) {
                $sql = "UPDATE users SET
            full_name = ?,
            email = ?,
            status = 'active',
            email_verified = 1,
            updated_at = NOW()";
                $bindings = [$full_name, $email];
                if ($password !== null && ! empty($password)) {
                    $sql .= ', password = ?';
                    $bindings[] = password_hash($password, PASSWORD_DEFAULT);
                }
                $sql .= ' WHERE employee_code = ? OR email = ?';
                $bindings[] = $employee_code;
                $bindings[] = $email;
                $ka->update($sql, $bindings);
                error_log("User updated and activated in K-Academy: $employee_code");

                return ['success' => true, 'message' => 'User updated and activated in K-Academy', 'action' => 'updated'];
            }

            $hashed = ($password !== null && ! empty($password))
                ? password_hash($password, PASSWORD_DEFAULT)
                : password_hash('default123', PASSWORD_DEFAULT);
            $ka->insert("INSERT INTO users (
            employee_code,
            full_name,
            email,
            password,
            activation_token,
            email_verified,
            role,
            status,
            created_at
        ) VALUES (?, ?, ?, ?, ?, 1, ?, 'active', NOW())", [$employee_code, $full_name, $email, $hashed, bin2hex(random_bytes(32)), $role]);
            $user_id = $ka->getPdo()->lastInsertId();
            error_log("User synced and activated in K-Academy: $employee_code (ID: $user_id)");

            return ['success' => true, 'message' => 'User synced and activated in K-Academy', 'action' => 'inserted', 'user_id' => $user_id];
        } catch (\Throwable $e) {
            error_log('Failed to sync user to K-Academy: '.$e->getMessage());

            return ['success' => false, 'message' => 'Failed to sync user to K-Academy: '.$e->getMessage()];
        }
    }
}

if (! function_exists('syncAllUsersToKAcademy')) {
    function syncAllUsersToKAcademy(): array
    {
        if (! getKADB()) {
            return ['success' => false, 'message' => 'K-Academy connection not available'];
        }
        $users = DB::select("SELECT * FROM employees WHERE status = 1 AND email IS NOT NULL AND email != '' ORDER BY id");
        $synced = $updated = $failed = $skipped = 0;
        $errors = [];
        foreach ($users as $user) {
            if (empty($user['email']) || empty($user['employee_code'])) {
                $skipped++;
                $errors[] = "Skipped ID {$user['id']}: Missing email or employee code";

                continue;
            }
            $result = syncUserToKAcademy($user['employee_code'], $user['full_name'], $user['email'], null, $user['role'] ?? 'user');
            if ($result['success']) {
                $result['action'] === 'inserted' ? $synced++ : $updated++;
            } else {
                $failed++;
                $errors[] = $user['employee_code'].': '.$result['message'];
            }
        }

        return ['success' => true, 'synced' => $synced, 'updated' => $updated, 'failed' => $failed, 'skipped' => $skipped, 'errors' => $errors];
    }
}

if (! function_exists('forceActivateAllKAcademyUsers')) {
    function forceActivateAllKAcademyUsers(): array
    {
        $ka = getKADB();
        if (! $ka) {
            return ['success' => false, 'message' => 'K-Academy connection not available'];
        }
        try {
            $affected = $ka->update("UPDATE users SET status = 'active', email_verified = 1 WHERE status != 'active' OR email_verified = 0");
            error_log("Force activated $affected users in K-Academy");

            return ['success' => true, 'message' => "Activated $affected users", 'affected' => $affected];
        } catch (\Throwable $e) {
            error_log('Failed to force activate users: '.$e->getMessage());

            return ['success' => false, 'message' => 'Failed to activate users: '.$e->getMessage()];
        }
    }
}

if (! function_exists('userExistsInKAcademy')) {
    function userExistsInKAcademy($employee_code = null, $email = null): bool
    {
        $ka = getKADB();
        if (! $ka || (empty($employee_code) && empty($email))) {
            return false;
        }
        $user = $employee_code
            ? $ka->selectOne('SELECT id, status FROM users WHERE employee_code = ?', [$employee_code])
            : $ka->selectOne('SELECT id, status FROM users WHERE email = ?', [$email]);

        return $user ? $user['status'] === 'active' : false;
    }
}
