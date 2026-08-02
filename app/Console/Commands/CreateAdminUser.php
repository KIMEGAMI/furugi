<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create
        {email=admin@shinji.work : Admin email address}
        {--password= : Admin password. Omit this option to enter it securely.}';

    protected $description = 'Create or update an admin user in the database.';

    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $password = $this->resolvePassword();

        $validator = Validator::make(
            ['email' => $email, 'password' => $password],
            [
                'email' => ['required', 'email:rfc', 'max:255'],
                'password' => ['required', 'string', Password::min(8)],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Admin',
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'is_admin' => true,
            ]
        );

        $this->info('Admin user was saved to the database.');

        return self::SUCCESS;
    }

    private function resolvePassword(): string
    {
        $password = $this->option('password');

        if (is_string($password) && $password !== '') {
            $this->warn('Passing passwords with --password may leave them in shell history. Prefer hidden input.');

            return $password;
        }

        $password = (string) $this->secret('Admin password');
        $confirmation = (string) $this->secret('Confirm admin password');

        if (! hash_equals($password, $confirmation)) {
            $this->error('The password confirmation does not match.');

            return '';
        }

        return $password;
    }
}
