<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipment', function (Blueprint $table): void {
            $table->string('brand', 100)->nullable()->change();
            $table->string('movement', 50)->nullable()->after('category');
            $table->string('movement_date', 30)->nullable()->after('movement');
            $table->string('society', 120)->nullable()->after('department');
            $table->boolean('has_business_line')->nullable()->after('society');
            $table->string('phone_number', 40)->nullable()->after('has_business_line');
            $table->string('source_file', 255)->nullable()->after('phone_number');
        });
    }

    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table): void {
            $table->dropColumn(['movement', 'movement_date', 'society', 'has_business_line', 'phone_number', 'source_file']);
            $table->string('brand', 100)->nullable(false)->change();
        });
    }
};
