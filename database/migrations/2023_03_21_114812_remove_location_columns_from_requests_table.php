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
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn('asn');
            $table->dropColumn('region');
            $table->dropColumn('city');
            $table->dropColumn('continent');
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
            $table->dropColumn('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->string('asn')->nullable();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->string('continent')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
};
