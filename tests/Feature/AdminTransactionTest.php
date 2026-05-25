<?php

namespace Tests\Feature;

use App\Models\Properties;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AdminTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_all_transactions()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get(route('transactions'));
        $response->assertStatus(200);
    }

    public function test_admin_can_approve_transaction()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        
        $property = Properties::create([
            'name' => 'Aula Gedung',
            'type' => 'aula',
            'capacity' => 100,
            'price' => 1000000,
            'unit' => 1,
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
            'phone_number' => '08123',
            'email' => 'john@test.com',
            'ordered_unit' => 1,
            'jumlah_peserta' => 10,
            'total_harga' => 1000,
        ]);

        $response = $this->actingAs($admin)->post(route('transactions.update', $transaction->id), [
            'user_id' => $user->id,
            'office' => 'XYZ',
            'event' => 'Event Update',
            'start' => $transaction->start,
            'end' => $transaction->end,
            'status' => 'approved', // approve!
            'affiliation' => 'external_pu',
            'phone_number' => '08123',
            'email' => 'john@test.com',
            'ordered_unit' => 1,
            'jumlah_peserta' => 10,
            'total_harga' => 1000,
            'ruangan_id' => $property->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'approved',
            'kegiatan' => 'Event Update'
        ]);
    }

    public function test_admin_reject_transaction_without_reason_fails()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        
        $property = Properties::create([
            'name' => 'Aula Gedung',
            'type' => 'aula',
            'capacity' => 100,
            'price' => 1000000,
            'unit' => 1,
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
            'phone_number' => '08123',
            'email' => 'john@test.com',
            'ordered_unit' => 1,
            'jumlah_peserta' => 10,
            'total_harga' => 1000,
        ]);

        $response = $this->actingAs($admin)->post(route('transactions.update', $transaction->id), [
            'user_id' => $user->id,
            'office' => 'XYZ',
            'event' => 'Event',
            'start' => $transaction->start,
            'end' => $transaction->end,
            'status' => 'rejected', // reject but no reason
            'affiliation' => 'external_pu',
            'phone_number' => '08123',
            'email' => 'john@test.com',
            'ordered_unit' => 1,
            'jumlah_peserta' => 10,
            'total_harga' => 1000,
            'ruangan_id' => $property->id,
            'rejection_reason' => '',
        ]);

        $response->assertSessionHasErrors('rejection_reason');
        
        // Status remains unchanged
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'pending',
        ]);
    }

    public function test_admin_reject_transaction_with_reason()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        
        $property = Properties::create([
            'name' => 'Aula Gedung',
            'type' => 'aula',
            'capacity' => 100,
            'price' => 1000000,
            'unit' => 1,
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
            'phone_number' => '08123',
            'email' => 'john@test.com',
            'ordered_unit' => 1,
            'jumlah_peserta' => 10,
            'total_harga' => 1000,
        ]);

        $response = $this->actingAs($admin)->post(route('transactions.update', $transaction->id), [
            'user_id' => $user->id,
            'office' => 'XYZ',
            'event' => 'Event',
            'start' => $transaction->start,
            'end' => $transaction->end,
            'status' => 'rejected', // reject with reason
            'rejection_reason' => 'Jadwal Penuh',
            'affiliation' => 'external_pu',
            'phone_number' => '08123',
            'email' => 'john@test.com',
            'ordered_unit' => 1,
            'jumlah_peserta' => 10,
            'total_harga' => 1000,
            'ruangan_id' => $property->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'rejected',
            'rejection_reason' => 'Jadwal Penuh'
        ]);
    }
}
