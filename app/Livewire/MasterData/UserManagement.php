<?php

namespace App\Livewire\MasterData;

use App\Models\User;
use Livewire\Component;
use App\Models\roleModels;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use App\Models\branchesModel;
use App\Models\cabangModel;

class UserManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $editingId = null;

    public $name, $email, $username, $password, $role_id, $branch_id, $appearance;

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            // 'email' => 'required|email',
            'username' => 'required|string|max:100',
            'role_id' => 'required|exists:role_models,id',
            'branch_id' => 'nullable|exists:branch_models,id',
            'appearance' => 'nullable|string',
        ];

        // Hanya wajib password jika create
        if (!$this->editingId) {
            $rules['password'] = 'required|min:6';
        }

        return $rules;
    }
    public function render()
    {
        $users = User::with(['toRole', 'toCabang'])
            ->search($this->search)
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
        $this->reset(['editingId', 'name', 'email', 'username', 'password', 'role_id', 'branch_id', 'appearance']);
    }

    public function create()
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            // 'email' => $this->email,
            'username' => $this->username,
            'password' => Hash::make($this->password),
            'role_id' => $this->role_id,
            'branch_id' => $this->branch_id,
            'appearance' => $this->appearance ?? 'default',
        ]);

        session()->flash('success', 'User berhasil ditambahkan!');
        $this->resetForm();
    }

    public function edit($id)
    {
        $user = User::find($id);
        if (!$user) return;

        $this->editingId = $user->id;
        $this->name = $user->name;
        // $this->email = $user->email;
        $this->username = $user->username;
        $this->role_id = $user->role_id;
        $this->branch_id = $user->branch_id;
        $this->appearance = $user->appearance;
    }

    public function update()
    {
        $this->validate();

        $user = User::find($this->editingId);
        if (!$user) {
            session()->flash('error', 'User tidak ditemukan!');
            return;
        }

        $data = [
            'name' => $this->name,
            // 'email' => $this->email,
            'username' => $this->username,
            'role_id' => $this->role_id,
            'branch_id' => $this->branch_id,
            'appearance' => $this->appearance ?? 'default',
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        $user->update($data);

        session()->flash('success', 'User berhasil diperbarui!');
        $this->resetForm();
    }

    public function delete($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            session()->flash('success', 'User berhasil dihapus!');
        }
    }
}
