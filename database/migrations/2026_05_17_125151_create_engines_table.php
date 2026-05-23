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
        Schema::create('engines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->enum('transmission', ['automatico', 'manuale']);
            $table->decimal('consumption', 4, 2); //l/100km
            $table->decimal('emissions', 5, 2); // g/km
            $table->decimal('power', 5, 2); // kW
            $table->enum('fuel', [
                'benzina',
                'diesel',
                'elettrico',
                'gpl',
                'metano',
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('engines');
    }
};
