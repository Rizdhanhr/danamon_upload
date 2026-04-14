<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\JobLockService;
use App\Services\ProcessNotificationService;
use Log;

class ProcessFinanceNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-finance-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected $jobLockService;

    public function __construct(JobLockService $jobLockService,ProcessNotificationService $processNotification)
    {
        parent::__construct(); // <- penting biar Command Laravel tetap jalan
        $this->jobLockService = $jobLockService;
        $this->processNotification = $processNotification;
        
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        ini_set('memory_limit', '500M');
        if (!$this->jobLockService->acquire('process_finance_notification')) {
            return 0;
        }

        try {
            $this->processNotification->run();
        } catch (\Throwable $e) {
            Log::channel('cron')->error('Process Upload gagal di command: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            $this->jobLockService->release('process_finance_notification');
        }
    }
}
