<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Auth\Pages\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class FilamentAdminTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array<int, string>>
     */
    public static function panelRoutesProvider(): array
    {
        return [
            'dashboard' => ['/admin'],
            'alcaldes' => ['/admin/alcaldes'],
            'concejals' => ['/admin/concejals'],
            'locals' => ['/admin/locals'],
            'mesas' => ['/admin/mesas'],
            'pactos' => ['/admin/pactos'],
            'partidos' => ['/admin/partidos'],
            'resultados-alcaldes' => ['/admin/resultados-alcaldes'],
            'resultados-concejals' => ['/admin/resultados-concejals'],
            'subpactos' => ['/admin/subpactos'],
        ];
    }

    #[DataProvider('panelRoutesProvider')]
    public function test_panel_page_renders_for_an_administrator(string $route): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->get($route)->assertSuccessful();
    }

    #[DataProvider('panelRoutesProvider')]
    public function test_panel_page_redirects_guests_to_login(string $route): void
    {
        $this->get($route)->assertRedirect('/admin/login');
    }

    #[DataProvider('panelRoutesProvider')]
    public function test_panel_page_denies_users_without_admin_flag(string $route): void
    {
        // Regresion: canAccessPanel() devolvia true sin mirar nada, de modo que
        // cualquier cuenta autenticada entraba al panel completo. Filament
        // responde 403, no una redireccion, cuando el usuario esta autenticado
        // pero su canAccessPanel() es falso.
        $this->actingAs(User::factory()->create());

        $this->get($route)->assertForbidden();
    }

    public function test_login_is_rejected_for_users_without_admin_flag(): void
    {
        // El propio login de Filament consulta canAccessPanel() y falla con el
        // mismo mensaje que una contrasena incorrecta, de modo que no reveals
        // que la cuenta existe pero no tiene acceso.
        $user = User::factory()->create();

        Livewire::test(Login::class)
            ->fillForm([
                'email' => $user->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertHasFormErrors(['email']);

        $this->assertGuest();
    }

    public function test_login_succeeds_for_administrators(): void
    {
        $user = User::factory()->admin()->create();

        Livewire::test(Login::class)
            ->fillForm([
                'email' => $user->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertHasNoFormErrors();

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_page_is_public(): void
    {
        $this->get('/admin/login')->assertSuccessful();
    }

    public function test_is_admin_is_not_mass_assignable(): void
    {
        // Si fuera asignable en masa, cualquier formulario que cree o actualice
        // un usuario podria concederle acceso al panel.
        $user = User::factory()->create();

        $this->assertNotContains('is_admin', (new User)->getFillable());

        $user->forceFill(['is_admin' => false])->save();
        $user->update(['is_admin' => true]);

        $this->assertFalse($user->fresh()->is_admin);
    }

    public function test_profile_page_is_available_to_administrators(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->get('/admin/profile')->assertSuccessful();
    }
}
