<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_item_mappings', function (Blueprint $table) {
            $table->id();

            $table->string('ri_item_number')->nullable();
            $table->string('ri_name')->nullable();

            $table->string('ga_item_number')->nullable();
            $table->string('ga_code')->nullable();
            $table->string('ga_name')->nullable();

            $table->timestamps();

            $table->index('ri_item_number');
            $table->index('ga_item_number');
            $table->index('ga_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_item_mappings');
    }
};