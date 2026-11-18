<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Akun Administrator
    |--------------------------------------------------------------------------
    |
    | Kredensial admin awal yang dipakai oleh AdminUserSeeder. Nilai ini
    | dibaca dari .env agar tidak perlu mengubah kode saat deploy. Seeder
    | dilewati bila email atau password belum diisi.
    |
    */

    'admin' => [
        'name' => env('ADMIN_NAME', 'Administrator Desa'),
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
    ],

];
