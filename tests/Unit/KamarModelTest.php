<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Kamar;
use App\Models\Properties;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Unit Tests untuk model Kamar.
 *
 * Menguji:
 * - Relasi belongsTo Properties (via properties() dan ruangan())
 * - Mass assignment / fillable
 * - Default kapasitas
 */
class KamarModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper: buat instance Properties.
     */
    private function createProperty(array $overrides = []): Properties
    {
        return Properties::create(array_merge([
            'name'     => 'Asrama Putra',
            'type'     => 'asrama',
            'capacity' => 50,
            'price'    => '150000',
            'unit'     => 10,
            'status'   => 'ative',
        ], $overrides));
    }

    // ──────────────────────────────────────────────
    // Test: Relasi belongsTo Properties (via properties())
    // ──────────────────────────────────────────────

    /**
     * Test bahwa Kamar->properties() mengembalikan instance Properties
     * yang benar berdasarkan FK properties_id.
     */
    public function test_kamar_belongs_to_properties(): void
    {
        $property = $this->createProperty(['name' => 'Asrama Utara']);

        $kamar = Kamar::create([
            'properties_id' => $property->id,
            'nama_kamar'    => 'Kamar 301',
            'kapasitas'     => 4,
            'lantai'        => 3,
        ]);

        // Relasi properties() harus return instance Properties
        $this->assertInstanceOf(Properties::class, $kamar->properties,
            'Relasi properties() harus return instance Properties');

        // ID harus cocok
        $this->assertEquals($property->id, $kamar->properties->id,
            'properties_id pada Kamar harus sesuai dengan Properties.id');

        // Nama harus bisa diakses
        $this->assertEquals('Asrama Utara', $kamar->properties->name,
            'Nama Properties harus bisa diakses via relasi properties()');
    }

    // ──────────────────────────────────────────────
    // Test: Relasi belongsTo Properties (via ruangan())
    // ──────────────────────────────────────────────

    /**
     * Test bahwa Kamar->ruangan() juga mengembalikan instance Properties.
     * Relasi ruangan() adalah alias dari properties() (sama-sama belongsTo
     * Properties via properties_id).
     */
    public function test_kamar_ruangan_relation_is_alias_of_properties(): void
    {
        $property = $this->createProperty();

        $kamar = Kamar::create([
            'properties_id' => $property->id,
            'nama_kamar'    => 'Kamar 101',
            'kapasitas'     => 2,
            'lantai'        => 1,
        ]);

        // Relasi ruangan() juga harus return instance Properties
        $this->assertInstanceOf(Properties::class, $kamar->ruangan,
            'Relasi ruangan() harus return instance Properties (alias)');

        // Keduanya harus mengembalikan ID yang sama
        $this->assertEquals(
            $kamar->properties->id,
            $kamar->ruangan->id,
            'properties() dan ruangan() harus mengembalikan Properties yang sama'
        );
    }

    // ──────────────────────────────────────────────
    // Test: Mass Assignment / Fillable
    // ──────────────────────────────────────────────

    /**
     * Test bahwa semua kolom $fillable (properties_id, nama_kamar, kapasitas, lantai)
     * bisa di-mass assign dan tersimpan dengan benar.
     */
    public function test_kamar_fillable_attributes(): void
    {
        $property = $this->createProperty();

        $kamar = Kamar::create([
            'properties_id' => $property->id,
            'nama_kamar'    => 'Kamar Deluxe 501',
            'kapasitas'     => 6,
            'lantai'        => 5,
        ]);

        $kamar->refresh();

        $this->assertEquals($property->id, $kamar->properties_id);
        $this->assertEquals('Kamar Deluxe 501', $kamar->nama_kamar);
        $this->assertEquals(6, $kamar->kapasitas);
        $this->assertEquals(5, $kamar->lantai);
    }

    /**
     * Test bahwa tabel yang digunakan adalah 'kamar' (bukan default 'kamars').
     * Karena $table = 'kamar' di model.
     */
    public function test_kamar_uses_correct_table_name(): void
    {
        $kamar = new Kamar();

        $this->assertEquals('kamar', $kamar->getTable(),
            'Model Kamar harus menggunakan tabel "kamar" (bukan "kamars")');
    }

    // ──────────────────────────────────────────────
    // Test: Multiple kamar untuk satu property
    // ──────────────────────────────────────────────

    /**
     * Test bahwa multiple kamar bisa dibuat untuk satu property,
     * dan masing-masing mengacu ke property yang sama.
     */
    public function test_multiple_kamar_belong_to_same_property(): void
    {
        $property = $this->createProperty();

        $kamar1 = Kamar::create([
            'properties_id' => $property->id,
            'nama_kamar'    => 'Kamar 101',
            'kapasitas'     => 2,
            'lantai'        => 1,
        ]);

        $kamar2 = Kamar::create([
            'properties_id' => $property->id,
            'nama_kamar'    => 'Kamar 102',
            'kapasitas'     => 3,
            'lantai'        => 1,
        ]);

        // Keduanya harus menunjuk ke property yang sama
        $this->assertEquals($property->id, $kamar1->properties->id);
        $this->assertEquals($property->id, $kamar2->properties->id);
        $this->assertEquals($kamar1->properties->id, $kamar2->properties->id,
            'Kedua kamar harus milik property yang sama');
    }
}
