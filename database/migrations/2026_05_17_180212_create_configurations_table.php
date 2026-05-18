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
            $table->decimal('total_price', 12, 2);
            $table
                ->foreignUuid('user_id')
                ->constrained('users')
                ->nullOnDelete();
            $table
                ->foreignUuid('vehicle_id')
                ->constrained('vehicles')
                ->nullOnDelete();
            $table
                ->foreignUuid('engine_vehicle_id')
                ->constrained('engine_vehicles')
                ->nullOnDelete();
            $table
                ->foreignUuid('setup_vehicle_id')
                ->constrained('setup_vehicles')
                ->nullOnDelete();
            /* $table
                ->foreignUuid('configuration_optional_id')
                ->constrained('configuration_optionals')
                ->nullOnDelete(); */
            $table
                ->foreignUuid('color_vehicle_id')
                ->constrained('color_vehicles')
                ->nullOnDelete();
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
