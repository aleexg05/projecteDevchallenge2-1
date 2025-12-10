<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('etiquetas', function (Blueprint $table) {
            // Eliminar la constraint única actual
            $table->dropUnique(['etiqueta_producte', 'user_id']);

            // Afegir id_llista_compra
            $table->unsignedBigInteger('id_llista_compra')->nullable()->after('user_id');

            // Afegir foreign key
            $table->foreign('id_llista_compra')
                ->references('id_llista_compra')
                ->on('llistes_compra')
                ->onDelete('cascade');

            // Nova constraint única per llista
            $table->unique(['etiqueta_producte', 'id_llista_compra']);
        });
    }

    public function down()
    {
        Schema::table('etiquetas', function (Blueprint $table) {
            $table->dropForeign(['id_llista_compra']);
            $table->dropUnique(['etiqueta_producte', 'id_llista_compra']);
            $table->dropColumn('id_llista_compra');
            $table->unique(['etiqueta_producte', 'user_id']);
        });
    }
};
