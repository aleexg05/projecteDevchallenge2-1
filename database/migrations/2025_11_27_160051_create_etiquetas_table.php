<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('etiquetas', function (Blueprint $table) {
            $table->id('id_etiqueta');
            $table->string('etiqueta_producte', 100);
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('id_llista_compra')->nullable();
            $table->timestamps();

            // Foreign key per llista
            $table->foreign('id_llista_compra')
                ->references('id_llista_compra')
                ->on('llistes_compra')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etiquetas');
    }
};
