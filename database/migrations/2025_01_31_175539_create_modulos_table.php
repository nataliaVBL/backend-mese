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
        Schema::create('modulos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_painel')->constrained('paineis');
            $table->string('nome', 200)->nullable(); 
            $table->bigInteger('chave_http')->nullable(); 
            $table->string('descricao', 500)->nullable(); 
            $table->string('endereco', 100)->nullable(); 
            $table->float('latitude')->nullable(); 
            $table->float('longitude')->nullable(); 
            $table->integer('intervalo_de_atualizacao')->nullable();
            $table->string('ip_autorizado', 100)->nullable();  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modulos');
    }
};
