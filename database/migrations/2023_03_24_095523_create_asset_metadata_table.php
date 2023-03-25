<?php

use App\Models\Base;
use App\Models\Table;
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
        Schema::create('assets', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->timestamps();

            $table->string('filename');
            $table->string('path');
            $table->integer('size');

            $table->foreignIdFor(Base::class);
            $table->foreignIdFor(Table::class);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
