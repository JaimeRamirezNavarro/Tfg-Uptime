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
    Schema::create('servers', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('api_token')->unique();
        $table->string('ip_address')->nullable(); // La ponemos aquí
        $table->string('ssh_user')->nullable();    // La ponemos aquí
        $table->string('ssh_password')->nullable(); // La ponemos aquí
        $table->unsignedBigInteger('user_id')->nullable(); // La hacemos nullable de una vez
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
