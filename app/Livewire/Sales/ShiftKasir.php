<?php

namespace App\Livewire\Sales;

use Livewire\Component;

use Livewire\WithPagination;
use App\Models\shiftKasirModel as ShiftModel;
use Illuminate\Support\Facades\Auth;

class ShiftKasir extends Component
{
    use WithPagination;

    public $initial_cash = 0;
    public $cash_in = 0;
    public $cash_out = 0;
    public $final_cash = 0;
    public $notes = '';
    public $activeShift = null;

    protected $rules = [
        'initial_cash' => 'required|numeric|min:0',
        'notes' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->activeShift = ShiftModel::where('cashier_id', Auth::id())
            ->where('status', 'open')
            ->first();
    }

    /** Buka shift baru */
    public function openShift()
    {
        $this->validate();

        if ($this->activeShift) {
            $this->dispatch('alert', ['title' => 'Shift masih aktif, tutup dulu sebelum buka baru.', 'icon' => 'warning']);
            return;
        }

        $this->activeShift = ShiftModel::create([
            'cashier_id'   => Auth::id(),
            'branch_id'    => Auth::user()->branch_id,
            'shift_start'  => now(),
            'initial_cash' => $this->initial_cash,
            'cash_in'      => 0,
            'cash_out'     => 0,
            'final_cash'   => $this->initial_cash,
            'status'       => 'open',
            'notes'        => $this->notes,
        ]);

        $this->reset(['initial_cash', 'notes']);
        $this->dispatch('alert', ['title' => 'Shift dibuka!', 'icon' => 'success']);
    }

    /** Tambah uang masuk/keluar */
    public function addCash($type)
    {
        if (!$this->activeShift) return;

        $amount = $type === 'in' ? $this->cash_in : $this->cash_out;
        if ($amount <= 0) {
            $this->dispatch('alert', ['title' => 'Nominal tidak valid.', 'icon' => 'error']);
            return;
        }

        if ($type === 'in') {
            $this->activeShift->increment('cash_in', $amount);
        } else {
            $this->activeShift->increment('cash_out', $amount);
        }

        $this->activeShift->update([
            'final_cash' => $this->activeShift->initial_cash
                + $this->activeShift->cash_in
                - $this->activeShift->cash_out,
        ]);

        $this->reset(['cash_in', 'cash_out']);
        $this->dispatch('alert', ['title' => 'Transaksi dicatat.', 'icon' => 'info']);
    }

    /** Tutup shift aktif */
    public function closeShift()
    {
        if (!$this->activeShift) return;

        $this->activeShift->update([
            'shift_end'  => now(),
            'status'     => 'closed',
            'final_cash' => $this->activeShift->initial_cash
                + $this->activeShift->cash_in
                - $this->activeShift->cash_out,
            'notes'        => $this->notes,
        ]);

        $this->activeShift = null;
        $this->dispatch('alert', ['title' => 'Shift ditutup.', 'icon' => 'success']);
    }

    public function render()
    {
        $shifts = ShiftModel::where('branch_id', Auth::user()->branch_id)
            ->latest('shift_start')
            ->paginate(6);

        return view('livewire.sales.shift-kasir', [
            'shifts' => $shifts,
        ]);
    }
}
