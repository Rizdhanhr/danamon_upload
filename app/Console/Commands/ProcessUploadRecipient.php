<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\JobLockService;
use App\Services\ProcessRecipientService;
use Log;


class ProcessUploadRecipient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-upload-recipient';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected $jobLockService;

    public function __construct(JobLockService $jobLockService, ProcessRecipientService $processRecipient)
    {
        parent::__construct(); // <- penting biar Command Laravel tetap jalan
        $this->jobLockService = $jobLockService;
        $this->processRecipient = $processRecipient;
    }

    public function handle()
    {
        ini_set('memory_limit', '500M');
        if (!$this->jobLockService->acquire('process_upload_recipient')) {
            return 0;
        }

        try {
            $this->processRecipient->run();
        } catch (\Throwable $e) {
            Log::channel('cron')->error('Process Upload gagal di command: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            $this->jobLockService->release('process_upload_recipient');
        }
    }
}
