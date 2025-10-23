<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use Livewire\WithPagination;
// use App\Models\branchesModel as branchModel;
use App\Models\cabangModel;
use App\Models\gudangModel;
// use App\Models\warehosesModels as warehouseModels;

class CabangToko extends Component
{
    use WithPagination;

    public $mode = 'branch'; // branch | warehouse
    public $search = '';
    public $perPage = 10;

    // Form fields
    public $name, $address, $phone, $description, $editingId = null;
    public $branch_id; // hanya untuk gudang

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ];

        if ($this->mode === 'warehouse') {
            $rules['branch_id'] = 'required|exists:branch_models,id';
        }

        return $rules;
    }

    public function setMode($mode)
    {
        $this->resetForm();
        $this->mode = $mode;
        $this->resetPage();
    }

    public function render()
    {
        $query = $this->mode === 'branch' ? cabangModel::query() : gudangModel::with('toCabang');

        $items = $query->search($this->search)
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $branches = gudangModel::all();

        return view('livewire.master-data.cabang-toko', [
            'items' => $items,
            'branches' => $branches,
        ]);
    }

    public function resetForm()
    {
        $this->reset(['name', 'address', 'phone', 'description', 'editingId', 'branch_id']);
    }

    public function create()
    {
        $this->validate();

        $model = $this->mode === 'branch' ? new cabangModel() : new gudangModel();

        $data = [
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'description' => $this->description,
        ];

        if ($this->mode === 'warehouse') {
            $data['branch_id'] = $this->branch_id;
        }

        $model->fill($data);
        $model->save();

        session()->flash('success', ucfirst($this->mode) . ' berhasil ditambahkan!');
        $this->resetForm();
    }

    public function edit($id)
    {
        $model = $this->mode === 'branch' ? cabangModel::find($id) : gudangModel::find($id);
        if (!$model) return;

        $this->editingId = $model->id;
        $this->name = $model->name;
        $this->address = $model->address;
        $this->phone = $model->phone;
        $this->description = $model->description;
        $this->branch_id = $model->branch_id ?? null;
    }

    public function update()
    {
        $this->validate();

        $model = $this->mode === 'branch' ? cabangModel::find($this->editingId) : gudangModel::find($this->editingId);
        if (!$model) {
            session()->flash('error', ucfirst($this->mode) . ' tidak ditemukan!');
            return;
        }

        $data = [
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'description' => $this->description,
        ];

        if ($this->mode === 'warehouse') {
            $data['branch_id'] = $this->branch_id;
        }

        $model->update($data);

        session()->flash('success', ucfirst($this->mode) . ' berhasil diperbarui!');
        $this->resetForm();
    }

    public function delete($id)
    {
        $model = $this->mode === 'branch' ? cabangModel::find($id) : gudangModel::find($id);
        if ($model) {
            $model->delete();
            session()->flash('success', ucfirst($this->mode) . ' berhasil dihapus!');
        }
    }
}
