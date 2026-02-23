<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class JobLockService
{
    public static function acquire(string $name): bool
    {
        return DB::transaction(function () use ($name) {
            $lock = DB::table('cron_lock')
            ->where('name', $name)
            ->lockForUpdate()
            ->first();

            if (!$lock) {
                return false; 
            }

            if ($lock->status == 1) {
                return false; 
            }

            DB::table('cron_lock')
            ->where('id', $lock->id)
            ->update(['status' => 1]);

            return true;
        }, 3);
    }

    public static function release(string $name): void
    {
        DB::table('cron_lock')
        ->where('name', $name)
        ->update(['status' => 0]);
    }
}