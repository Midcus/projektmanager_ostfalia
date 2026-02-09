<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPraefixAndBeschreibungToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
          
            $table->string('praefix')->nullable()->after('nachname');
            $table->text('beschreibung')->nullable()->after('praefix');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('praefix');
            $table->dropColumn('beschreibung');
        });
    }
}

