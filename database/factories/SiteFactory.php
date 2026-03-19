<?php

namespace Database\Factories;

use App\Enums\CaptchaProvider;
use App\Enums\SiteAuthMode;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Site>
 */
class SiteFactory extends Factory
{
    protected $model = Site::class;

    public function definition(): array
    {
        return [
            'tenant_id' => User::factory(),
            'name' => fake()->company().' Site',
            'domain' => fake()->unique()->domainName(),
            'notification_email' => fake()->safeEmail(),
            'public_key' => Str::upper(Str::random(32)),
            'auth_mode' => SiteAuthMode::NONE,
            'captcha_provider' => CaptchaProvider::NONE,
            'is_active' => true,
            'metadata' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
