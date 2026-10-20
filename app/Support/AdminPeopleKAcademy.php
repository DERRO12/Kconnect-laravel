<?php

namespace App\Support;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;

/**
 * K-Academy helpers that admin/manage-employees defined locally
 * (it opened its own mysqli connection; now the "kacademy" connection via getKADB()).
 */
class AdminPeopleKAcademy
{
    /** Old syncSingleEmployeeToKAcademy($conn, $kacademy, $employee_id). */
    public static function syncSingleEmployee(?ConnectionInterface $kacademy, $employee_id): array
    {
        if (! $kacademy) {
            return ['success' => false, 'message' => 'K-Academy connection not available'];
        }

        $emp = DB::selectOne('SELECT * FROM employees WHERE id = '.intval($employee_id));
        if (! $emp) {
            return ['success' => false, 'message' => 'Employee not found'];
        }

        if (empty($emp['email']) || empty($emp['employee_code'])) {
            return ['success' => false, 'message' => 'Missing email or employee code'];
        }

        $employee_code = $emp['employee_code'] ?? '';
        $full_name = $emp['full_name'] ?? '';
        $email = $emp['email'] ?? '';
        $role = $emp['role'] ?? 'user';

        $check = $kacademy->select('SELECT id FROM users WHERE employee_code = ? OR email = ?', [$employee_code, $email]);

        if (count($check) > 0) {
            try {
                $kacademy->update("UPDATE users SET 
            full_name = ?,
            email = ?,
            status = 'active',
            email_verified = 1,
            updated_at = NOW()
            WHERE employee_code = ? OR email = ?", [$full_name, $email, $employee_code, $email]);

                return ['success' => true, 'message' => 'Employee updated in K-Academy', 'action' => 'updated'];
            } catch (\Throwable $e) {
                return ['success' => false, 'message' => 'Update failed: '.$e->getMessage()];
            }
        }

        $password_hash = password_hash('default123', PASSWORD_DEFAULT);
        $activation_token = bin2hex(random_bytes(32));
        try {
            $kacademy->insert("INSERT INTO users (
            employee_code, 
            full_name, 
            email, 
            password, 
            activation_token,
            email_verified,
            role,
            status,
            created_at
        ) VALUES (
            ?,
            ?,
            ?,
            ?,
            ?,
            1,
            ?,
            'active',
            NOW()
        )", [$employee_code, $full_name, $email, $password_hash, $activation_token, $role]);

            return ['success' => true, 'message' => 'Employee synced to K-Academy', 'action' => 'inserted'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Insert failed: '.$e->getMessage()];
        }
    }

    /** Old isEmployeeSynced($kacademy, $employee_code, $email). */
    public static function isEmployeeSynced(?ConnectionInterface $kacademy, $employee_code, $email): bool
    {
        if (! $kacademy) {
            return false;
        }
        if (empty($employee_code) && empty($email)) {
            return false;
        }

        $check = $kacademy->select('SELECT id FROM users WHERE employee_code = ? OR email = ?', [(string) ($employee_code ?? ''), (string) ($email ?? '')]);

        return count($check) > 0;
    }
}
