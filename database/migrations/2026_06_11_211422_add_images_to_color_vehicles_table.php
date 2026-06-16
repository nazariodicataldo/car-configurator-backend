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
        Schema::table('color_vehicles', function (Blueprint $table) {
            $table->text('front_image_url')->nullable()->after('price');
            $table
                ->text('back_image_url')
                ->nullable()
                ->after('front_image_url');
            $table->text('side_image_url')->nullable()->after('back_image_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('color_vehicles', function (Blueprint $table) {
            $table->dropColumn('front_image_url');
            $table->dropColumn('back_image_url');
            $table->dropColumn('side_image_url');
        });
    }
};
