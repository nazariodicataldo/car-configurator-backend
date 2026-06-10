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
        Schema::create('optional_setups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table
                ->foreignUuid('optional_id')
                ->constrained('optionals')
                ->cascadeOnDelete();
            $table
                ->foreignUuid('setup_vehicle_id')
                ->references('id')
                ->on('setup_vehicles')
                ->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->boolean('is_included')->default(false);
            $table->timestamps();
            $table->unique(['optional_id', 'setup_vehicle_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('optional_setups');
    }
};
