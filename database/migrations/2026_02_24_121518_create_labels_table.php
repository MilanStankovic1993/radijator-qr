<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_labels', function (Blueprint $table) {
            $table->id();

            $table->string('token', 32)->unique()->index();

            // -----------------------------
            // ZAJEDNIČKI PODACI
            // -----------------------------
            $table->string('po_number')->nullable()->index();            // Porudžbenica

            // PO meta (fali za dokument)
            $table->string('vendor_no')->nullable();                     // Vendor No.
            $table->string('buyer')->nullable();                         // Buyer

            $table->string('storage_location')->nullable();              // Mesto prijema / skladištenja
            $table->date('load_date')->nullable()->index();              // Datum utovara
            $table->string('order_type')->nullable();                    // Vrsta narudžbenice

            $table->decimal('quantity', 12, 3)->nullable();              // Količina
            $table->string('um', 20)->nullable();                        // UM (PC, KG, M, ...)
            $table->decimal('price', 12, 2)->nullable();                 // Cena

            // -----------------------------
            // INTERNI PODACI – RADIJATOR INŽENJERING
            // -----------------------------
            $table->string('ri_item_number')->nullable()->index();       // Broj artikla (Radijator Inz)
            $table->string('ri_code')->nullable()->index();              // Šifra (Radijator Inz) Sklop/Deo
            $table->string('ri_name')->nullable();                       // Naziv (Radijator Inz) Sklop/Deo
            $table->string('ri_doc_number')->nullable();                 // Prijemnica / otpremnica

            // -----------------------------
            // GROUP ATLANTIC
            // -----------------------------
            $table->string('ga_item_number')->nullable()->index();       // Broj artikla (Group Atlantic)
            $table->string('ga_internal_number')->nullable()->index();   // Interni broj
            $table->string('ga_code')->nullable()->index();              // Šifra (Group Atlantic) Sklop/Deo
            $table->string('ga_name')->nullable();                       // Naziv (Group Atlantic) Sklop/Deo

            // -----------------------------
            // Billing / Shipping (fali za dokument)
            // -----------------------------
            $table->text('billing_address')->nullable();
            $table->string('billing_email')->nullable();

            $table->text('shipping_address')->nullable();
            $table->string('terms_payment')->nullable();
            $table->string('terms_delivery')->nullable();

            // Ostalo
            $table->text('note')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('disabled_at')->nullable()->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_labels');
    }
};