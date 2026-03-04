<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_label_audits', function (Blueprint $table) {
            $table->id();

            $table->foreignId('qr_label_id')->constrained('qr_labels')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // create|update|disable|enable|delete|restore
            $table->string('action', 30)->index();

            // samo promenjena polja (ili snapshot na create)
            $table->json('before')->nullable();
            $table->json('after')->nullable();

            // kontekst
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            $table->index(['qr_label_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_label_audits');
    }
};