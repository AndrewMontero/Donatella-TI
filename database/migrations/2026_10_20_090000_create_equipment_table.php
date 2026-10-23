<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->string('asset_code', 80)->unique();
            $table->string('brand', 100);
            $table->string('department', 120);
            $table->string('holder_name', 150)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['department', 'brand']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
