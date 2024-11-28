<?php

use App\Models\HeaderPo;
use Illuminate\Support\Carbon;
use function Livewire\Volt\{state, mount};

state([
    'count'
]);

mount(function(){
    $today = Carbon::today();
        $untilDate = $today->copy()->addDay(4);
        $this->count =  HeaderPo::query()->where('due_date', '>=',$today)->where('due_date', '<=', $untilDate)->count();
});
?>

<div>
    <span class="absolute top-0 right-0 p-1 text-sm text-white bg-red-500 rounded-lg">{{$count}}</span>
</div>
