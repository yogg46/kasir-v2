<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Appearance extends Component
{
    public $appearance;

    public function mount()
    {
        $this->appearance = Auth::user()->appearance ?? 'system';
    }

    public function save()
    {
        $user = User::find(Auth::id());
        $user->appearance = $this->appearance;
        $user->save();

        // $this->dispatch('notify', message: 'Appearance updated successfully.');
    }
}
