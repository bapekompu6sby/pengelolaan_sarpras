<?php

namespace Tests\Feature;

use App\Models\Properties;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class BookingFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Base seed or properties for booking tests
    }

    public function test_user_can_view_booking_page()
    {
        $user = User::factory()->create(['role' => 'user']);
        $response = $this->actingAs($user)->get(route('bookings'));
        $response->assertStatus(200);
    }

    public function test_user_can_submit_valid_booking()
    {
        $user = User::factory()->create(['role' => 'user']);
        $property = Properties::create([
            'name' => 'Aula Gedung',
            'type' => 'aula',
            'capacity' => 100,
            'price' => 1000000,
            'unit' => 1,
        ]);

        $response = $this->actingAs($user)->post(route('bookings.store'), [
            'name' => 'John Doe',
            'office' => 'PT XYZ',
            'event' => 'Seminar',
            'start' => Carbon::now()->addDays(1)->format('Y-m-d'),
            'end' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'jumlah_peserta' => 50,
            'venue' => $property->id,
            'affiliation' => 'external_pu',
            'ordered_unit' => 1,
            'email' => 'john@example.com',
            'phone_number' => '081234567890',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('transactions', [
            'property_id' => $property->id,
            'status' => 'pending',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_submit_booking_without_required_fields()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->post(route('bookings.store'), [
            'name' => 'John Doe',
            // Missing office, event, start, end, venue, affiliation, ordered_unit, etc.
        ]);

        $response->assertSessionHasErrors(['office', 'event', 'start', 'end', 'venue', 'affiliation', 'ordered_unit']);
        $this->assertDatabaseEmpty('transactions');
    }

    public function test_user_can_cancel_own_transaction()
    {
        $user = User::factory()->create(['role' => 'user']);
        $property = Properties::create([
            'name' => 'Aula',
            'type' => 'aula',
            'capacity' => 10,
            'unit' => 1
        ]);
        
        $transaction = Transaction::create([
            'name' => 'John',
            'instansi' => 'XYZ',
            'kegiatan' => 'Event',
            'start' => Carbon::now()->addDays(1)->format('Y-m-d'),
            'end' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'property_id' => $property->id,
            'user_id' => $user->id,
            'status' => 'pending',
            'affiliation' => 'external_pu',
            'ordered_unit' => 1,
            'jumlah_peserta' => 10,
            'total_harga' => 1000,
            'phone_number' => '08123456789',
            'email' => 'john@test.com'
        ]);

        $response = $this->actingAs($user)->post(route('transactions.cancel', $transaction->id));
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'cancelled'
        ]);
    }

    public function test_user_cannot_cancel_others_transaction()
    {
        $user1 = User::factory()->create(['role' => 'user']);
        $user2 = User::factory()->create(['role' => 'user']);
        
        $property = Properties::create([
            'name' => 'Aula',
            'type' => 'aula',
            'capacity' => 10,
            'unit' => 1
        ]);

        $transaction = Transaction::create([
            'name' => 'Jane',
            'instansi' => 'ABC',
            'kegiatan' => 'Event 2',
            'start' => Carbon::now()->addDays(1)->format('Y-m-d'),
            'end' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'property_id' => $property->id,
            'user_id' => $user2->id, // Owned by user2
            'status' => 'pending',
            'affiliation' => 'external_pu',
            'ordered_unit' => 1,
            'jumlah_peserta' => 10,
            'total_harga' => 1000,
            'phone_number' => '08123456789',
            'email' => 'jane@test.com'
        ]);

        // user1 trying to cancel user2's transaction
        $response = $this->actingAs($user1)->post(route('transactions.cancel', $transaction->id));
        
        $response->assertStatus(404); // firstOrFail throws 404 Exception
        
        // Status remains pending
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'pending'
        ]);
    }
}
