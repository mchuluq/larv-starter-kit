<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{

    public function up(): void{
        Schema::create('route_permissions', function (Blueprint $table) {
            $table->string('route');
            $table->string('group_id',64)->nullable();
            $table->string('role_id',64)->nullable();
            $table->string('account_id',36)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void{
        Schema::dropIfExists('route_permissions');
    }
};