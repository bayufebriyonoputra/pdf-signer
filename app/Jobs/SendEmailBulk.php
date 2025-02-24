<?php

namespace App\Jobs;

use Throwable;
use App\Mail\SendPoMail;
use App\Models\EmailFailed;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendEmailBulk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    public $address;

    /**
     * Create a new job instance.
     */
    public function __construct($data, $address)
    {
        $this->data = $data;
        $this->address = $address;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $len = count($this->data);
        for ($i = 0; $i < $len; $i++) {
            Mail::to($this->address[$i])
                ->cc(['purchasing02@sai.co.id', 'deni@sai.co.id'])
                ->send(new SendPoMail($this->data[$i]));
        }
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('Job Email Bulk gagal dengan error: ' . $exception->getMessage());

        $noPo = implode(', ', array_column($this->data, 'noPo'));
        EmailFailed::create([
            'no_po' => $noPo,
            'message' => 'Email untuk NO PO berikut gagal dikirm'
        ]);
    }
}
