<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // database/migrations/2024_01_01_000001_add_branding_to_tenants_table.php
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('brand_name')->nullable();         // Custom app name
            $table->string('brand_logo')->nullable();         // Path to uploaded logo
            $table->string('brand_color_primary')->nullable(); // e.g. #003087
            $table->string('brand_color_accent')->nullable();  // e.g. #CE1126
            $table->string('brand_tagline')->nullable();       // Custom tagline
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};