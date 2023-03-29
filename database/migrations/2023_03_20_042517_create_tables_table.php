<?php

use App\Models\Base;
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
        Schema::create('tables', function (Blueprint $table) {
            $table->string('id');
            $table->timestamps();

            $table->foreignIdFor(Base::class);

            $table->string('name');

            $table->boolean('is_active')->default(true);
            $table->integer('ttl')->default(600); // 10 minutes

            $table->primary(['id', 'base_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
