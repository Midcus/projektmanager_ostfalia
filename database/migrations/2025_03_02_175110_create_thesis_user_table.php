<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThesisUserTable extends Migration
{
    public function up()
    {
        Schema::create('thesis_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('thesis_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('thesis_id')->references('id')->on('theses')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            
            $table->unique(['thesis_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('thesis_user');
    }
}