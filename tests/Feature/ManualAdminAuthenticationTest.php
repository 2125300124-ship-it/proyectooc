<?php

namespace Tests\Feature;

use App\Models\Administrador;
use App\Models\Cliente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as GoogleUser;
use Tests\TestCase;

class ManualAdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_admin_routes(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->get('/productos/crear')->assertRedirect(route('login'));
    }

    public function test_active_administrator_can_log_in_and_is_identified_on_dashboard(): void
    {
        $administrador = $this->crearAdministrador();

        $this->post('/login', [
            'usuario' => $administrador->usuario,
            'contraseña' => 'secreto123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($administrador, 'admin');
        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('Bienvenido, Ana García')
            ->assertSee('ana-admin');
    }

    public function test_inactive_administrator_is_denied_with_a_specific_message(): void
    {
        $administrador = $this->crearAdministrador(['estado' => false]);

        $this->from('/login')->post('/login', [
            'usuario' => $administrador->usuario,
            'contraseña' => 'secreto123',
        ])->assertRedirect('/login')
            ->assertSessionHasErrors('status', 'Esta cuenta se encuentra desactivada.');

        $this->assertGuest('admin');
    }

    public function test_invalid_password_displays_an_error(): void
    {
        $administrador = $this->crearAdministrador();

        $this->from('/login')->post('/login', [
            'usuario' => $administrador->usuario,
            'contraseña' => 'incorrecta',
        ])->assertRedirect('/login')
            ->assertSessionHasErrors('error', 'Usuario o contraseña incorrectos.');

        $this->assertGuest('admin');
    }

    public function test_google_login_redirects_to_the_provider(): void
    {
        Socialite::fake('google');

        $this->get(route('auth.google.redirect'))
            ->assertRedirect('https://socialite.fake/google/authorize');
    }

    public function test_google_redirect_uses_configured_client_and_callback(): void
    {
        config([
            'services.google.client_id' => 'test-google-client-id',
            'services.google.client_secret' => 'test-google-client-secret',
            'services.google.redirect' => route('auth.google.callback'),
        ]);

        $response = $this->get(route('auth.google.redirect'));
        $response->assertRedirect();

        $redirectUrl = $response->headers->get('Location');
        parse_str((string) parse_url($redirectUrl, PHP_URL_QUERY), $query);

        $this->assertSame('accounts.google.com', parse_url($redirectUrl, PHP_URL_HOST));
        $this->assertSame('test-google-client-id', $query['client_id'] ?? null);
        $this->assertSame(route('auth.google.callback'), $query['redirect_uri'] ?? null);
    }

    public function test_login_page_displays_google_sign_in_action(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Iniciar sesión con Google')
            ->assertSee(route('auth.google.redirect'));
    }

    public function test_google_callback_logs_in_matching_active_administrator(): void
    {
        $administrador = $this->crearAdministrador();
        Socialite::fake('google', GoogleUser::fake([
            'email' => $administrador->correo,
            'email_verified' => true,
        ]));

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($administrador, 'admin');
    }

    public function test_google_callback_registers_customer_and_redirects_to_home(): void
    {
        Socialite::fake('google', GoogleUser::fake([
            'id' => 'google-customer-123',
            'name' => 'Laura Gómez',
            'email' => 'laura@example.test',
            'email_verified' => true,
            'avatar' => 'https://example.test/avatar.jpg',
        ]));

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('inicio'));

        $cliente = Cliente::query()->where('correo', 'laura@example.test')->firstOrFail();
        $this->assertSame('Laura', $cliente->nombres);
        $this->assertSame('Gómez', $cliente->apellidos);
        $this->assertSame('google-customer-123', $cliente->google_id);
        $this->assertAuthenticatedAs($cliente, 'web');
    }

    public function test_google_callback_logs_in_existing_customer_and_redirects_to_home(): void
    {
        $cliente = Cliente::query()->create([
            'nombres' => 'Laura',
            'apellidos' => 'Gómez',
            'correo' => 'laura@example.test',
            'contraseña' => Hash::make('secreto123'),
            'estado' => true,
        ]);
        Socialite::fake('google', GoogleUser::fake([
            'id' => 'google-customer-123',
            'email' => $cliente->correo,
            'email_verified' => true,
        ]));

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('inicio'));

        $this->assertSame('google-customer-123', $cliente->fresh()->google_id);
        $this->assertAuthenticatedAs($cliente, 'web');
    }

    public function test_google_callback_returns_to_login_when_authorization_is_cancelled(): void
    {
        $this->get(route('auth.google.callback', ['error' => 'access_denied']))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('google');

        $this->assertGuest('admin');
    }

    public function test_google_callback_handles_an_invalid_oauth_state(): void
    {
        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn(new class {
                public function user(): never
                {
                    throw new InvalidStateException();
                }
            });

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('google', 'La sesión de inicio con Google venció o cambió. Inténtalo de nuevo.');

        $this->assertGuest('admin');
    }

    public function test_google_callback_rejects_an_unverified_email(): void
    {
        $administrador = $this->crearAdministrador();
        Socialite::fake('google', GoogleUser::fake([
            'email' => $administrador->correo,
            'email_verified' => false,
        ]));

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('google', 'Google no proporcionó un correo verificado para esta cuenta.');

        $this->assertGuest('admin');
    }

    public function test_google_callback_rejects_missing_email_and_inactive_administrator(): void
    {
        $administrador = $this->crearAdministrador();

        Socialite::fake('google', GoogleUser::fake(['email' => null]));
        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('google');
        $this->assertGuest('admin');

        Socialite::fake('google', GoogleUser::fake([
            'email' => $administrador->correo,
            'email_verified' => true,
        ]));
        $administrador->update(['estado' => false]);

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('google');
        $this->assertGuest('admin');
    }

    public function test_logout_ends_the_admin_session(): void
    {
        $administrador = $this->crearAdministrador();
        $this->actingAs($administrador, 'admin');

        $this->post('/logout')->assertRedirect(route('login'));

        $this->assertGuest('admin');
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_customer_can_log_out(): void
    {
        $cliente = Cliente::query()->create([
            'nombres' => 'Laura',
            'apellidos' => 'Gómez',
            'correo' => 'laura@example.test',
            'contraseña' => Hash::make('secreto123'),
            'estado' => true,
        ]);
        $this->actingAs($cliente, 'web');

        $this->post('/logout')->assertRedirect(route('login'));

        $this->assertGuest('web');
    }

    private function crearAdministrador(array $overrides = []): Administrador
    {
        return Administrador::create(array_merge([
            'nombres' => 'Ana',
            'apellidos' => 'García',
            'correo' => 'ana@example.test',
            'usuario' => 'ana-admin',
            'contraseña' => Hash::make('secreto123'),
            'rol' => 'Dueño',
            'estado' => true,
        ], $overrides));
    }
}
