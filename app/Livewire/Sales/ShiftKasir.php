<?php

// ============================================
// FILE 1: LIVEWIRE COMPONENT (ENHANCED)
// ============================================
// File: app/Livewire/Sales/ShiftKasir.php

namespace App\Livewire\Sales;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use App\Models\shiftKasirModel as ShiftModel;
use App\Models\salesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShiftKasir extends Component
{
    use WithPagination;

    public $initial_cash = 0;
    public $cash_in = 0;
    public $cash_out = 0;
    public $final_cash = 0;

    public $activeShift = null;

    // View Detail
    public $viewShiftId = null;
    public $viewShiftData = null;
    public $viewSales = [];
    public $viewExpenses = [];
    public $showDetailModal = false;

    // Delete
    public $deleteId = null;

    protected $rules = [
        'initial_cash' => 'required|numeric|min:0',
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
            $this->dispatch('alert', [
                'title' => 'Shift Masih Aktif',
                'text' => 'Tutup shift terlebih dahulu sebelum membuka shift baru',
                'icon' => 'warning'
            ]);
            return;
        }

        try {
            DB::beginTransaction();

            $this->activeShift = ShiftModel::create([
                'cashier_id'   => Auth::id(),
                'branch_id'    => Auth::user()->branch_id,
                'shift_start'  => now(),
                'initial_cash' => $this->initial_cash,
                'cash_in'      => 0,
                'cash_out'     => 0,
                'final_cash'   => $this->initial_cash,
                'status'       => 'open',
            ]);

            DB::commit();

            $this->reset(['initial_cash']);

            $this->dispatch('alert', [
                'title' => 'Shift Dibuka!',
                'text' => 'Shift kasir berhasil dibuka',
                'icon' => 'success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('alert', [
                'title' => 'Gagal!',
                'text' => $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    /** Tutup shift aktif */
    public function closeShift()
    {
        if (!$this->activeShift) return;

        try {
            DB::beginTransaction();

            $this->activeShift->update([
                'shift_end'  => now(),
                'status'     => 'closed',
                'final_cash' => $this->activeShift->initial_cash
                    + $this->activeShift->cash_in
                    - $this->activeShift->cash_out,
            ]);

            DB::commit();

            $this->activeShift = null;

            $this->dispatch('alert', [
                'title' => 'Shift Ditutup!',
                'text' => 'Shift kasir berhasil ditutup',
                'icon' => 'success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('alert', [
                'title' => 'Gagal!',
                'text' => $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    /** View Detail Shift */
    public function viewDetail($shiftId)
    {
        $shift = ShiftModel::with(['toKasir', 'toCabang'])->find($shiftId);

        if (!$shift) {
            $this->dispatch('alert', [
                'title' => 'Error!',
                'text' => 'Data shift tidak ditemukan',
                'icon' => 'error'
            ]);
            return;
        }

        $this->viewShiftId = $shiftId;
        $this->viewShiftData = $shift;

        // Get sales transactions during this shift
        $this->viewSales = salesModels::with(['toItems.toProduk'])
            ->where('cashier_id', $shift->cashier_id)
            ->where('branch_id', $shift->branch_id)
            ->whereBetween('created_at', [
                $shift->shift_start,
                $shift->shift_end ?? now()
            ])
            ->where('status', 'paid')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($sale) {
                return [
                    'id' => $sale->id,
                    'invoice_number' => $sale->invoice_number,
                    'sale_date' => $sale->created_at,
                    'subtotal' => $sale->subtotal,
                    'discount_total' => $sale->discount_total,
                    'total_amount' => $sale->total_amount,
                    'payment_method' => $sale->payment_method,
                    'items' => $sale->toItems->map(function ($item) {
                        return [
                            'product_name' => $item->toProduk->name ?? '-',
                            'unit_name' => $item->unit_name,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'discount' => $item->discount,
                            'subtotal' => $item->subtotal,
                        ];
                    })->toArray(),
                ];
            })->toArray();

        // dd($this->viewSales);

        // Get expenses (placeholder - siapkan struktur)
        $this->viewExpenses = [
            // TODO: Implement expense tracking
            // Format:
            // [
            //     'description' => 'Pembelian supplies',
            //     'amount' => 50000,
            //     'date' => now(),
            //     'notes' => 'ATK kantor'
            // ]
        ];

        $this->showDetailModal = true;
    }

    /** Close Detail Modal */
    public function closeDetailModal()
    {
        $this->reset(['viewShiftId', 'viewShiftData', 'viewSales', 'viewExpenses', 'showDetailModal']);
    }

    /** Confirm Delete */
    public function confirmDelete($id)
    {
        $shift = ShiftModel::find($id);

        if (!$shift) {
            $this->dispatch('alert', [
                'title' => 'Error!',
                'text' => 'Data shift tidak ditemukan',
                'icon' => 'error'
            ]);
            return;
        }

        if ($shift->status === 'open') {
            $this->dispatch('alert', [
                'title' => 'Tidak Bisa Dihapus!',
                'text' => 'Shift yang masih aktif tidak bisa dihapus. Tutup shift terlebih dahulu.',
                'icon' => 'warning'
            ]);
            return;
        }

        $this->deleteId = $id;

        $this->dispatch('confirm', [
            'title' => 'Hapus data shift ini?',
            'text' => 'Data shift akan dihapus permanen. Tindakan ini tidak bisa dibatalkan!',
            'icon' => 'warning',
            'confirmButtonText' => 'Ya, Hapus!',
            'cancelButtonText' => 'Batal',
            'event' => 'deleteShiftConfirmed'
        ]);
    }

    /** Delete Shift */
    #[On('deleteShiftConfirmed')]
    public function deleteShift()
    {
        if (!$this->deleteId) return;

        try {
            DB::beginTransaction();

            $shift = ShiftModel::find($this->deleteId);

            if (!$shift) {
                throw new \Exception('Data shift tidak ditemukan');
            }

            // Check if there are sales in this shift
            $hasSales = salesModels::where('cashier_id', $shift->cashier_id)
                ->where('branch_id', $shift->branch_id)
                ->whereBetween('sale_date', [
                    $shift->shift_start,
                    $shift->shift_end ?? now()
                ])
                ->exists();

            if ($hasSales) {
                throw new \Exception('Tidak dapat menghapus shift yang memiliki transaksi penjualan');
            }

            $shift->forceDelete();

            DB::commit();

            $this->dispatch('alert', [
                'title' => 'Berhasil!',
                'text' => 'Data shift berhasil dihapus',
                'icon' => 'success'
            ]);

            $this->deleteId = null;
            $this->resetPage();
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('alert', [
                'title' => 'Gagal!',
                'text' => $e->getMessage(),
                'icon' => 'error'
            ]);

            $this->deleteId = null;
        }
    }

    /** Print Shift Report */
    public function printShift($shiftId)
    {
        // Redirect to print page
        $url = route('shift.print', $shiftId);

        // Dispatch event ke frontend
        $this->dispatch('openPrintWindow', $url);
    }

    public function render()
    {
        $shifts = ShiftModel::with(['toKasir', 'toCabang'])
            ->where('branch_id', Auth::user()->branch_id)
            ->latest('shift_start')
            ->paginate(10);

        return view('livewire.sales.shift-kasir', [
            'shifts' => $shifts,
        ]);
    }
}
