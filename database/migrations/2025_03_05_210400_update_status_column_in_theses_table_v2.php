<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateStatusColumnInThesesTableV2 extends Migration
{
    public function up()
    {

        DB::statement('ALTER TABLE theses MODIFY COLUMN status VARCHAR(50)');
    }

    public function down()
    {

        DB::statement('ALTER TABLE theses MODIFY COLUMN status ENUM("active", "inactive")');
    }
}