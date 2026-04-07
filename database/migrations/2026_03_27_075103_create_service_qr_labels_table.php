<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_qr_labels', function (Blueprint $table) {
            $table->id();

            $table->string('token')->unique();

            $table->string('picture_path')->nullable();

            $table->date('date')->nullable();
            $table->string('supplier_order_number')->nullable();

            $table->string('name')->nullable();
            $table->string('boiler_type')->nullable();
            $table->string('dimension')->nullable();
            $table->string('code_pdm')->nullable();

            $table->decimal('weight', 10, 2)->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('quantity', 12, 2)->nullable();

            $table->string('buyer')->nullable();
            $table->text('note')->nullable();

            $table->timestamp('printed_at')->nullable();
            $table->foreignId('printed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('disabled_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_qr_labels');
    }
};