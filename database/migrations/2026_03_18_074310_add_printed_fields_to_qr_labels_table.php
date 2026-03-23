<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qr_labels', function (Blueprint $table) {
            $table->timestamp('printed_at')->nullable()->after('disabled_at');
            $table->foreignId('printed_by')->nullable()->after('printed_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('qr_labels', function (Blueprint $table) {
            $table->dropConstrainedForeignId('printed_by');
            $table->dropColumn('printed_at');
        });
    }
};