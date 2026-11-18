<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Create the initial administrator account from environment configuration.
     */
    public function run(): void
    {
        $email = config('desa.admin.email');
        $password = config('desa.admin.password');

        if (blank($email) || blank($password)) {
            $this->command?->warn('ADMIN_EMAIL atau ADMIN_PASSWORD belum diset. Seeder admin dilewati.');

            return;
        }

        $admin = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => config('desa.admin.name'),
                'password' => $password,
                'email_verified_at' => now(),
            ],
        );

        if (! $admin->is_admin) {
            $admin->forceFill(['is_admin' => true])->save();
        }
    }
}
