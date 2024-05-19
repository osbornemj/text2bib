<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Artisan;

class AdminSeedExamples extends Component
{
    public function mount()
    {
        $exitCode = Artisan::call('db:seed --class=ExampleSeeder');
        // $exitCode == 0 means success
    }
}
