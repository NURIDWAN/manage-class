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
        Schema::table('events', function (Blueprint $table) {
            if (! Schema::hasColumn('events', 'title')) {
                $table->string('title')->after('id');
            }

            if (! Schema::hasColumn('events', 'description')) {
                $table->text('description')->nullable()->after('title');
            }

            if (! Schema::hasColumn('events', 'date')) {
                $table->date('date')->nullable()->after('description');
            }

            if (! Schema::hasColumn('events', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('date')->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('announcements', function (Blueprint $table) {
            if (! Schema::hasColumn('announcements', 'title')) {
                $table->string('title')->after('id');
            }

            if (! Schema::hasColumn('announcements', 'content')) {
                $table->text('content')->nullable()->after('title');
            }

            if (! Schema::hasColumn('announcements', 'author_id')) {
                $table->foreignId('author_id')->nullable()->after('content')->constrained('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'created_by')) {
                $table->dropConstrainedForeignId('created_by');
            }
            $table->dropColumn(['title', 'description', 'date']);
        });

        Schema::table('announcements', function (Blueprint $table) {
            if (Schema::hasColumn('announcements', 'author_id')) {
                $table->dropConstrainedForeignId('author_id');
            }
            $table->dropColumn(['title', 'content']);
        });
    }
};
