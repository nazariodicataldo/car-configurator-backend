<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->enum('body_type', [
                'berlina',
                'due volumi',
                'suv',
                'monovolume',
                'coupe',
                'cabriolet',
                'furgone',
                'autobus',
                'camion',
            ]);
            $table->enum('seats', ['2', '4', '5', '6', '7', '8', '9']);
            $table->decimal('base_price', 10, 2);
            $table->text('base_img_url')->nullable();
            $table
                ->foreignUuid('brand_id')
                ->constrained('brands')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
