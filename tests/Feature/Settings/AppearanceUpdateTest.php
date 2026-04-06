<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppearanceUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_appearance_settings_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/settings/appearance');

        $response->assertStatus(200);
    }

    public function test_users_can_update_theme_to_light(): void
    {
        $user = User::factory()->create([
            'theme_preference' => 'system',
        ]);

        $response = $this->actingAs($user)
            ->from('/settings/appearance')
            ->put('/settings/appearance', [
                'theme_preference' => 'light',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/settings/appearance');
        
        $this->assertEquals('light', $user->refresh()->theme_preference);
    }

    public function test_users_can_update_theme_to_dark(): void
    {
        $user = User::factory()->create([
            'theme_preference' => 'light',
        ]);

        $response = $this->actingAs($user)->put('/settings/appearance', [
            'theme_preference' => 'dark',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals('dark', $user->refresh()->theme_preference);
    }

    public function test_users_can_update_theme_to_system(): void
    {
        $user = User::factory()->create([
            'theme_preference' => 'dark',
        ]);

        $response = $this->actingAs($user)->put('/settings/appearance', [
            'theme_preference' => 'system',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals('system', $user->refresh()->theme_preference);
    }

    public function test_theme_update_requires_valid_value(): void
    {
        $user = User::factory()->create([
            'theme_preference' => 'system',
        ]);

        $response = $this->actingAs($user)->put('/settings/appearance', [
            'theme_preference' => 'invalid-theme',
        ]);

        $response->assertSessionHasErrors('theme_preference');
        $this->assertEquals('system', $user->refresh()->theme_preference);
    }

    public function test_guests_cannot_update_theme_preference(): void
    {
        $response = $this->put('/settings/appearance', [
            'theme_preference' => 'dark',
        ]);

        $response->assertRedirect('/login');
    }
}
