<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotizAndSemesterToThesesTable extends Migration
{
    public function up()
    {
        Schema::table('theses', function (Blueprint $table) {

            $table->text('notiz')->nullable()->after('kenntnisse');
            $table->string('semester')->nullable()->after('notiz');
        });
    }

    public function down()
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->dropColumn('notiz');
            $table->dropColumn('semester');
        });
    }
}
