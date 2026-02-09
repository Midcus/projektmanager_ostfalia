<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateStatusColumnInThesesTableV2 extends Migration
{
    public function up()
    {
        // Dùng SQL trực tiếp để thay đổi cột status
        DB::statement('ALTER TABLE theses MODIFY COLUMN status VARCHAR(10)');
    }

    public function down()
    {
        // Quay lại ENUM cũ nếu cần rollback
        DB::statement('ALTER TABLE theses MODIFY COLUMN status ENUM("active", "inactive")');
    }
}