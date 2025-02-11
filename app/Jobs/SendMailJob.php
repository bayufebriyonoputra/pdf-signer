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

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    public array $address;
    /**
     * Create a new job instance.
     */
    public function __construct($data, $address)
    {
        $this->data =  $data;
        $this->address = $address;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // throw new \Exception("Memicu kegagalan secara sengaja");
        Mail::to($this->address)
            ->cc(['purchasing02@sai.co.id', 'deni@sai.co.id'])
            ->send(new SendPoMail($this->data));
    }

      /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        Log::error('Job gagal dengan error: ' . $exception->getMessage());
        EmailFailed::create([
            'no_po' => $this->data['noPo']
        ]);
    }
}
