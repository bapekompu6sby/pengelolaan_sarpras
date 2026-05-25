<?php

namespace Tests\Feature;

use App\Models\Properties;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class PublicRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_dashboard_is_accessible()
    {
        $response = $this->get('/');
        $response->assertRedirect(route('dashboard'));
    }

    public function test_internal_bapekom_is_accessible()
    {
        $response = $this->get(route('internalBapekomp'));
        $response->assertStatus(200);
    }

    public function test_calendar_is_accessible()
    {
        $response = $this->get(route('calendar'));
        $response->assertStatus(200);
    }

    public function test_check_availability_returns_json()
    {
        $user = User::factory()->create();

        $property = Properties::create([
            'name' => 'Aula Cek',
            'type' => 'aula',
            'capacity' => 100,
            'price' => 500000,
            'unit' => 2,
        ]);

        $response = $this->actingAs($user)
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->post(route('properties.check'), [
                'venue_id' => $property->id,
                'start_date' => Carbon::now()->addDays(1)->format('Y-m-d'),
                'end_date' => Carbon::now()->addDays(2)->format('Y-m-d'),
                'ordered_unit' => 1,
                'property_type' => 'aula',
            ]);

        $response->assertStatus(200);
        
        $response->assertJson([
            'available' => true,
            'avail_count' => 2,
            'checked_type' => 'aula',
        ]);
        
        $response->assertJsonStructure([
            'available',
            'avail_count',
            'checked_type',
            'debug' => [
                'start',
                'end',
                'jam_start',
                'jam_end'
            ]
        ]);
    }
}
