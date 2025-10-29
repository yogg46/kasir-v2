<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\supliersModels;
use Livewire\Attributes\Computed;

class SupplierManagement extends Component
{

    use WithPagination;

    // Properties untuk form
    public $supplier_id = null;
    public $code = '';
    public $name = '';
    public $address = '';
    public $phone = '';

    // Properties untuk UI
    public $search = '';
    public $perPage = 10;
    public $showModal = false;
    public $isEdit = false;
    public $showDeletedOnly = false;

    // Untuk sorting
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    /**
     * Validation rules
     */
    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]*$/'],
        ];
    }

    /**
     * Custom validation messages
     */
    protected $messages = [
        'name.required' => 'Nama supplier wajib diisi',
        'name.max' => 'Nama supplier maksimal 255 karakter',
        'address.max' => 'Alamat maksimal 500 karakter',
        'phone.max' => 'Nomor telepon maksimal 20 karakter',
        'phone.regex' => 'Format nomor telepon tidak valid',
    ];

    /**
     * Reset pagination when search changes
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when perPage changes
     */
    public function updatingPerPage()
    {
        $this->resetPage();
    }

    /**
     * Get suppliers with search and filters
     */
    #[Computed]
    public function suppliers()
    {
        $query = supliersModels::query();

        // Show deleted or not
        if ($this->showDeletedOnly) {
            $query->onlyTrashed();
        }

        // Search
        if ($this->search) {
            $query->search($this->search);
        }

        // Sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    /**
     * Sort by field
     */
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Open modal for create
     */
    public function create()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->showModal = true;
    }

    /**
     * Open modal for edit
     */
    public function edit($id)
    {
        $supplier = supliersModels::findOrFail($id);
        
        $this->supplier_id = $supplier->id;
        $this->code = $supplier->code;
        $this->name = $supplier->name;
        $this->address = $supplier->address ?? '';
        $this->phone = $supplier->phone ?? '';
        
        $this->isEdit = true;
        $this->showModal = true;
    }

    /**
     * Save supplier (create or update)
     */
    public function save()
    {
        $this->validate();

        try {
            if ($this->isEdit) {
                // Update
                $supplier = supliersModels::findOrFail($this->supplier_id);
                $supplier->update([
                    'name' => $this->name,
                    'address' => $this->address,
                    'phone' => $this->phone,
                ]);

                $this->dispatch('alert', [
                    'title' => 'Berhasil!',
                    'text' => 'Data supplier berhasil diperbarui',
                    'icon' => 'success',
                ]);
            } else {
                // Create
                supliersModels::create([
                    'name' => $this->name,
                    'address' => $this->address,
                    'phone' => $this->phone,
                ]);

                $this->dispatch('alert', [
                    'title' => 'Berhasil!',
                    'text' => 'Data supplier berhasil ditambahkan',
                    'icon' => 'success',
                ]);
            }

            $this->closeModal();
            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'title' => 'Error!',
                'text' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'icon' => 'error',
            ]);
        }
    }

    /**
     * Confirm delete
     */
    public function confirmDelete($id)
    {
        $supplier = supliersModels::findOrFail($id);

        $this->dispatch('confirm', [
            'title' => 'Hapus Supplier?',
            'text' => "Apakah Anda yakin ingin menghapus supplier '{$supplier->name}'?",
            'icon' => 'warning',
            'confirmButtonText' => 'Ya, Hapus!',
            'cancelButtonText' => 'Batal',
            'event' => 'delete-confirmed',
        ]);

        $this->supplier_id = $id;
    }

    /**
     * Delete supplier
     */
    #[On('delete-confirmed')]
    public function delete()
    {
        try {
            $supplier = supliersModels::findOrFail($this->supplier_id);
            $name = $supplier->name;
            $supplier->delete();

            $this->dispatch('alert', [
                'title' => 'Terhapus!',
                'text' => "Supplier '{$name}' berhasil dihapus",
                'icon' => 'success',
            ]);

            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'title' => 'Error!',
                'text' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'icon' => 'error',
            ]);
        }
    }

    /**
     * Restore deleted supplier
     */
    public function restore($id)
    {
        try {
            $supplier = supliersModels::withTrashed()->findOrFail($id);
            $supplier->restore();

            $this->dispatch('alert', [
                'title' => 'Berhasil!',
                'text' => "Supplier '{$supplier->name}' berhasil dipulihkan",
                'icon' => 'success',
            ]);

            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'title' => 'Error!',
                'text' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'icon' => 'error',
            ]);
        }
    }

    /**
     * Permanent delete
     */
    public function forceDelete($id)
    {
        $this->dispatch('confirm', [
            'title' => 'Hapus Permanen?',
            'text' => 'Data ini akan dihapus secara permanen dan tidak dapat dipulihkan!',
            'icon' => 'warning',
            'confirmButtonText' => 'Ya, Hapus Permanen!',
            'cancelButtonText' => 'Batal',
            'event' => 'force-delete-confirmed',
        ]);

        $this->supplier_id = $id;
    }

    /**
     * Force delete confirmed
     */
    #[On('force-delete-confirmed')]
    public function forceDeleteConfirmed()
    {
        try {
            $supplier = supliersModels::withTrashed()->findOrFail($this->supplier_id);
            $name = $supplier->name;
            $supplier->forceDelete();

            $this->dispatch('alert', [
                'title' => 'Terhapus Permanen!',
                'text' => "Supplier '{$name}' berhasil dihapus secara permanen",
                'icon' => 'success',
            ]);

            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'title' => 'Error!',
                'text' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'icon' => 'error',
            ]);
        }
    }

    /**
     * Toggle show deleted
     */
    public function toggleShowDeleted()
    {
        $this->showDeletedOnly = !$this->showDeletedOnly;
        $this->resetPage();
    }

    /**
     * Close modal
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    /**
     * Reset form
     */
    private function resetForm()
    {
        $this->supplier_id = null;
        $this->code = '';
        $this->name = '';
        $this->address = '';
        $this->phone = '';
    }
    
    public function render()
    {
        return view('livewire.master-data.supplier-management');
    }
}
