<?php

namespace App\Http\Controllers\Manage;

use App\Http\Requests\Manage\StoreUserRequest;
use App\Http\Requests\Manage\UpdateUserRequest;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        $users = User::query()
            ->withoutSuperAdmins()
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        $roles = User::assignableRoles();

        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active', true);

        User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
            'active' => $data['active'],
        ]);

        return redirect()
            ->route('users.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Kullanıcı oluşturuldu',
                'message' => 'Yeni kullanıcı kaydedildi.',
            ]);
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        $roles = User::assignableRoles();
        if (auth()->user()?->role === User::ROLE_CUSTOMER_SERVICE) {
            $roles = array_filter($roles, fn ($k) => $k === User::ROLE_CUSTOMER_SERVICE, ARRAY_FILTER_USE_KEY);
        }

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active', true);

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
            'active' => $data['active'],
        ];

        if (! empty($data['password'])) {
            $payload['password'] = $data['password'];
        }

        $user->update($payload);

        return redirect()
            ->route('users.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Güncellendi',
                'message' => 'Kullanıcı bilgileri kaydedildi.',
            ]);
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Silindi',
                'message' => 'Kullanıcı kaldırıldı.',
            ]);
    }
}
