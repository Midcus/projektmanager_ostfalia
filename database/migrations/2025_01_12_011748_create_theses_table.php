<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThesesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('theses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('betreuer');
            $table->text('description');
            $table->text('kenntnisse');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('prof_id')->nullable();
            $table->timestamps();
        
            $table->foreign('prof_id')->references('id')->on('users')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('theses');
    }
}
