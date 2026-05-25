<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Models\User;
use App\Models\Properties;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class TransactionIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $admin;
    private Properties $property;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'user']);
        $this->admin = User::factory()->create(['role' => 'admin']);

        $this->property = Properties::create([
            'name' => 'Aula Serbaguna',
            'type' => 'aula',
            'capacity' => 100,
            'price' => 500000,
            'unit' => 2,
            'status' => 'ative', // Typo on purpose as per requirements
        ]);
    }

    public function test_booking_store_saves_transaction_and_calculates_price()
    {
        Mail::fake();

        $response = $this->actingAs($this->user)->post(route('bookings.store'), [
            'name' => 'John Doe',
            'office' => 'PT Makmur',
            'event' => 'Rapat Tahunan',
            'start' => '2025-10-01',
            'end' => '2025-10-03',
            'jumlah_peserta' => 50,
            'venue' => $this->property->id,
            'affiliation' => 'external_pu',
            'ordered_unit' => 1,
            'email' => 'john@example.com',
            'phone_number' => '08123456789',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('transactions', [
            'name' => 'John Doe',
            'status' => 'pending',
            'property_id' => $this->property->id,
            'user_id' => $this->user->id,
        ]);

        $transaction = Transaction::where('email', 'john@example.com')->first();
        
        // Price Calculation: 500000 * 3 days * 1 unit = 1500000
        $this->assertEquals(1500000, $transaction->total_harga);
    }

    public function test_availability_check_returns_true_if_no_overlap()
    {
        $response = $this->actingAs($this->user)->postJson(route('properties.check'), [
            'venue_id' => $this->property->id,
            'start_date' => '2025-10-01',
            'end_date' => '2025-10-05',
            'ordered_unit' => 1,
            'property_type' => 'aula',
        ]);

        $response->assertOk()
                 ->assertJsonPath('available', true)
                 ->assertJsonPath('checked_type', 'aula');
    }

    public function test_availability_check_returns_false_if_overlap_approved()
    {
        Transaction::create([
            'name' => 'Existing',
            'instansi' => 'Test',
            'kegiatan' => 'Test',
            'start' => '2025-10-02',
            'end' => '2025-10-04',
            'property_id' => $this->property->id,
            'status' => 'approved',
            'ordered_unit' => 2,
            'affiliation' => 'internal_pu',
            'phone_number' => '0000',
            'email' => 'test@test.com',
            'total_harga' => 0,
        ]);

        $response = $this->actingAs($this->user)->postJson(route('properties.check'), [
            'venue_id' => $this->property->id,
            'start_date' => '2025-10-01',
            'end_date' => '2025-10-05',
            'ordered_unit' => 1,
            'property_type' => 'aula',
        ]);

        // Only 2 units available, 2 are taken, requesting 1 -> should fail
        $response->assertOk()
                 ->assertJsonPath('available', false)
                 ->assertJsonPath('avail_count', 0);
    }

    public function test_availability_check_handles_fasilitas_type()
    {
        $fasilitas = Properties::create([
            'name' => 'Lapangan Tenis',
            'type' => 'fasilitas',
            'capacity' => 10,
            'price' => 100000,
            'unit' => 1,
            'status' => 'ative',
        ]);

        Transaction::create([
            'name' => 'Player 1',
            'instansi' => 'Test',
            'kegiatan' => 'Tenis',
            'start' => '2025-10-01',
            'end' => '2025-10-01',
            'jam_start' => '08:00',
            'jam_end' => '10:00',
            'property_id' => $fasilitas->id,
            'status' => 'approved',
            'ordered_unit' => 1,
            'affiliation' => 'internal_pu',
            'phone_number' => '0000',
            'email' => 'test@test.com',
            'total_harga' => 0,
        ]);

        // Conflict check
        $responseConflict = $this->actingAs($this->user)->postJson(route('properties.check'), [
            'venue_id' => $fasilitas->id,
            'start_date' => '2025-10-01',
            'end_date' => '2025-10-01',
            'jam_start' => '09:00',
            'jam_end' => '11:00',
            'ordered_unit' => 1,
            'property_type' => 'fasilitas',
        ]);

        $responseConflict->assertOk()->assertJsonPath('available', false);

        // No conflict check
        $responseNoConflict = $this->actingAs($this->user)->postJson(route('properties.check'), [
            'venue_id' => $fasilitas->id,
            'start_date' => '2025-10-01',
            'end_date' => '2025-10-01',
            'jam_start' => '11:00',
            'jam_end' => '13:00',
            'ordered_unit' => 1,
            'property_type' => 'fasilitas',
        ]);

        $responseNoConflict->assertOk()->assertJsonPath('available', true);
    }

    public function test_admin_approval_changes_status_to_approved()
    {
        $transaction = Transaction::create([
            'name' => 'John Doe',
            'instansi' => 'PT Makmur',
            'kegiatan' => 'Rapat',
            'start' => '2025-10-01',
            'end' => '2025-10-02',
            'property_id' => $this->property->id,
            'status' => 'pending',
            'ordered_unit' => 1,
            'affiliation' => 'internal_pu',
            'user_id' => $this->user->id,
            'phone_number' => '123',
            'email' => 'a@b.c',
            'total_harga' => 0,
        ]);

        $response = $this->actingAs($this->admin)->post(route('transactions.update', $transaction->id), [
            'user_id' => $this->user->id,
            'office' => 'PT Makmur',
            'event' => 'Rapat',
            'start' => '2025-10-01',
            'end' => '2025-10-02',
            'ruangan_id' => $this->property->id,
            'status' => 'approved',
            'ordered_unit' => 1,
            'affiliation' => 'internal_pu',
            'phone_number' => '123',
            'email' => 'a@b.c',
            'total_harga' => 0,
            'jumlah_peserta' => 10,
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'approved',
        ]);
    }

    public function test_admin_rejection_requires_reason()
    {
        $transaction = Transaction::create([
            'name' => 'John Doe',
            'instansi' => 'PT Makmur',
            'kegiatan' => 'Rapat',
            'start' => '2025-10-01',
            'end' => '2025-10-02',
            'property_id' => $this->property->id,
            'status' => 'pending',
            'ordered_unit' => 1,
            'affiliation' => 'internal_pu',
            'user_id' => $this->user->id,
            'phone_number' => '123',
            'email' => 'a@b.c',
            'total_harga' => 0,
        ]);

        $response = $this->actingAs($this->admin)->post(route('transactions.update', $transaction->id), [
            'user_id' => $this->user->id,
            'office' => 'PT Makmur',
            'event' => 'Rapat',
            'start' => '2025-10-01',
            'end' => '2025-10-02',
            'ruangan_id' => $this->property->id,
            'status' => 'rejected',
            'ordered_unit' => 1,
            'affiliation' => 'internal_pu',
            'phone_number' => '123',
            'email' => 'a@b.c',
            'total_harga' => 0,
            'jumlah_peserta' => 10,
        ]);

        $response->assertSessionHasErrors('rejection_reason');

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'pending',
        ]);
    }

    public function test_admin_approval_rollback_on_error()
    {
        $transaction = Transaction::create([
            'name' => 'John Doe',
            'instansi' => 'PT Makmur',
            'kegiatan' => 'Rapat',
            'start' => '2025-10-01',
            'end' => '2025-10-02',
            'property_id' => $this->property->id,
            'status' => 'pending',
            'ordered_unit' => 1,
            'affiliation' => 'internal_pu',
            'user_id' => $this->user->id,
            'phone_number' => '123',
            'email' => 'a@b.c',
            'total_harga' => 0,
        ]);

        // Simulating error by sending billing_code longer than DB schema allows (100 chars)
        $response = $this->actingAs($this->admin)->post(route('transactions.update', $transaction->id), [
            'user_id' => $this->user->id,
            'office' => 'PT Makmur',
            'event' => 'Rapat',
            'start' => '2025-10-01',
            'end' => '2025-10-02',
            'ruangan_id' => $this->property->id,
            'status' => 'approved',
            'ordered_unit' => 1,
            'affiliation' => 'internal_pu',
            'phone_number' => '123',
            'email' => 'a@b.c',
            'total_harga' => 0,
            'jumlah_peserta' => 10,
            'billing_code' => str_repeat('A', 150), // Over 100 chars
        ]);

        $response->assertSessionHas('failed');

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'pending',
        ]);
    }

    public function test_file_upload_payment_receipt_saved_correctly()
    {
        Storage::fake('local');
        
        $transaction = Transaction::create([
            'name' => 'John Doe',
            'instansi' => 'PT Makmur',
            'kegiatan' => 'Rapat',
            'start' => '2025-10-01',
            'end' => '2025-10-02',
            'property_id' => $this->property->id,
            'status' => 'waiting_payment',
            'ordered_unit' => 1,
            'affiliation' => 'external_pu',
            'user_id' => $this->user->id,
            'phone_number' => '123',
            'email' => 'a@b.c',
            'total_harga' => 500000,
        ]);

        $file = UploadedFile::fake()->image('receipt.jpg');

        $response = $this->actingAs($this->user)->post(route('transactions.payment', $transaction->id), [
            'payment_receipt' => $file,
        ]);

        $response->assertSessionHas('success');

        Storage::disk('local')->assertExists('private_uploads/payment_receipt/' . $file->hashName());

        $transaction->refresh();

        $this->assertEquals($file->hashName(), $transaction->payment_receipt);
        $this->assertStringNotContainsString('private_uploads', $transaction->payment_receipt);
    }
}
