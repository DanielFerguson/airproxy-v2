<?php

use App\Models\ApiToken;
use App\Models\User;
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
        Schema::create('bases', function (Blueprint $table) {
            $table->string('id');
            $table->timestamps();

            $table->foreignIdFor(User::class);
            $table->foreignIdFor(ApiToken::class);

            $table->string('name');

            $table->boolean('is_active')->default(true);

            $table->string('secret')->nullable();

            $table->primary(['id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bases');
    }
};
