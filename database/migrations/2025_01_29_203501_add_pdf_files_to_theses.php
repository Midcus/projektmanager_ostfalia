<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->string('pdf_1')->nullable(); // File PDF 1
            $table->string('pdf_2')->nullable(); // File PDF 2
        });
    }

    public function down()
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->dropColumn(['pdf_1', 'pdf_2']);
        });
    }
};
