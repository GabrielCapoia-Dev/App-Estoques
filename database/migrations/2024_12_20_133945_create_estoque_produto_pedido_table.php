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
        Schema::create('estoque_produto_pedido', function (Blueprint $table) {
            $table->unsignedBigInteger('estoque_produto_id');
            $table->unsignedBigInteger('pedido_id');
            $table->integer('quantidade')->default(1);
            $table->decimal('preco_unitario', 10, 2)->nullable();

            // Chave primária composta
            $table->primary(['estoque_produto_id', 'pedido_id']);

            // Chaves estrangeiras
            $table->foreign('estoque_produto_id')
                ->references('id')
                ->on('estoque_produto')
                ->onDelete('cascade');
            $table->foreign('pedido_id')
                ->references('id')
                ->on('pedidos')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estoque_produto_pedido');
    }
};
