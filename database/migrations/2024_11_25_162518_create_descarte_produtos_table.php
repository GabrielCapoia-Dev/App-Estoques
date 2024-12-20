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
        Schema::create('descarte_produtos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_estoque_produto')->constrained('estoque_produto')->onDelete('cascade');
            $table->string('defeito_descarte');
            $table->string('descricao_descarte');
            $table->string('quantidade_descarte');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('descarte_produtos');
    }
};
