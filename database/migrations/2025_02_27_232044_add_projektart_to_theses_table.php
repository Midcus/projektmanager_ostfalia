<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProjektartToThesesTable extends Migration
{
    public function up()
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->json('projektart')->nullable(); // Cột JSON, có thể để null
        });
    }

    public function down()
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->dropColumn('projektart');
        });
    }
}