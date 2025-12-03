<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
 Schema::create('etiquetas', function (Blueprint $table) {
    $table->id('id_etiqueta');
    $table->string('etiqueta_producte', 100);
    $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); 
    $table->timestamps();

    $table->unique(['etiqueta_producte', 'user_id']);
});




}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etiquetas');
    }
};
