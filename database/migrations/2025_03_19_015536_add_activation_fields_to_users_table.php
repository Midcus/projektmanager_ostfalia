<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActivationFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('activation_code', 6)->nullable()->after('password');
            $table->timestamp('activation_expires_at')->nullable()->after('activation_code');
            $table->boolean('is_activated')->default(false)->after('activation_expires_at');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['activation_code', 'activation_expires_at', 'is_activated']);
        });
    }
}