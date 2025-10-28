<?php

namespace App\Livewire\MasterData;

use App\Models\User;
use Livewire\Component;
use App\Models\roleModels;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use App\Models\cabangModel;
use Livewire\Attributes\On;

class UserManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $editingId = null;
    public $showTrashed = false;

    public $name, $username, $password, $role_id, $branch_id, $appearance;
    public $deleteId = null;

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100',
            'role_id' => 'required|exists:role_models,id',
            'branch_id' => 'nullable|exists:branches_models,id',
            'appearance' => 'nullable|string',
        ];

        if (!$this->editingId) {
            $rules['password'] = 'required|min:6';
        }

        return $rules;
    }

    public function render()
    {
        $query = User::with(['toRole', 'toCabang']);

        if ($this->showTrashed) {
            $query->onlyTrashed();
        }

        $users = $query
            ->when(
                $this->search,
                fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('username', 'like', "%{$this->search}%")
            )
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.master-data.user-management', [
            'users' => $users,
            'roles' => roleModels::select('id', 'role')->get(),
            'branches' => cabangModel::select('id', 'name')->get(),
        ]);
    }

    public function resetForm()
    {
        $this->reset(['editingId', 'name', 'username', 'password', 'role_id', 'branch_id', 'appearance']);
    }

    // ===========================================================
    // CRUD
    // ===========================================================
    public function create()
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'username' => $this->username,
            'password' => Hash::make($this->password),
            'role_id' => $this->role_id,
            'branch_id' => $this->branch_id,
            'appearance' => $this->appearance ?? 'default',
        ]);

        $this->dispatch('alert', [
            'title' => 'Berhasil!',
            'text' => 'User berhasil ditambahkan.',
            'icon' => 'success',
        ]);

        $this->resetForm();
    }

    public function edit($id)
    {
        $user = User::withTrashed()->find($id);
        if (!$user) {
            $this->dispatch('alert', [
                'title' => 'Error!',
                'text' => 'User tidak ditemukan.',
                'icon' => 'error',
            ]);
            return;
        }

        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->role_id = $user->role_id;
        $this->branch_id = $user->branch_id;
        $this->appearance = $user->appearance;
    }

    public function update()
    {
        $this->validate();

        $user = User::withTrashed()->find($this->editingId);
        if (!$user) {
            $this->dispatch('alert', [
                'title' => 'Error!',
                'text' => 'User tidak ditemukan.',
                'icon' => 'error',
            ]);
            return;
        }

        $data = [
            'name' => $this->name,
            'username' => $this->username,
            'role_id' => $this->role_id,
            'branch_id' => $this->branch_id,
            'appearance' => $this->appearance ?? 'default',
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        $user->update($data);

        $this->dispatch('alert', [
            'title' => 'Berhasil!',
            'text' => 'User berhasil diperbarui.',
            'icon' => 'success',
        ]);

        $this->resetForm();
    }

    // ===========================================================
    // SOFT DELETE, RESTORE, FORCE DELETE
    // ===========================================================
    public function confirmDelete($id)
    {
        $this->deleteId = $id;

        $this->dispatch('confirm', [
            'title' => 'Hapus User?',
            'text' => 'Data user ini akan dipindahkan ke arsip.',
            'event' => 'deleteConfirmed',
            'id' => $id,
        ]);
    }

    #[On('deleteConfirmed')]
    public function delete($data = null)
    {
        $id = is_array($data) ? ($data['id'] ?? $this->deleteId) : $this->deleteId;
        $user = User::find($id);

        if (!$user) {
            $this->dispatch('alert', [
                'title' => 'Error!',
                'text' => 'User tidak ditemukan.',
                'icon' => 'error',
            ]);
            return;
        }

        $user->delete();

        $this->dispatch('alert', [
            'title' => 'Berhasil!',
            'text' => 'User berhasil dihapus sementara.',
            'icon' => 'success',
        ]);
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->find($id);
        if ($user) {
            $user->restore();

            $this->dispatch('alert', [
                'title' => 'Dipulihkan!',
                'text' => 'User berhasil dikembalikan.',
                'icon' => 'success',
            ]);
        }
    }

    public function confirmForceDelete($id)
    {
        $this->dispatch('confirm', [
            'title' => 'Hapus Permanen?',
            'text' => 'Data ini akan dihapus selamanya!',
            'event' => 'forceDeleteConfirmed',
            'id' => $id,
        ]);
    }

    #[On('forceDeleteConfirmed')]
    public function forceDelete($data = null)
    {
        $id = is_array($data) ? ($data['id'] ?? null) : $this->deleteId;
        $user = User::onlyTrashed()->find($id);

        if ($user) {
            $user->forceDelete();

            $this->dispatch('alert', [
                'title' => 'Dihapus Permanen!',
                'text' => 'Data user telah dihapus secara permanen.',
                'icon' => 'error',
            ]);
        }
    }

    public function toggleTrashed()
    {
        $this->showTrashed = !$this->showTrashed;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
