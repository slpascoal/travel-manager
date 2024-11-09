<?php

namespace Tests\Feature;

use App\Models\Travel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TravelTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_travels()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/travels');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'travels' => [
                         '*' => ['id', 'applicant_name', 'destiny', 'departure_date', 'return_date', 'status']
                     ]
                 ]);
    }

    public function test_create_travel()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/travels', [
            'applicant_name' => 'Tester',
            'destiny' => 'New York',
            'departure_date' => '2024-12-01',
            'return_date' => '2024-12-05',
            'status' => 'requested',
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'travel was created successfully',
                 ]);
    }

    public function test_show_travel()
    {
        $user = User::factory()->create();
        $travel = Travel::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/travels/{$travel->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'travel' => [
                         'id' => $travel->id,
                         'applicant_name' => $travel->applicant_name,
                         'destiny' => $travel->destiny,
                         'departure_date' => $travel->departure_date,
                         'return_date' => $travel->return_date,
                         'status' => $travel->status,
                     ]
                 ]);
    }

    public function test_update_travel()
    {
        $user = User::factory()->create();
        $travel = Travel::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $response = $this->putJson("/api/travels/{$travel->id}", [
            'status' => 'approved',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'status updated successfully',
                     'status' => 'approved',
                 ]);
    }

    public function test_delete_travel()
    {
        $user = User::factory()->create();
        $travel = Travel::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/travels/{$travel->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'the travel was deleted',
                 ]);

        $this->assertDatabaseMissing('travel', [
            'id' => $travel->id,
        ]);
    }
}
