<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('detail')->nullable()->after('description');
            $table->json('sizes')->nullable()->after('detail');
            $table->string('color')->nullable()->after('sizes');
            $table->unsignedInteger('discount')->default(0)->after('color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['detail', 'sizes', 'color', 'discount']);
        });
    }
};
