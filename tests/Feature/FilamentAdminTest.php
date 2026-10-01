<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
    public function test_panel_page_renders_for_an_authenticated_user(string $route): void
    {
        $this->actingAs(User::factory()->create());

        $this->get($route)->assertSuccessful();
    }

    #[DataProvider('panelRoutesProvider')]
    public function test_panel_page_redirects_guests_to_login(string $route): void
    {
        $this->get($route)->assertRedirect('/admin/login');
    }

    public function test_login_page_is_public(): void
    {
        $this->get('/admin/login')->assertSuccessful();
    }

    public function test_any_user_can_access_the_panel(): void
    {
        // User::canAccessPanel() devuelve true sin condiciones: no hay control
        // de acceso por rol. Este test documenta esa decision.
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin')
            ->assertSuccessful();
    }
}
