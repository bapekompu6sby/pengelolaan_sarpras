<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Properties;
use App\Models\Transaction;
use App\Models\Kamar;
use App\Models\PropertiesImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;

/**
 * Unit Tests untuk model Properties.
 *
 * Menguji:
 * - Relasi hasMany Transaction
 * - Relasi hasMany Kamar
 * - Relasi hasMany PropertiesImage (images)
 * - Status typo 'ative' tersimpan benar
 * - Price disimpan sebagai string
 */
class PropertiesModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper: buat instance Properties.
     */
    private function createProperty(array $overrides = []): Properties
    {
        return Properties::create(array_merge([
            'name'     => 'Aula Serbaguna',
            'type'     => 'aula',
            'capacity' => 200,
            'price'    => '750000',
            'unit'     => 3,
            'status'   => 'ative',
        ], $overrides));
    }

    // ──────────────────────────────────────────────
    // Test: Relasi hasMany Transaction
    // ──────────────────────────────────────────────

    /**
     * Test bahwa Properties->transactions() mengembalikan Collection
     * dari Transaction yang terkait via property_id.
     */
    public function test_properties_has_many_transactions(): void
    {
        $property = $this->createProperty();

        // Buat 3 transaksi terkait
        foreach (range(1, 3) as $i) {
            Transaction::create([
                'name'         => "Pemesan $i",
                'instansi'     => "Instansi $i",
                'kegiatan'     => "Kegiatan $i",
                'start'        => "2025-09-0{$i}",
                'end'          => "2025-09-0{$i}",
                'property_id'  => $property->id,
                'status'       => 'pending',
                'affiliation'  => 'internal_pu', // gratis, agar tidak perlu kalkulasi rumit
                'phone_number' => '081234567890',
                'email'        => "pemesan{$i}@example.com",
                'ordered_unit' => 1,
            ]);
        }

        // Relasi harus mengembalikan Collection
        $this->assertInstanceOf(Collection::class, $property->transactions,
            'transactions() harus mengembalikan Eloquent Collection');

        // Harus ada 3 transaksi
        $this->assertCount(3, $property->transactions,
            'Harus ada 3 transaksi yang terasosiasi');

        // Setiap item harus instance Transaction
        $this->assertInstanceOf(Transaction::class, $property->transactions->first(),
            'Item dalam collection harus instance Transaction');
    }

    /**
     * Test bahwa Properties tanpa transaksi mengembalikan collection kosong.
     */
    public function test_properties_with_no_transactions_returns_empty_collection(): void
    {
        $property = $this->createProperty();

        $this->assertInstanceOf(Collection::class, $property->transactions);
        $this->assertCount(0, $property->transactions,
            'Properties tanpa transaksi harus return collection kosong');
    }

    // ──────────────────────────────────────────────
    // Test: Relasi hasMany Kamar
    // ──────────────────────────────────────────────

    /**
     * Test bahwa Properties->kamar() mengembalikan kamar-kamar
     * yang terkait via properties_id (FK di tabel kamar).
     */
    public function test_properties_has_many_kamar(): void
    {
        $property = $this->createProperty(['type' => 'asrama']);

        // Buat 2 kamar
        Kamar::create([
            'properties_id' => $property->id,
            'nama_kamar'    => 'Kamar 101',
            'kapasitas'     => 2,
            'lantai'        => 1,
        ]);
        Kamar::create([
            'properties_id' => $property->id,
            'nama_kamar'    => 'Kamar 102',
            'kapasitas'     => 3,
            'lantai'        => 1,
        ]);

        $this->assertCount(2, $property->kamar,
            'Harus ada 2 kamar yang terasosiasi');

        $this->assertInstanceOf(Kamar::class, $property->kamar->first(),
            'Item dalam collection harus instance Kamar');
    }

    /**
     * Test bahwa kamar dari property lain tidak ikut ter-load.
     */
    public function test_properties_kamar_only_returns_own_kamar(): void
    {
        $propertyA = $this->createProperty(['name' => 'Asrama A']);
        $propertyB = $this->createProperty(['name' => 'Asrama B']);

        Kamar::create([
            'properties_id' => $propertyA->id,
            'nama_kamar'    => 'A-101',
            'kapasitas'     => 2,
            'lantai'        => 1,
        ]);
        Kamar::create([
            'properties_id' => $propertyB->id,
            'nama_kamar'    => 'B-201',
            'kapasitas'     => 2,
            'lantai'        => 2,
        ]);

        // Property A hanya punya 1 kamar
        $this->assertCount(1, $propertyA->kamar);
        $this->assertEquals('A-101', $propertyA->kamar->first()->nama_kamar,
            'Hanya kamar milik property A yang dikembalikan');

        // Property B hanya punya 1 kamar
        $this->assertCount(1, $propertyB->kamar);
        $this->assertEquals('B-201', $propertyB->kamar->first()->nama_kamar,
            'Hanya kamar milik property B yang dikembalikan');
    }

    // ──────────────────────────────────────────────
    // Test: Relasi hasMany PropertiesImage
    // ──────────────────────────────────────────────

    /**
     * Test bahwa Properties->images() mengembalikan gambar-gambar gallery.
     */
    public function test_properties_has_many_images(): void
    {
        $property = $this->createProperty();

        PropertiesImage::create([
            'property_id' => $property->id,
            'image_path'  => 'gallery_image_1.jpg',
        ]);
        PropertiesImage::create([
            'property_id' => $property->id,
            'image_path'  => 'gallery_image_2.jpg',
        ]);

        $this->assertCount(2, $property->images,
            'Harus ada 2 gambar gallery yang terasosiasi');
    }

    // ──────────────────────────────────────────────
    // Test: Status typo 'ative'
    // ──────────────────────────────────────────────

    /**
     * Test bahwa status 'ative' (bukan 'active') tersimpan dan terbaca benar.
     * Ini BUKAN bug yang harus diperbaiki — ini nilai yang sudah dipakai
     * di seluruh production. Jangan ubah.
     */
    public function test_status_ative_is_stored_correctly(): void
    {
        $property = $this->createProperty(['status' => 'ative']);

        $this->assertEquals('ative', $property->status,
            'Status harus tersimpan sebagai "ative" (typo yang disengaja)');

        // Refresh dari DB
        $property->refresh();
        $this->assertEquals('ative', $property->status,
            'Status "ative" harus konsisten setelah refresh dari database');
    }

    /**
     * Test bahwa status 'inactive' juga bisa tersimpan.
     */
    public function test_status_inactive_is_stored_correctly(): void
    {
        $property = $this->createProperty(['status' => 'inactive']);

        $property->refresh();
        $this->assertEquals('inactive', $property->status,
            'Status "inactive" harus tersimpan dengan benar');
    }

    // ──────────────────────────────────────────────
    // Test: Price disimpan sebagai String
    // ──────────────────────────────────────────────

    /**
     * Test bahwa kolom price disimpan sebagai string di database
     * (sesuai migration yang menggunakan $table->string('price')).
     */
    public function test_price_is_stored_as_string(): void
    {
        $property = $this->createProperty(['price' => '1250000']);

        $property->refresh();

        // Price harus string (atau numeric string)
        $this->assertEquals('1250000', $property->price,
            'Price harus tersimpan sebagai string');
        $this->assertTrue(is_numeric($property->price),
            'Price harus bernilai numeric (meskipun tipenya string)');
    }

    // ──────────────────────────────────────────────
    // Test: Mass Assignment / Fillable
    // ──────────────────────────────────────────────

    /**
     * Test bahwa semua kolom $fillable bisa di-mass assign dan tersimpan benar.
     */
    public function test_properties_fillable_attributes(): void
    {
        $property = Properties::create([
            'name'       => 'Kelas Utama',
            'type'       => 'kelas',
            'capacity'   => 50,
            'room_type'  => 'VIP',
            'area'       => '120 m2',
            'facilities' => 'AC, Proyektor, Whiteboard',
            'price'      => '300000',
            'unit'       => 2,
            'status'     => 'ative',
        ]);

        $property->refresh();

        $this->assertEquals('Kelas Utama', $property->name);
        $this->assertEquals('kelas', $property->type);
        $this->assertEquals(50, $property->capacity);
        $this->assertEquals('VIP', $property->room_type);
        $this->assertEquals('120 m2', $property->area);
        $this->assertEquals('AC, Proyektor, Whiteboard', $property->facilities);
        $this->assertEquals('300000', $property->price);
        $this->assertEquals(2, $property->unit);
        $this->assertEquals('ative', $property->status);
    }
}
