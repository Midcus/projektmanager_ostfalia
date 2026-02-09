<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpiresAtToThesisUserTable extends Migration
{
    public function up()
    {
        Schema::table('thesis_user', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('user_id');
        });
    }

    public function down()
    {
        Schema::table('thesis_user', function (Blueprint $table) {
            $table->dropColumn('expires_at');
        });
    }
}