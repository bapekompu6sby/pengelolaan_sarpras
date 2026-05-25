<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Properties;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;

/**
 * Unit Tests untuk model User.
 *
 * Menguji:
 * - Role column tersimpan benar ('admin', 'supervisor', 'user')
 * - Default role = 'user' (dari migration)
 * - Role ada di $guarded (tidak bisa mass-assign)
 * - Relasi hasMany Transaction
 * - Password hashing
 */
class UserModelTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────
    // Test: Role column
    // ──────────────────────────────────────────────

    /**
     * Test bahwa role 'admin' tersimpan dan terbaca benar di database.
     * Karena role ada di $guarded, harus menggunakan forceFill() atau manual.
     */
    public function test_user_admin_role_is_stored_correctly(): void
    {
        $user = User::factory()->create();
        // Role ada di $guarded, jadi set manual via query
        $user->forceFill(['role' => 'admin'])->save();
        $user->refresh();

        $this->assertEquals('admin', $user->role,
            'Role "admin" harus tersimpan dan terbaca dengan benar');
    }

    /**
     * Test bahwa role 'supervisor' tersimpan dan terbaca benar.
     */
    public function test_user_supervisor_role_is_stored_correctly(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'supervisor'])->save();
        $user->refresh();

        $this->assertEquals('supervisor', $user->role,
            'Role "supervisor" harus tersimpan dan terbaca dengan benar');
    }

    /**
     * Test bahwa role 'user' tersimpan dan terbaca benar.
     */
    public function test_user_role_is_stored_correctly(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'user'])->save();
        $user->refresh();

        $this->assertEquals('user', $user->role,
            'Role "user" harus tersimpan dan terbaca dengan benar');
    }

    /**
     * Test bahwa default role dari migration adalah 'user'.
     * Migration: $table->string('role')->default('user')
     */
    public function test_default_role_is_user(): void
    {
        $user = User::factory()->create();
        $user->refresh();

        $this->assertEquals('user', $user->role,
            'Default role harus "user" sesuai migration');
    }

    // ──────────────────────────────────────────────
    // Test: Role di $guarded (proteksi mass-assignment)
    // ──────────────────────────────────────────────

    /**
     * Test bahwa role TIDAK bisa di-set via mass assignment (ada di $guarded).
     * Ini memastikan keamanan: role tidak bisa diubah via form input biasa.
     */
    public function test_role_is_guarded_from_mass_assignment(): void
    {
        $user = User::create([
            'name'     => 'Test Guard',
            'email'    => 'guard@example.com',
            'password' => 'password123',
            'role'     => 'admin',   // coba inject role via mass assignment
        ]);

        $user->refresh();

        // Role seharusnya tetap 'user' (default) karena di-guard
        $this->assertEquals('user', $user->role,
            'Role harus tetap default "user" karena dilindungi oleh $guarded');
    }

    // ──────────────────────────────────────────────
    // Test: Relasi hasMany Transaction
    // ──────────────────────────────────────────────

    /**
     * Test bahwa User->transactions() mengembalikan transaksi-transaksi
     * yang dimiliki user via user_id.
     */
    public function test_user_has_many_transactions(): void
    {
        $user = User::factory()->create();

        $property = Properties::create([
            'name'     => 'Aula Test',
            'type'     => 'aula',
            'capacity' => 100,
            'price'    => '0',
            'unit'     => 1,
            'status'   => 'ative',
        ]);

        // Buat 2 transaksi untuk user ini
        foreach (range(1, 2) as $i) {
            Transaction::create([
                'name'         => "Pemesan $i",
                'instansi'     => 'Test Instansi',
                'kegiatan'     => "Kegiatan $i",
                'start'        => '2025-10-01',
                'end'          => '2025-10-01',
                'property_id'  => $property->id,
                'status'       => 'pending',
                'affiliation'  => 'internal_pu',
                'phone_number' => '081234567890',
                'email'        => "test{$i}@example.com",
                'ordered_unit' => 1,
                'user_id'      => $user->id,
            ]);
        }

        $this->assertInstanceOf(Collection::class, $user->transactions,
            'transactions() harus mengembalikan Eloquent Collection');

        $this->assertCount(2, $user->transactions,
            'User harus memiliki 2 transaksi');

        $this->assertInstanceOf(Transaction::class, $user->transactions->first(),
            'Item dalam collection harus instance Transaction');
    }

    /**
     * Test bahwa User tanpa transaksi mengembalikan collection kosong.
     */
    public function test_user_with_no_transactions_returns_empty_collection(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(Collection::class, $user->transactions);
        $this->assertCount(0, $user->transactions,
            'User tanpa transaksi harus return collection kosong');
    }

    // ──────────────────────────────────────────────
    // Test: Password Hashing
    // ──────────────────────────────────────────────

    /**
     * Test bahwa password di-hash otomatis via cast 'hashed' di model.
     */
    public function test_password_is_hashed(): void
    {
        $user = User::factory()->create([
            'password' => 'mysecretpassword',
        ]);

        // Password tidak boleh tersimpan sebagai plain text
        $this->assertNotEquals('mysecretpassword', $user->password,
            'Password harus di-hash, bukan plain text');

        // Harus bisa di-verify via Hash::check
        $this->assertTrue(
            \Illuminate\Support\Facades\Hash::check('mysecretpassword', $user->password),
            'Hash::check harus berhasil memverifikasi password asli'
        );
    }

    // ──────────────────────────────────────────────
    // Test: Hidden Attributes
    // ──────────────────────────────────────────────

    /**
     * Test bahwa password dan remember_token di-hide saat serialisasi (toArray/toJson).
     */
    public function test_password_and_remember_token_are_hidden(): void
    {
        $user = User::factory()->create();
        $array = $user->toArray();

        $this->assertArrayNotHasKey('password', $array,
            'Password harus hidden saat serialisasi');
        $this->assertArrayNotHasKey('remember_token', $array,
            'remember_token harus hidden saat serialisasi');
    }

    // ──────────────────────────────────────────────
    // Test: Factory dasar
    // ──────────────────────────────────────────────

    /**
     * Test bahwa UserFactory menghasilkan User yang valid.
     */
    public function test_user_factory_creates_valid_user(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->id);
        $this->assertNotEmpty($user->name);
        $this->assertNotEmpty($user->email);
        $this->assertNotNull($user->email_verified_at);
    }
}
