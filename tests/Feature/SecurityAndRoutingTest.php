<?php

namespace Tests\Feature;

use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class SecurityAndRoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_responses_include_security_headers_without_hsts_over_http(): void
    {
        $response = $this->withoutVite()->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');
        $response->assertHeaderMissing('Strict-Transport-Security');
    }

    public function test_https_responses_include_hsts_header(): void
    {
        $response = $this->withoutVite()->get('https://localhost/');

        $response->assertStatus(200);
        $response->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }

    public function test_api_unauthenticated_route_returns_json_even_without_accept_header(): void
    {
        $response = $this->get('/api/v1/auth/user', [
            'Accept' => 'text/html',
        ]);

        $response->assertStatus(401)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'success' => false,
                'message' => 'No autenticado.',
            ]);
    }

    public function test_api_not_found_route_returns_json_even_without_accept_header(): void
    {
        $response = $this->get('/api/v1/non-existent-endpoint', [
            'Accept' => 'text/html',
        ]);

        $response->assertStatus(404)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_api_response_error_redacts_sensitive_request_fields_in_debug_mode(): void
    {
        config(['app.debug' => true]);

        $request = Request::create('/api/v1/auth/login', 'POST', [
            'email' => 'user@example.com',
            'password' => 'super-secret-password',
            'password_confirmation' => 'super-secret-password',
            'current_password' => 'old-secret',
            'token' => 'secret-token-value',
            'access_token' => 'secret-access-token',
        ]);
        $this->app->instance('request', $request);

        $response = ApiResponse::error('Error de prueba', 400, null, null, ['step' => 'test']);
        $payload = $response->getData(true);

        $this->assertArrayHasKey('debug', $payload);
        $this->assertSame('user@example.com', $payload['debug']['request']['email']);
        $this->assertArrayNotHasKey('password', $payload['debug']['request']);
        $this->assertArrayNotHasKey('password_confirmation', $payload['debug']['request']);
        $this->assertArrayNotHasKey('current_password', $payload['debug']['request']);
        $this->assertArrayNotHasKey('token', $payload['debug']['request']);
        $this->assertArrayNotHasKey('access_token', $payload['debug']['request']);
    }

    public function test_login_route_redirects_to_panel_login(): void
    {
        $response = $this->get('/login');

        $response->assertRedirect(route('filament.panel.auth.login'));
    }
}
