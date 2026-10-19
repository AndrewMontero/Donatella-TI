<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('tickets')->whereIn('status', ['new', 'waiting'])->update(['status' => 'pending']);
        DB::table('tickets')->whereIn('status', ['resolved', 'closed'])->update(['status' => 'done']);

        Schema::table('tickets', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('tickets')->where('status', 'pending')->update(['status' => 'new']);
        DB::table('tickets')->where('status', 'done')->update(['status' => 'closed']);

        Schema::table('tickets', function (Blueprint $table) {
            $table->string('status')->default('new')->change();
        });
    }
};
