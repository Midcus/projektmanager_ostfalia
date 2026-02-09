<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Thesis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanExpiredInterests extends Command
{
    protected $signature = 'interests:clean-expired';
    protected $description = 'clean all expired interest from the thesis_user table';

    public function handle()
    {
        $now = now(); 


        $expiredRecords = DB::table('thesis_user')
            ->where('expires_at', '<', $now)
            ->get();

        if ($expiredRecords->isEmpty()) {
            $this->info('Cron job interests:clean-expired executed at ' . $now->toDateTimeString() . '. No expired records found.');
        } else {
            $this->info('Cron job interests:clean-expired executed at ' . $now->toDateTimeString() . '. Found ' . $expiredRecords->count() . ' expired records: ' . $expiredRecords->toJson());
        }

        $deletedCount = DB::table('thesis_user')
            ->where('expires_at', '<', $now)
            ->delete();

        $this->info('Cron job interests:clean-expired deleted records at ' . $now->toDateTimeString() . '. Deleted ' . $deletedCount . ' expired interests.');
        $this->info('Abgelaufene Interessen wurden entfernt. Deleted ' . $deletedCount . ' records.');
    }
}