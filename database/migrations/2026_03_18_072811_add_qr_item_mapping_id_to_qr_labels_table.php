<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qr_labels', function (Blueprint $table) {
            $table->foreignId('qr_item_mapping_id')
                ->nullable()
                ->after('id')
                ->constrained('qr_item_mappings')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('qr_labels', function (Blueprint $table) {
            $table->dropConstrainedForeignId('qr_item_mapping_id');
        });
    }
};