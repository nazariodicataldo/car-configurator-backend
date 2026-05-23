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
        Schema::create('configurations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table
                ->foreignUuid('user_id')
                ->constrained('users')
                ->nullOnDelete();
            $table
                ->foreignUuid('vehicle_id')
                ->constrained('vehicles')
                ->nullOnDelete();
            $table->decimal('vehicle_price', 10, 2);
            $table
                ->foreignUuid('engine_id')
                ->constrained('engines')
                ->nullOnDelete();
            $table->decimal('engine_price', 10, 2);
            $table
                ->foreignUuid('setup_id')
                ->constrained('setups')
                ->nullOnDelete();
            $table->decimal('setup_price', 10, 2);
            $table
                ->foreignUuid('color_id')
                ->constrained('colors')
                ->nullOnDelete();

            $table->decimal('color_price', 10, 2);
            /* $table
                ->foreignUuid('vehicles_wheel_id')
                ->constrained('vehicles_wheels')
                ->nullOnDelete();
            $table
                ->foreignUuid('interior_vehicle_id')
                ->constrained('interior_vehicles')
                ->nullOnDelete(); */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configurations');
    }
};
