<?php

namespace Tests\Feature;

use App\Models\Alcalde;
use App\Models\Mesa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array<int, string>>
     */
    public static function publicRoutesProvider(): array
    {
        return [
            'home' => ['/'],
            'health' => ['/up'],
            'alcaldes' => ['/alcaldes'],
            'concejales' => ['/concejales'],
            'resultados' => ['/resultados'],
            'talonador' => ['/talonador'],
        ];
    }

    #[DataProvider('publicRoutesProvider')]
    public function test_public_page_responds_successfully(string $route): void
    {
        $this->seedElectionData();

        $this->get($route)->assertSuccessful();
    }

    public function test_resultados_page_does_not_break_with_an_empty_database(): void
    {
        // Regresion: la vista dividia entre Mesa::count() sin comprobar que
        // fuera mayor que cero, provocando "Division by zero" (HTTP 500)
        // cuando la base de datos aun no tenia mesas.
        $this->assertSame(0, Mesa::count());

        $this->get('/resultados')->assertSuccessful();
    }

    public function test_resultados_page_renders_candidate_names_from_the_database(): void
    {
        $this->seedElectionData();

        $this->get('/resultados')
            ->assertSuccessful()
            ->assertSee(Alcalde::query()->firstOrFail()->name, false);
    }
}
