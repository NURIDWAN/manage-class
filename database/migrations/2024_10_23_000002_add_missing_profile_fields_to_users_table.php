<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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

        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'nim')) {
                $table->string('nim', 50)->nullable()->unique()->after('role');
            }

            if (! Schema::hasColumn('users', 'kelas')) {
                $table->string('kelas', 50)->nullable()->after('nim');
            }

            if (! Schema::hasColumn('users', 'no_hp')) {
                $table->string('no_hp', 50)->nullable()->after('kelas');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        if (Schema::hasColumn('users', 'no_hp')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('no_hp');
            });
        }

        if (Schema::hasColumn('users', 'kelas')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('kelas');
            });
        }

        if (Schema::hasColumn('users', 'nim')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('nim');
            });
        }
    }
};
