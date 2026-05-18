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
        Schema::create('configuration_optionals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table
                ->foreignUuid('configuration_id')
                ->constrained('configurations')
                ->cascadeOnDelete();
            $table
                ->foreignUuid('optional_setup_id')
                ->constrained('optional_setups')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuration_optionals');
    }
};
