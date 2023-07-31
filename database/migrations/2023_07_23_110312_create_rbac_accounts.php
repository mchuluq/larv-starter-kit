<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{

    public function up(): void{
        Schema::create('accounts', function (Blueprint $table) {
            $table->string('id',36)->primary();
            $table->string('user_id',36);
            $table->string('group_id',64)->nullable();
            $table->boolean('active')->default(true);
            $table->string('accountable_id',64)->comment("IDNUMBER");
            $table->string('accountable_type');
            $table->timestamps();
        });
    }

    public function down(): void{
        Schema::dropIfExists('accounts');
    }
};