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
        Schema::create('sensores', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('id_modulo')->constrained('modulos');
            $table->integer('s')->nullable(); 
            $table->integer('codigo')->nullable(); 
            $table->string('nome', 200)->nullable(); 
            $table->string('descricao', 500)->nullable(); 
            $table->string('equacao', 200)->nullable(); 
            $table->string('unidade', 20)->nullable(); 
            $table->float('ref_grafico_1')->nullable(); 
            $table->float('ref_grafico_2')->nullable(); 
            $table->string('titulo_ref_grafico_1', 100)->nullable(); 
            $table->string('titulo_ref_grafico_2', 100)->nullable(); 
            $table->integer('modbus')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensores');
    }
};
