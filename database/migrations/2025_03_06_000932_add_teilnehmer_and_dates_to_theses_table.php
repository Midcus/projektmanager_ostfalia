<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTeilnehmerAndDatesToThesesTable extends Migration

{
    public function up()
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->date('vortragdatum')->nullable()->after('geheim');
            $table->date('startdatum')->nullable()->after('vortragdatum');
            $table->date('enddatum')->nullable()->after('startdatum');
        });
    }

    public function down()
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->dropColumn(['vortragdatum', 'startdatum', 'enddatum']);
        });
    }
}
