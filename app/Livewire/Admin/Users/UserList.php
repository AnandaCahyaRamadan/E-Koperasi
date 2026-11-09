<?php

namespace App\Livewire\Admin\Users;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $filterRole = 'all';

    public $userId;
    public $role_id;
    public $name;
    public $email;
    public $phone;
    public $employee_id;
    public $password;
    public $is_active = true;
    public $showModal = false;

    public function openCreateModal()
    {
        $this->reset(['userId', 'role_id', 'name', 'email', 'phone', 'employee_id', 'password', 'is_active']);
        $this->is_active = true;
        $this->role_id = Role::where('slug', 'pelanggan')->value('id') ?? 3;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->role_id = $user->role_id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->employee_id = $user->employee_id;
        $this->password = '';
        $this->is_active = (bool)$user->is_active;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function save()
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->userId)],
            'role_id' => ['required', 'exists:roles,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'employee_id' => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($this->userId)],
        ];

        if (!$this->userId) {
            $rules['password'] = ['required', 'string', 'min:6'];
        } else {
            $rules['password'] = ['nullable', 'string', 'min:6'];
        }

        $this->validate($rules);

        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            $data = [
                'role_id' => $this->role_id,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'employee_id' => $this->employee_id,
                'is_active' => $this->is_active,
            ];

            if (!empty($this->password)) {
                $data['password'] = Hash::make($this->password);
            }

            $user->update($data);
            session()->flash('success', "Pengguna {$user->name} berhasil diperbarui.");
        } else {
            User::create([
                'role_id' => $this->role_id,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'employee_id' => $this->employee_id,
                'password' => Hash::make($this->password),
                'is_active' => $this->is_active,
            ]);
            session()->flash('success', 'Pengguna baru berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function toggleActive($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            session()->flash('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
            return;
        }

        $user->update(['is_active' => !$user->is_active]);
        session()->flash('success', "Status akun {$user->name} berhasil diperbarui.");
    }

    public function render()
    {
        $roles = Role::all();

        $query = User::with('role')->latest();

        if ($this->filterRole !== 'all') {
            $query->where('role_id', $this->filterRole);
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('employee_id', 'like', "%{$this->search}%");
            });
        }

        $users = $query->paginate(10);

        return view('livewire.admin.users.user-list', [
            'users' => $users,
            'roles' => $roles,
        ])->layout('layouts.admin');
    }
}

