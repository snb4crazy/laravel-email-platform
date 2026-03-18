<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PlatformBootstrapUserCommand extends Command
{
    protected $signature = 'platform:bootstrap-user';

    protected $description = 'Create the first platform user interactively (public signup remains disabled)';

    public function handle(): int
    {
        $this->components->info('Bootstrap first user for Email Platform');

        if (User::query()->exists()) {
            $this->components->warn('A user already exists. Public signup is disabled; use this command only for controlled account creation.');
        }

        $name = trim((string) $this->ask('Full name'));
        $email = strtolower(trim((string) $this->ask('Email address')));
        $password = (string) $this->secret('Password (min 8 chars)');
        $passwordConfirmation = (string) $this->secret('Confirm password');
        $markVerified = (bool) $this->confirm('Mark email as verified?', true);

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            $this->components->error('User was not created due to validation errors.');

            foreach ($validator->errors()->all() as $error) {
                $this->line('- '.$error);
            }

            return self::FAILURE;
        }

        $user = User::query()->create([
            'name' => $name,
            'email' => $email,
            'role' => User::ROLE_ADMIN,
            'password' => Hash::make($password),
            'email_verified_at' => $markVerified ? now() : null,
        ]);

        $this->components->info('User created successfully.');
        $this->table(['id', 'name', 'email', 'role', 'email_verified_at'], [[
            $user->id,
            $user->name,
            $user->email,
            $user->role,
            $user->email_verified_at,
        ]]);

        return self::SUCCESS;
    }
}
