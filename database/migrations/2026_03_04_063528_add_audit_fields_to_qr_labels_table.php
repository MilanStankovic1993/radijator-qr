<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qr_labels', function (Blueprint $table) {
            // ko je poslednji menjao
            $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();

            // soft delete + ko je obrisao
            $table->foreignId('deleted_by')->nullable()->after('updated_by')->constrained('users')->nullOnDelete();
            $table->softDeletes(); // adds deleted_at
        });
    }

    public function down(): void
    {
        Schema::table('qr_labels', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropConstrainedForeignId('deleted_by');
            $table->dropConstrainedForeignId('updated_by');
        });
    }
};