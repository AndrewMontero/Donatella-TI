<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipment', function (Blueprint $table): void {
            $table->string('category', 20)->default('laptop')->after('name')->index();
        });

        Schema::table('equipment', function (Blueprint $table): void {
            $table->dropUnique('equipment_asset_code_unique');
            $table->dropColumn(['asset_code', 'holder_name']);
            $table->renameColumn('name', 'collaborator_name');
        });
    }

    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table): void {
            $table->renameColumn('collaborator_name', 'name');
            $table->string('asset_code', 80)->nullable();
            $table->string('holder_name', 150)->nullable();
            $table->dropColumn('category');
        });
    }
};
