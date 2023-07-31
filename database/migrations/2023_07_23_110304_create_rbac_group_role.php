<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    
    public function up(): void{
        Schema::create('groups', function (Blueprint $table) {
            $table->string('id',64)->primary();
            $table->string('name',64);
            $table->string('description')->nullable();
            $table->timestamps();
        });
        Schema::create('roles', function (Blueprint $table) {
            $table->string('id',64)->primary();
            $table->string('name',64);
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void{
        Schema::dropIfExists('groups');
        Schema::dropIfExists('roles');
    }
};