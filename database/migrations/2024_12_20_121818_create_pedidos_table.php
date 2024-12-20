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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();

            // Relacionamento com a tabela 'locals'
            $table->foreignId('id_local')
                ->constrained('locals')
                ->onDelete('cascade');

            // Relacionamento com a tabela 'estoque_produto_pedido'
            $table->foreignId('estoque_produto_pedido_id')
                ->constrained('estoque_produto_pedido')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
