<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{

    public function up(): void{
        Schema::table('oauth_access_tokens',function(Blueprint $table){
            $table->bigInteger('otp_checked_at')->nullable()->after('revoked');
        });
    }

    public function down(): void{
        Schema::table('oauth_access_tokens', function (Blueprint $table) {
            $table->dropColumn(['otp_checked_at']);
        });
    }

};
