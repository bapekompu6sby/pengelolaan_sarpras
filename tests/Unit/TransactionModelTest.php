<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Transaction;
use App\Models\Properties;
use App\Models\User;
use App\Models\Kamar;
use App\Models\DetailKamarTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Unit Tests untuk model Transaction.
 *
 * Menguji:
 * - booted() auto-calculation total_harga
 * - Relasi belongsTo Properties dan User
 * - Relasi hasMany DetailKamarTransaction (duplikat: detailKamars + detailKamarTransactions)
 */
class TransactionModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper: buat instance Properties dengan data default.
     */
    private function createProperty(array $overrides = []): Properties
    {
        return Properties::create(array_merge([
            'name'     => 'Aula Utama',
            'type'     => 'aula',
            'capacity' => 100,
            'price'    => '500000',   // string, sesuai migration
            'unit'     => 5,
            'status'   => 'ative',    // typo sesuai kode asli — JANGAN diubah
        ], $overrides));
    }

    /**
     * Helper: buat instance Transaction dengan data default (external_pu).
     */
    private function createTransaction(Properties $property, array $overrides = []): Transaction
    {
        // Pastikan selalu ada user_id default karena di schema (migration) tidak nullable
        $userId = $overrides['user_id'] ?? User::factory()->create()->id;

        return Transaction::create(array_merge([
            'name'         => 'Budi Santoso',
            'instansi'     => 'Dinas PU Jatim',
            'kegiatan'     => 'Pelatihan Teknis',
            'start'        => '2025-09-01',
            'end'          => '2025-09-03',       // 3 hari (1 Sep, 2 Sep, 3 Sep)
            'property_id'  => $property->id,
            'status'       => 'pending',
            'affiliation'  => 'external_pu',
            'phone_number' => '081234567890',
            'email'        => 'budi@example.com',
            'ordered_unit' => 2,
            'user_id'      => $userId,
        ], $overrides));
    }

    // ──────────────────────────────────────────────
    // Test: booted() — Auto Calculate total_harga
    // ──────────────────────────────────────────────

    /**
     * Test bahwa booted() menghitung total_harga = price * duration (hari)
     * untuk afiliasi external_pu. Durasi = diff(start, end).days + 1.
     *
     * Contoh: start=2025-09-01, end=2025-09-03 → 3 hari, price=500000
     * total_harga = 500000 * 3 = 1500000
     */
    public function test_booted_calculates_total_harga_for_external_affiliation(): void
    {
        $property = $this->createProperty(['price' => '500000']);

        $transaction = $this->createTransaction($property, [
            'start'       => '2025-09-01',
            'end'         => '2025-09-03',
            'affiliation' => 'external_pu',
        ]);

        // Refresh dari DB karena booted() melakukan save() terpisah
        $transaction->refresh();

        // Durasi = diff(1 Sep, 3 Sep).days + 1 = 2 + 1 = 3 hari
        // Total  = 500000 * 3 = 1500000
        $this->assertEquals(1500000, $transaction->total_harga,
            'total_harga harus = price * (diff_days + 1) untuk external_pu');
    }

    /**
     * Test bahwa booted() menghasilkan total_harga = 0 untuk afiliasi internal_pu.
     * Sesuai logika: internal_pu selalu gratis.
     */
    public function test_booted_sets_total_harga_zero_for_internal_affiliation(): void
    {
        $property = $this->createProperty(['price' => '500000']);

        $transaction = $this->createTransaction($property, [
            'affiliation' => 'internal_pu',
        ]);

        $transaction->refresh();

        $this->assertEquals(0, $transaction->total_harga,
            'total_harga harus 0 untuk internal_pu (gratis)');
    }

    /**
     * Test booted() dengan durasi 1 hari (start == end).
     * Durasi = diff(date, date).days + 1 = 0 + 1 = 1 hari.
     */
    public function test_booted_calculates_total_harga_for_single_day_booking(): void
    {
        $property = $this->createProperty(['price' => '200000']);

        $transaction = $this->createTransaction($property, [
            'start'       => '2025-09-01',
            'end'         => '2025-09-01',
            'affiliation' => 'external_pu',
        ]);

        $transaction->refresh();

        // Durasi = 0 + 1 = 1 hari → total = 200000 * 1 = 200000
        $this->assertEquals(200000, $transaction->total_harga,
            'Booking 1 hari (start==end) harus dihitung sebagai 1 hari');
    }

    /**
     * Test bahwa price bertipe string di Properties tetap bisa dihitung
     * (PHP auto-casts string numeric ke number dalam operasi aritmatika).
     */
    public function test_booted_handles_string_price_correctly(): void
    {
        $property = $this->createProperty(['price' => '750000']); // string

        $transaction = $this->createTransaction($property, [
            'start'       => '2025-09-10',
            'end'         => '2025-09-12',   // 3 hari
            'affiliation' => 'external_pu',
        ]);

        $transaction->refresh();

        // 750000 * 3 = 2250000
        $this->assertEquals(2250000, $transaction->total_harga,
            'String price harus tetap bisa dikalkulasi secara aritmatika');
    }

    /**
     * Test bahwa total_harga = 0 ketika end < start (edge case invalid range).
     * Sesuai logika di calculateTotalPrice(): if end < start → total = 0.
     */
    public function test_booted_sets_zero_when_end_before_start(): void
    {
        $property = $this->createProperty(['price' => '500000']);

        $transaction = $this->createTransaction($property, [
            'start'       => '2025-09-05',
            'end'         => '2025-09-01',   // end < start
            'affiliation' => 'external_pu',
        ]);

        $transaction->refresh();

        $this->assertEquals(0, $transaction->total_harga,
            'total_harga harus 0 ketika end < start (invalid range)');
    }

    /**
     * Test bahwa price = '0' menghasilkan total_harga = 0
     * walaupun booking external_pu.
     */
    public function test_booted_calculates_zero_when_price_is_zero_string(): void
    {
        $property = $this->createProperty(['price' => '0']);

        $transaction = $this->createTransaction($property, [
            'start'       => '2025-09-01',
            'end'         => '2025-09-05',
            'affiliation' => 'external_pu',
        ]);

        $transaction->refresh();

        $this->assertEquals(0, $transaction->total_harga,
            'total_harga harus 0 ketika price properti = 0');
    }

    /**
     * Test perhitungan durasi panjang (multi-minggu).
     * Start=2025-09-01, End=2025-09-30 → 30 hari.
     */
    public function test_booted_calculates_long_duration_correctly(): void
    {
        $property = $this->createProperty(['price' => '100000']);

        $transaction = $this->createTransaction($property, [
            'start'       => '2025-09-01',
            'end'         => '2025-09-30',
            'affiliation' => 'external_pu',
        ]);

        $transaction->refresh();

        // Durasi = 29 + 1 = 30 hari → total = 100000 * 30 = 3000000
        $this->assertEquals(3000000, $transaction->total_harga,
            'Durasi 30 hari harus menghasilkan price * 30');
    }

    // ──────────────────────────────────────────────
    // Test: Relasi belongsTo Properties
    // ──────────────────────────────────────────────

    /**
     * Test bahwa Transaction->properties() mengembalikan instance Properties yang benar.
     */
    public function test_transaction_belongs_to_properties(): void
    {
        $property = $this->createProperty(['name' => 'Paviliun VIP']);

        $transaction = $this->createTransaction($property);

        // Relasi properties() harus mengembalikan instance Properties
        $this->assertInstanceOf(Properties::class, $transaction->properties,
            'Relasi properties() harus return instance Properties');

        // ID harus cocok
        $this->assertEquals($property->id, $transaction->properties->id,
            'property_id pada Transaction harus sesuai dengan Properties.id');

        // Nama harus cocok
        $this->assertEquals('Paviliun VIP', $transaction->properties->name,
            'Nama Properties harus bisa diakses via relasi');
    }

    // ──────────────────────────────────────────────
    // Test: Relasi belongsTo User
    // ──────────────────────────────────────────────

    /**
     * Test bahwa Transaction->user() mengembalikan instance User yang benar.
     */
    public function test_transaction_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $property = $this->createProperty();

        $transaction = $this->createTransaction($property, [
            'user_id' => $user->id,
        ]);

        // Relasi user() harus mengembalikan instance User
        $this->assertInstanceOf(User::class, $transaction->user,
            'Relasi user() harus return instance User');

        $this->assertEquals($user->id, $transaction->user->id,
            'user_id pada Transaction harus sesuai dengan User.id');
    }

    /**
     * Test bahwa Transaction tanpa user_id tetap valid (nullable).
     */
    public function test_transaction_user_relation_is_nullable(): void
    {
        $property = $this->createProperty();

        $transaction = $this->createTransaction($property, [
            'user_id' => null,
        ]);

        $this->assertNull($transaction->user,
            'Relasi user() harus null jika user_id tidak diisi');
    }

    // ──────────────────────────────────────────────
    // Test: Relasi hasMany DetailKamarTransaction (duplikat)
    // ──────────────────────────────────────────────

    /**
     * Test bahwa detailKamars() dan detailKamarTransactions() keduanya bekerja
     * dan mengembalikan hasil yang sama (duplikat relasi).
     */
    public function test_detail_kamars_and_detail_kamar_transactions_return_same_result(): void
    {
        $property = $this->createProperty(['type' => 'asrama']);
        $transaction = $this->createTransaction($property);

        // Buat kamar terkait property
        $kamar = Kamar::create([
            'properties_id' => $property->id,
            'nama_kamar'    => 'Kamar 101',
            'kapasitas'     => 2,
            'lantai'        => 1,
        ]);

        // Buat detail kamar transaction
        DetailKamarTransaction::create([
            'transaction_id' => $transaction->id,
            'kamar_id'       => $kamar->id,
            'start'          => '2025-09-01',
            'end'            => '2025-09-03',
        ]);

        // Kedua relasi harus mengembalikan jumlah yang sama
        $this->assertCount(1, $transaction->detailKamars,
            'detailKamars() harus mengembalikan 1 record');
        $this->assertCount(1, $transaction->detailKamarTransactions,
            'detailKamarTransactions() harus mengembalikan 1 record (sama)');

        // ID record harus sama
        $this->assertEquals(
            $transaction->detailKamars->first()->id,
            $transaction->detailKamarTransactions->first()->id,
            'Kedua relasi duplikat harus mengembalikan record yang identik'
        );
    }

    // ──────────────────────────────────────────────
    // Test: Mass Assignment / Fillable
    // ──────────────────────────────────────────────

    /**
     * Test bahwa semua kolom dalam $fillable bisa di-mass assign.
     */
    public function test_transaction_fillable_attributes_are_set(): void
    {
        $property = $this->createProperty();

        $data = [
            'name'             => 'Test User',
            'instansi'         => 'Test Instansi',
            'kegiatan'         => 'Test Kegiatan',
            'start'            => '2025-10-01',
            'end'              => '2025-10-02',
            'property_id'      => $property->id,
            'status'           => 'pending',
            'affiliation'      => 'external_pu',
            'phone_number'     => '0812345',
            'email'            => 'test@mail.com',
            'ordered_unit'     => 3,
            'description'      => 'Deskripsi kegiatan',
            'rejection_reason' => null,
        ];

        $transaction = Transaction::create($data);
        $transaction->refresh();

        $this->assertEquals('Test User', $transaction->name);
        $this->assertEquals('Test Instansi', $transaction->instansi);
        $this->assertEquals('Test Kegiatan', $transaction->kegiatan);
        $this->assertEquals('pending', $transaction->status);
        $this->assertEquals(3, $transaction->ordered_unit);
        $this->assertEquals('Deskripsi kegiatan', $transaction->description);
    }
}
