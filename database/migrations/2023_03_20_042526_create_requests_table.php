<?php

use App\Models\Base;
use App\Models\Table;
use App\Models\View;
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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignIdFor(Base::class);
            $table->foreignIdFor(Table::class);
            $table->foreignIdFor(View::class)->nullable();

            $table->unsignedInteger('page');
            $table->unsignedInteger('per_page');
            $table->ipAddress('ip_address');
            $table->string('user_agent');
            $table->string('referrer')->nullable();
            $table->json('headers');

            $table->string('asn')->nullable();
            $table->string('continent')->nullable();
            $table->string('country')->nullable();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->float('latitude')->nullable();
            $table->float('longitude')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
