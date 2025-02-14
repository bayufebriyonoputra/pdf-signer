<?php

namespace App\Console\Commands;

use App\Enum\StatusEnum;
use App\Models\DetailPo;
use App\Models\HeaderPo;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteOldPo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-old-po';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Po melebihi 2 bulan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = Carbon::now()->subMonths(2);
        $po = HeaderPo::where('updated_at', '<', $date)->where('status', StatusEnum::DONE)->get();

        //delete pdf files
        foreach ($po as $item) {
            $detail = DetailPo::where('header_id', $item->id)->get();
            foreach ($detail as $d) {
                Storage::disk('public')->delete($d->file);
            }
            $detail->each->delete();
        }
        $po->each->delete();
    }
}
