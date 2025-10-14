<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        if (Schema::hasColumn('users', 'role')) {
            DB::table('users')
                ->where('role', 'member')
                ->update(['role' => 'user']);

            Schema::table('users', function (Blueprint $table): void {
                $table->string('role', 50)->default('user')->change();
            });

            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->string('role', 50)->default('user')->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'role')) {
            return;
        }

        DB::table('users')
            ->where('role', 'user')
            ->update(['role' => 'member']);

        DB::table('users')
            ->where('role', 'super_admin')
            ->update(['role' => 'admin']);

        Schema::table('users', function (Blueprint $table): void {
            $table->enum('role', ['admin', 'member'])->default('member')->change();
        });
    }
};
