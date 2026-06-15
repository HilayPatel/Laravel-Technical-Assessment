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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // Link to the import log track entry
            $table->foreignId('import_record_id')->constrained('import_records')->onDelete('set null');
            $table->string('handle')->nullable()->index();
            $table->string('title')->nullable();
            $table->text('body_html')->nullable();
            $table->string('vendor')->nullable();
            $table->string('product_type')->nullable();
            $table->text('tags')->nullable();
            $table->boolean('published')->default(false);

            // Variant Information
            $table->string('variant_sku')->nullable()->index();
            $table->decimal('variant_price', 10, 2)->default(0.00);
            $table->decimal('variant_compare_at_price', 10, 2)->nullable();
            $table->boolean('variant_requires_shipping')->default(true);
            $table->boolean('variant_taxable')->default(true);
            $table->string('variant_inventory_tracker')->nullable();
            $table->integer('variant_inventory_qty')->default(0);
            $table->string('variant_inventory_policy')->nullable();
            $table->string('variant_fulfillment_service')->nullable();
            $table->decimal('variant_weight', 8, 2)->default(0.00);
            $table->string('variant_weight_unit')->nullable();

            // Media Asset References
            $table->text('image_src')->nullable();
            $table->integer('image_position')->nullable();
            $table->text('image_alt_text')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
