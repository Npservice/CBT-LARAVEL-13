<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        // 1. TABEL ROLES
        Schema::create('roles', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_role')->unique(); // 'admin', 'guru', 'siswa'
            $table->string('display_role');
            $table->timestamps();
        });

        // 2. TABEL PERMISSIONS
        Schema::create('permissions', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_permission')->unique(); // 'buat-soal', 'koreksi-nilai'
            $table->string('group_permission');
            $table->string('display_permission');
            $table->timestamps();
        });

        // 3. TABEL PIVOT: ROLE_HAS_PERMISSION
        Schema::create('role_has_permission', function (Blueprint $table) {
            $table->foreignUuid('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignUuid('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->primary(['role_id', 'permission_id']);
        });

        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->foreignUuid('role_id')->nullable()->constrained('roles')->onDelete('set null');
            $table->string('role'); // Langsung diisi string: 'admin', 'guru', atau 'siswa'
            $table->timestamps();
        });
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignUuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_has_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
