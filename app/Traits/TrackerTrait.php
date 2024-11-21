<?php
namespace App\Traits;

use App\Models\Tracker;

trait TrackerTrait
{
    public function addTrack($noPo, $message, $description, $icon = '<i class="bi bi-folder-plus"></i>', $additionalClass = 'bg-cyan-500')
    {
        Tracker::create([
            'no_po' => $noPo,
            'message' => $message,
            'description' => $description,
            'icon' => $icon,
            'additional_class' => $additionalClass
        ]);
    }
}
