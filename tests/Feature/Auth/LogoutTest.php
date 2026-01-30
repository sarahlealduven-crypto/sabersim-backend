<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

it('can logout successfully', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth-token', ['*'], now()->addDays(30));
    $tokenId = $token->accessToken->id;

    $response = postJson('/api/auth/logout', [], [
        'Authorization' => 'Bearer '.$token->plainTextToken,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Sesión cerrada exitosamente.',
        ]);

    // Verificar que el token fue eliminado
    $this->assertDatabaseMissing('personal_access_tokens', [
        'id' => $tokenId,
    ]);
});

it('cannot logout without authentication', function () {
    $response = postJson('/api/auth/logout');

    $response->assertStatus(401);
});

it('cannot logout with invalid token', function () {
    $response = postJson('/api/auth/logout', [], [
        'Authorization' => 'Bearer invalid-token',
    ]);

    $response->assertStatus(401);
});

it('deletes only the current access token on logout', function () {
    $user = User::factory()->create();

    // Crear dos tokens para el mismo usuario
    $token1 = $user->createToken('auth-token-1', ['*'], now()->addDays(30));
    $token2 = $user->createToken('auth-token-2', ['*'], now()->addDays(30));

    $tokenId1 = $token1->accessToken->id;
    $tokenId2 = $token2->accessToken->id;

    // Hacer logout con el primer token
    $response = postJson('/api/auth/logout', [], [
        'Authorization' => 'Bearer '.$token1->plainTextToken,
    ]);
    $response->assertStatus(200);

    // Verificar que solo el primer token fue eliminado
    $this->assertDatabaseMissing('personal_access_tokens', [
        'id' => $tokenId1,
    ]);

    $this->assertDatabaseHas('personal_access_tokens', [
        'id' => $tokenId2,
    ]);

    // El segundo token todavía debería funcionar
    $secondResponse = postJson('/api/auth/logout', [], [
        'Authorization' => 'Bearer '.$token2->plainTextToken,
    ]);
    $secondResponse->assertStatus(200);
});
