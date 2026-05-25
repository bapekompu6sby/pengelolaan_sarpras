<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\DetailKamarTransaction;
use App\Models\Transaction;
use App\Models\Properties;
use App\Models\Kamar;
use App\Models\Penghuni;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;

/**
 * Unit Tests untuk model DetailKamarTransaction dan Penghuni.
 *
 * Menguji:
 * - DetailKamarTransaction belongsTo Transaction
 * - DetailKamarTransaction belongsTo Kamar
 * - DetailKamarTransaction hasMany Penghuni
 * - Penghuni belongsTo DetailKamarTransaction
 * - Full data chain: Property → Transaction → DetailKamar → Penghuni
 */
class DetailKamarTransactionModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper: buat data chain dasar (property + transaction + kamar).
     */
    private function createBaseData(): array
    {
        $property = Properties::create([
            'name'     => 'Asrama Selatan',
            'type'     => 'asrama',
            'capacity' => 40,
            'price'    => '150000',
            'unit'     => 10,
            'status'   => 'ative',
        ]);

        $transaction = Transaction::create([
            'name'         => 'Test Pemesan',
            'instansi'     => 'Test Instansi',
            'kegiatan'     => 'Pelatihan',
            'start'        => '2025-09-01',
            'end'          => '2025-09-03',
            'property_id'  => $property->id,
            'status'       => 'approved',
            'affiliation'  => 'internal_pu',
            'phone_number' => '081234567890',
            'email'        => 'test@example.com',
            'ordered_unit' => 3,
        ]);

        $kamar = Kamar::create([
            'properties_id' => $property->id,
            'nama_kamar'    => 'Kamar 101',
            'kapasitas'     => 4,
            'lantai'        => 1,
        ]);

        return compact('property', 'transaction', 'kamar');
    }

    // ──────────────────────────────────────────────
    // Test: belongsTo Transaction
    // ──────────────────────────────────────────────

    /**
     * Test bahwa DetailKamarTransaction->transaction() mengembalikan
     * instance Transaction yang benar.
     */
    public function test_detail_belongs_to_transaction(): void
    {
        ['transaction' => $tx, 'kamar' => $kamar] = $this->createBaseData();

        $detail = DetailKamarTransaction::create([
            'transaction_id' => $tx->id,
            'kamar_id'       => $kamar->id,
            'start'          => '2025-09-01',
            'end'            => '2025-09-03',
        ]);

        $this->assertInstanceOf(Transaction::class, $detail->transaction,
            'Relasi transaction() harus return instance Transaction');
        $this->assertEquals($tx->id, $detail->transaction->id);
    }

    // ──────────────────────────────────────────────
    // Test: belongsTo Kamar
    // ──────────────────────────────────────────────

    /**
     * Test bahwa DetailKamarTransaction->kamar() mengembalikan
     * instance Kamar yang benar.
     */
    public function test_detail_belongs_to_kamar(): void
    {
        ['transaction' => $tx, 'kamar' => $kamar] = $this->createBaseData();

        $detail = DetailKamarTransaction::create([
            'transaction_id' => $tx->id,
            'kamar_id'       => $kamar->id,
            'start'          => '2025-09-01',
            'end'            => '2025-09-03',
        ]);

        $this->assertInstanceOf(Kamar::class, $detail->kamar,
            'Relasi kamar() harus return instance Kamar');
        $this->assertEquals($kamar->id, $detail->kamar->id);
        $this->assertEquals('Kamar 101', $detail->kamar->nama_kamar);
    }

    // ──────────────────────────────────────────────
    // Test: hasMany Penghuni
    // ──────────────────────────────────────────────

    /**
     * Test bahwa DetailKamarTransaction->penghunis() mengembalikan
     * penghuni-penghuni yang terkait.
     */
    public function test_detail_has_many_penghunis(): void
    {
        ['transaction' => $tx, 'kamar' => $kamar] = $this->createBaseData();

        $detail = DetailKamarTransaction::create([
            'transaction_id' => $tx->id,
            'kamar_id'       => $kamar->id,
            'start'          => '2025-09-01',
            'end'            => '2025-09-03',
        ]);

        // Tambah 2 penghuni
        Penghuni::create([
            'detail_kamar_transaction_id' => $detail->id,
            'nama_penghuni'               => 'Ahmad Rizky',
        ]);
        Penghuni::create([
            'detail_kamar_transaction_id' => $detail->id,
            'nama_penghuni'               => 'Siti Nurhaliza',
        ]);

        $this->assertCount(2, $detail->penghunis,
            'Harus ada 2 penghuni yang terasosiasi');
        $this->assertInstanceOf(Penghuni::class, $detail->penghunis->first());
    }

    // ──────────────────────────────────────────────
    // Test: Penghuni belongsTo DetailKamarTransaction
    // ──────────────────────────────────────────────

    /**
     * Test bahwa Penghuni->detail() mengembalikan DetailKamarTransaction
     * yang benar (relasi inverse).
     */
    public function test_penghuni_belongs_to_detail(): void
    {
        ['transaction' => $tx, 'kamar' => $kamar] = $this->createBaseData();

        $detail = DetailKamarTransaction::create([
            'transaction_id' => $tx->id,
            'kamar_id'       => $kamar->id,
            'start'          => '2025-09-01',
            'end'            => '2025-09-03',
        ]);

        $penghuni = Penghuni::create([
            'detail_kamar_transaction_id' => $detail->id,
            'nama_penghuni'               => 'Rudi Hartono',
        ]);

        $this->assertInstanceOf(DetailKamarTransaction::class, $penghuni->detail,
            'Relasi detail() harus return instance DetailKamarTransaction');
        $this->assertEquals($detail->id, $penghuni->detail->id);
    }

    // ──────────────────────────────────────────────
    // Test: Full data chain
    // ──────────────────────────────────────────────

    /**
     * Test full chain: Property → Transaction → DetailKamar → Penghuni.
     * Memastikan seluruh relasi bisa di-traverse dari ujung ke ujung.
     */
    public function test_full_data_chain_traversal(): void
    {
        ['property' => $property, 'transaction' => $tx, 'kamar' => $kamar] = $this->createBaseData();

        $detail = DetailKamarTransaction::create([
            'transaction_id' => $tx->id,
            'kamar_id'       => $kamar->id,
            'start'          => '2025-09-01',
            'end'            => '2025-09-03',
        ]);

        $penghuni = Penghuni::create([
            'detail_kamar_transaction_id' => $detail->id,
            'nama_penghuni'               => 'Full Chain User',
        ]);

        // Traverse: Penghuni → Detail → Transaction → Properties
        $traversedProperty = $penghuni->detail->transaction->properties;

        $this->assertInstanceOf(Properties::class, $traversedProperty,
            'Full chain traversal harus berhasil sampai Properties');
        $this->assertEquals($property->id, $traversedProperty->id,
            'Property yang di-traverse harus sama dengan yang dibuat');

        // Traverse: Penghuni → Detail → Kamar → Properties
        $kamarProperty = $penghuni->detail->kamar->properties;

        $this->assertEquals($property->id, $kamarProperty->id,
            'Kamar juga harus merujuk ke Properties yang sama');
    }

    // ──────────────────────────────────────────────
    // Test: Custom table name
    // ──────────────────────────────────────────────

    /**
     * Test bahwa model menggunakan nama tabel yang benar (custom, bukan convention).
     */
    public function test_uses_correct_table_names(): void
    {
        $detail = new DetailKamarTransaction();
        $this->assertEquals('detail_kamar_transaction', $detail->getTable(),
            'DetailKamarTransaction harus menggunakan tabel "detail_kamar_transaction"');

        $penghuni = new Penghuni();
        $this->assertEquals('penghuni', $penghuni->getTable(),
            'Penghuni harus menggunakan tabel "penghuni"');
    }
}
