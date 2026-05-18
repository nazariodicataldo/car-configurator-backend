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
        Schema::create('vehicles_wheels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table
                ->foreignUuid('vehicle_id')
                ->constrained('vehicles')
                ->cascadeOnDelete();
            $table
                ->foreignUuid('wheel_id')
                ->constrained('wheels')
                ->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles_wheels');
    }
};
