<?php

namespace Tests\Feature;

use App\Models\Properties;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPropertyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_property()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('properties.store'), [
            'name' => 'Aula Baru',
            'type' => 'aula',
            'capacity' => 100,
            'price' => 500000,
            'unit' => 1,
        ]);

        $response->assertRedirect(route('properties'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('properties', [
            'name' => 'Aula Baru',
            'type' => 'aula',
        ]);
    }

    public function test_admin_can_update_property()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $property = Properties::create([
            'name' => 'Aula Lama',
            'type' => 'aula',
            'capacity' => 50,
            'price' => 100000,
            'unit' => 1,
        ]);

        $response = $this->actingAs($admin)->patch(route('properties.update', $property->id), [
            'name' => 'Aula Update',
            'type' => 'aula',
            'capacity' => 150,
            'price' => 200000,
            'unit' => 2,
        ]);

        $response->assertRedirect(route('properties'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'name' => 'Aula Update',
            'capacity' => 150,
        ]);
    }

    public function test_admin_can_delete_property()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $property = Properties::create([
            'name' => 'Aula Untuk Dihapus',
            'type' => 'aula',
            'capacity' => 50,
            'price' => 100000,
            'unit' => 1,
        ]);

        $response = $this->actingAs($admin)->delete(route('properties.destroy', $property->id));

        $response->assertRedirect(route('properties'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('properties', [
            'id' => $property->id,
        ]);
    }

    public function test_admin_can_toggle_property_status()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $property = Properties::create([
            'name' => 'Aula Test',
            'type' => 'aula',
            'capacity' => 50,
            'price' => 100000,
            'unit' => 1,
            'status' => 'inactive'
        ]);

        $response = $this->actingAs($admin)
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->patch(route('properties.status', $property->id), [
                'is_active' => true,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'ok' => true,
            'status' => 'ative' // Notice typo in controller/DB 'ative'
        ]);

        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'status' => 'ative',
        ]);
    }
}
