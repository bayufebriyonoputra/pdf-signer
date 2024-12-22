<?php

namespace Database\Seeders;

use App\Models\MasterStep;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class MasterStepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = database_path('seeders/data/master_steps.csv');
        if(File::exists($csvFile)){
            $file = fopen($csvFile, 'r');
            $header = fgetcsv($file);

            while($row = fgetcsv($file)){
                $data = array_combine($header, $row);
                MasterStep::create([
                    'previous_step' => $data['previous_step'],
                    'step_name' => $data['step_name'],
                    'next_step' => $data['next_step'],
                ]);
            }
            fclose($file);
        }
    }
}
