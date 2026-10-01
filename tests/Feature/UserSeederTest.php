<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Env;
use Tests\TestCase;

class UserSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Env::getRepository()->clear('ADMIN_EMAIL');
        Env::getRepository()->clear('ADMIN_PASSWORD');
        Env::getRepository()->clear('ADMIN_NAME');

        parent::tearDown();
    }

    public function test_it_creates_an_administrator(): void
    {
        $this->seed(UserSeeder::class);

        $user = User::firstOrFail();

        $this->assertTrue($user->isAdmin(), 'La cuenta sembrada debe poder acceder al panel.');
        $this->assertNotEmpty($user->password);
    }

    public function test_it_reads_the_account_from_the_environment(): void
    {
        Env::getRepository()->set('ADMIN_EMAIL', 'elecciones@example.org');
        Env::getRepository()->set('ADMIN_NAME', 'Comisión Electoral');
        Env::getRepository()->set('ADMIN_PASSWORD', 'una-contrasena-larga-y-unica');

        $this->seed(UserSeeder::class);

        $user = User::firstOrFail();

        $this->assertSame('elecciones@example.org', $user->email);
        $this->assertSame('Comisión Electoral', $user->name);
        $this->assertTrue($user->isAdmin());
        $this->assertTrue(
            auth()->validate([
                'email' => 'elecciones@example.org',
                'password' => 'una-contrasena-larga-y-unica',
            ]),
            'La contrasena del entorno debe ser la que permite iniciar sesion.'
        );
    }

    public function test_it_generates_a_password_when_the_environment_does_not_define_one(): void
    {
        Env::getRepository()->clear('ADMIN_PASSWORD');

        $this->seed(UserSeeder::class);

        $user = User::firstOrFail();

        $this->assertFalse(
            auth()->validate(['email' => $user->email, 'password' => 'password']),
            'Con ADMIN_PASSWORD vacio la contrasena debe ser aleatoria, no una conocida.'
        );
    }

    public function test_it_does_not_duplicate_an_existing_account(): void
    {
        Env::getRepository()->set('ADMIN_EMAIL', 'elecciones@example.org');

        $this->seed(UserSeeder::class);
        $this->seed(UserSeeder::class);

        $this->assertSame(1, User::where('email', 'elecciones@example.org')->count());
    }

    public function test_no_password_is_committed_to_the_repository(): void
    {
        // El repositorio es publico, asi que un hash bcrypt en el codigo es una
        // credencial publicada. Este test falla si alguien reintroduce una.
        $source = file_get_contents(database_path('seeders/UserSeeder.php'));

        $this->assertDoesNotMatchRegularExpression(
            '/\$2[aby]\$[0-9]{2}\$/',
            $source,
            'UserSeeder no debe contener un hash bcrypt literal.'
        );
    }
}
