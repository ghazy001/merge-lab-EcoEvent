<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $users = User::when($q, function($qry) use ($q) {
            $qry->where(function($sub) use ($q) {
                $sub->where('name','like',"%{$q}%")
                    ->orWhere('email','like',"%{$q}%");
            });
        })
            ->latest()
            ->paginate(12)
            ->appends(['q'=>$q]);

        return view('admin.users.index', compact('users','q'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'       => ['required','string','max:255'],
            'email'      => ['required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'role'       => ['required', Rule::in(['user','admin'])],
            'is_banned'  => ['nullable','boolean'],
            'ban_reason' => ['nullable','string','max:255'],
        ]);

        if (auth()->id() === $user->id) {
            if ($data['role'] !== 'admin') {
                return back()->withErrors(['role' => "You can't demote yourself."]);
            }
            if (!empty($data['is_banned'])) {
                return back()->withErrors(['is_banned' => "You can't ban yourself."]);
            }
        }

        $user->update([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'role'       => $data['role'],
            'is_banned'  => (bool)($data['is_banned'] ?? false),
            'ban_reason' => $data['ban_reason'] ?? null,
        ]);

        return redirect()->route('admin.users.index')->with('success','User updated.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->withErrors(['user' => "You can't delete your own account."]);
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success','User deleted.');
    }

    public function ban(Request $request, User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->withErrors(['user' => "You can't ban yourself."]);
        }
        $user->update([
            'is_banned'  => true,
            'ban_reason' => $request->string('ban_reason')->toString(),
        ]);
        return back()->with('success', 'User banned.');
    }

    public function unban(User $user)
    {
        $user->update(['is_banned' => false, 'ban_reason' => null]);
        return back()->with('success', 'User unbanned.');
    }
}
