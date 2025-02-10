<?php

namespace App\Jobs;

use App\Mail\SendPoMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    public array $address;
    /**
     * Create a new job instance.
     */
    public function __construct($data, array $address)
    {
        $this->data =  $data;
        $this->address = $address;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->address)
            ->cc(['purchasing02@sai.co.id', 'deni@sai.co.id'])
            ->send(new SendPoMail($this->data));
    }
}
