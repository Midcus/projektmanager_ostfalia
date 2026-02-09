<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGeheimToThesesTable extends Migration
{
    public function up()
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->enum('geheim', ['yes', 'no'])->default('no')->after('status');
        });


        DB::table('theses')->update(['geheim' => 'no']);
    }

    public function down()
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->dropColumn('geheim');
        });
    }
}