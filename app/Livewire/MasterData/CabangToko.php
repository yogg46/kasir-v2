<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use Livewire\WithPagination;
// use App\Models\branchesModel as branchModel;
use App\Models\cabangModel;
use App\Models\gudangModel;
use Illuminate\Support\Facades\DB;

use Livewire\Attributes\On;

class CabangToko extends Component
{
    use WithPagination;

    public $mode = 'branch'; // branch | warehouse
    public $search = '';
    public $perPage = 10;

    // Form fields
    public $name;
    public $address;
    public $phone;
    public $is_head_office = false; // untuk cabang
    public $is_main = false; // untuk gudang
    public $editingId = null;
    public $branch_id; // hanya untuk gudang
    public $showTrashed = false;

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ];

        if ($this->mode === 'branch') {
            $rules['is_head_office'] = 'boolean';
        }

        if ($this->mode === 'warehouse') {
            $rules['branch_id'] = 'required|exists:branches_models,id';
            $rules['is_main'] = 'boolean';
        }

        return $rules;
    }

    protected $messages = [
        'name.required' => 'Nama harus diisi',
        'branch_id.required' => 'Toko harus dipilih',
        'branch_id.exists' => 'Toko tidak valid',
    ];

    public function setMode($mode)
    {
        $this->resetForm();
        $this->mode = $mode;
        $this->resetPage();
    }

    public function render()
    {
        if ($this->mode === 'branch') {
            $query = cabangModel::query();
        } else {
            $query = gudangModel::with('toCabang');
        }

        if ($this->showTrashed) {
            $query->onlyTrashed();
        }

        $items = $query
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('code', 'like', "%{$this->search}%")
                        ->orWhere('address', 'like', "%{$this->search}%");
                });
            })
            ->orderBy($this->mode === 'branch' ? 'is_head_office' : 'is_main', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $branches = cabangModel::all();

        return view('livewire.master-data.cabang-toko', [
            'items' => $items,
            'branches' => $branches,
        ]);
    }


    public function resetForm()
    {
        $this->reset(['name', 'address', 'phone', 'is_head_office', 'is_main', 'editingId', 'branch_id']);
        $this->resetValidation();
    }

    public function create()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            if ($this->mode === 'branch') {
                // Jika set sebagai cabang pusat, unset yang lain
                if ($this->is_head_office) {
                    cabangModel::where('is_head_office', true)->update(['is_head_office' => false]);
                }

                cabangModel::create([
                    'name' => $this->name,
                    'address' => $this->address,
                    'phone' => $this->phone,
                    'is_head_office' => $this->is_head_office,
                ]);

                $message = 'Cabang berhasil ditambahkan!';
            } else {
                // Jika set sebagai gudang utama, unset yang lain di cabang yang sama
                if ($this->is_main) {
                    gudangModel::where('branch_id', $this->branch_id)
                        ->where('is_main', true)
                        ->update(['is_main' => false]);
                }

                gudangModel::create([
                    'branch_id' => $this->branch_id,
                    'name' => $this->name,
                    'address' => $this->address,
                    'is_main' => $this->is_main,
                ]);

                $message = 'Gudang berhasil ditambahkan!';
            }

            DB::commit();

            $this->dispatch('alert', [
                'title' => 'Berhasil!',
                'text' => $message,
                'icon' => 'success',
                'timer' => 2000,
            ]);

            $this->resetForm();
            $this->resetPage();
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('alert', [
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'icon' => 'error',
                'timer' => 3000,
            ]);
        }
    }

    public function edit($id)
    {
        $model = $this->mode === 'branch'
            ? cabangModel::find($id)
            : gudangModel::find($id);

        if (!$model) {
            $this->dispatch('alert', [
                'title' => 'Error!',
                'text' => 'Data tidak ditemukan',
                'icon' => 'error',
            ]);
            return;
        }

        $this->editingId = $model->id;
        $this->name = $model->name;
        $this->address = $model->address;
        $this->phone = $model->phone ?? null;

        if ($this->mode === 'branch') {
            $this->is_head_office = $model->is_head_office;
        } else {
            $this->branch_id = $model->branch_id;
            $this->is_main = $model->is_main;
        }
    }

    public function update()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $model = $this->mode === 'branch'
                ? cabangModel::find($this->editingId)
                : gudangModel::find($this->editingId);

            if (!$model) {
                throw new \Exception('Data tidak ditemukan');
            }

            if ($this->mode === 'branch') {
                // Jika set sebagai cabang pusat, unset yang lain
                if ($this->is_head_office) {
                    cabangModel::where('id', '!=', $this->editingId)
                        ->where('is_head_office', true)
                        ->update(['is_head_office' => false]);
                }

                $model->update([
                    'name' => $this->name,
                    'address' => $this->address,
                    'phone' => $this->phone,
                    'is_head_office' => $this->is_head_office,
                ]);

                $message = 'Cabang berhasil diperbarui!';
            } else {
                // Jika set sebagai gudang utama, unset yang lain di cabang yang sama
                if ($this->is_main) {
                    gudangModel::where('branch_id', $this->branch_id)
                        ->where('id', '!=', $this->editingId)
                        ->where('is_main', true)
                        ->update(['is_main' => false]);
                }

                $model->update([
                    'branch_id' => $this->branch_id,
                    'name' => $this->name,
                    'address' => $this->address,
                    'is_main' => $this->is_main,
                ]);

                $message = 'Gudang berhasil diperbarui!';
            }

            DB::commit();

            $this->dispatch('alert', [
                'title' => 'Berhasil!',
                'text' => $message,
                'icon' => 'success',
                'timer' => 2000,
            ]);

            $this->resetForm();
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('alert', [
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'icon' => 'error',
                'timer' => 3000,
            ]);
        }
    }

    public $deleteId = null; // Store ID untuk konfirmasi delete

    public function confirmDelete($id)
    {
        $this->deleteId = $id;

        $model = $this->mode === 'branch'
            ? cabangModel::withTrashed()->find($id)
            : gudangModel::withTrashed()->find($id);

        if (!$model) {
            $this->dispatch('alert', [
                'title' => 'Error!',
                'text' => 'Data tidak ditemukan',
                'icon' => 'error',
            ]);
            return;
        }

        $this->dispatch('confirm', [
            'title' => 'Hapus ' . ($this->mode === 'branch' ? 'cabang' : 'gudang') . ' ini?',
            'text' => 'Data "' . $model->name . '" akan dihapus. Tindakan ini tidak bisa dibatalkan!',
            'icon' => 'warning',
            'confirmButtonText' => 'Ya, Hapus!',
            'cancelButtonText' => 'Batal',
            'event' => 'deleteConfirmed'
        ]);
    }

    #[On('deleteConfirmed')]
    public function delete()
    {
        // dd($this->deleteId);
        if (!$this->deleteId) {
            return;
        }

        try {
            DB::beginTransaction();

            $model = $this->mode === 'branch'
                ? cabangModel::withTrashed()->find($this->deleteId)
                : gudangModel::withTrashed()->find($this->deleteId);

            if (!$model) {
                throw new \Exception('Data tidak ditemukan');
            }

            // Validasi untuk cabang
            if ($this->mode === 'branch') {
                if ($model->is_head_office) {
                    throw new \Exception('Tidak dapat menghapus cabang pusat');
                }

                // Cek apakah masih punya gudang
                if ($model->toGudang()->count() > 0) {
                    throw new \Exception('Tidak dapat menghapus cabang yang masih memiliki gudang');
                }

                // Cek apakah masih punya user
                if ($model->toUsers()->count() > 0) {
                    throw new \Exception('Tidak dapat menghapus cabang yang masih memiliki user');
                }
            }

            // Validasi untuk gudang
            if ($this->mode === 'warehouse') {
                // Cek apakah masih punya stok
                if ($model->toStocks()->where('quantity', '>', 0)->exists()) {
                    throw new \Exception('Tidak dapat menghapus gudang yang masih memiliki stok');
                }
            }

            $deletedName = $model->name;
            $model->delete();

            DB::commit();

            $this->dispatch('alert', [
                'title' => 'Berhasil!',
                'text' => ($this->mode === 'branch' ? 'Cabang' : 'Gudang') . ' "' . $deletedName . '" berhasil dihapus!',
                'icon' => 'success',
                'timer' => 2000,
            ]);

            $this->deleteId = null;
            $this->resetPage();
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('alert', [
                'title' => 'Gagal!',
                'text' => $e->getMessage(),
                'icon' => 'error',
                'timer' => 3000,
            ]);

            $this->deleteId = null;
        }
    }

    public function toggleTrashed()
    {
        $this->showTrashed = !$this->showTrashed;
        $this->resetPage();
    }

    // ===========================================================
    // ♻️ RESTORE DATA
    // ===========================================================
    public function restore($id)
    {
        $model = $this->mode === 'branch' ? cabangModel::onlyTrashed()->find($id) : gudangModel::onlyTrashed()->find($id);

        if (!$model) {
            $this->dispatch('alert', [
                'title' => 'Gagal!',
                'text' => 'Data tidak ditemukan.',
                'icon' => 'error',
            ]);
            return;
        }

        $model->restore();

        $this->dispatch('alert', [
            'title' => 'Berhasil!',
            'text' => 'Data "' . $model->name . '" berhasil dipulihkan.',
            'icon' => 'success',
        ]);

        $this->resetPage();
    }

    // ===========================================================
    // ❌ HAPUS PERMANEN
    // ===========================================================

        public $forceDeleteId = null; // Store ID untuk konfirmasi delete
    public function confirmForceDelete($id)
    {
        $this->forceDeleteId = $id;

        $this->dispatch('confirm', [
            'title' => 'Hapus Permanen?',
            'text' => 'Data ini akan dihapus selamanya dan tidak bisa dikembalikan!',
            'icon' => 'error',
            'confirmButtonText' => 'Ya, Hapus Permanen!',
            'cancelButtonText' => 'Batal',
            'event' => 'forceDeleteConfirmed',
            // 'id' => $id,
        ]);
    }

    #[On('forceDeleteConfirmed')]
    public function forceDelete()
    {
        $id = $this->forceDeleteId;
        // dd($id);
        $model = $this->mode === 'branch' ? cabangModel::onlyTrashed()->find($id) : gudangModel::onlyTrashed()->find($id);

        if (!$model) {
            $this->dispatch('alert', [
                'title' => 'Gagal!',
                'text' => 'Data tidak ditemukan.',
                'icon' => 'error',
            ]);
            return;
        }

        $deletedName = $model->name;
        $model->forceDelete();

        $this->dispatch('alert', [
            'title' => 'Berhasil!',
            'text' => 'Data "' . $deletedName . '" telah dihapus permanen.',
            'icon' => 'success',
        ]);

        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
