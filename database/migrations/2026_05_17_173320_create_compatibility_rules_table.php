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
        Schema::create('compatibility_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table
                ->foreignUuid('optional_a_id')
                ->constrained('optionals')
                ->cascadeOnDelete();
            $table
                ->foreignUuid('optional_b_id')
                ->constrained('optionals')
                ->cascadeOnDelete();
            $table->unique(['optional_a_id', 'optional_b_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compatible_rules');
    }
};
