<?php

use App\Enum\StatusEnum;
use App\Models\HeaderPo;
use Illuminate\Support\Carbon;
use function Livewire\Volt\{state, mount};

state([
    'count',

]);

mount(function($content){

    if($content == 'reminder'){
        $today = Carbon::today();
        $untilDate = $today->copy()->addDay(4);
        $this->count =  HeaderPo::query()->where('due_date', '>=',$today)->where('due_date', '<=', $untilDate)->count();
    }elseif($content == 'pending'){
        $this->count = HeaderPo::query()->where('status', StatusEnum::PENDING)->count();
    }

});
?>

<div>
    <span class="absolute top-0 right-0 py-1 px-2 text-sm text-white bg-red-500 rounded-lg">{{$count}}</span>
</div>
