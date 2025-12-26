<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $users = $query->latest()->paginate(10);
        return view('admin.account.index', compact('users'));
    }

    public function store(Request $request)
    {
        // PROTEKSI: Hanya superadmin@gmail.com yang bisa menambah akun
        if (Auth::user()->email !== 'superadmin@gmail.com') {
            return back()->with('error', 'Hanya Superadmin yang diperbolehkan menambah akun baru.');
        }

        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z0-9 ]+$/', // Tambahkan regex agar sinkron dengan UI
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        return back()->with('success', 'Akun admin berhasil dibuat!');
    }

    public function update(Request $request, $id)
    {
        // PROTEKSI: Jika bukan superadmin DAN bukan miliknya sendiri, maka tolak
        if (Auth::user()->email !== 'superadmin@gmail.com' && Auth::id() != $id) {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengubah akun orang lain.');
        }

        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z0-9 ]+$/',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8', // Jadikan nullable agar tidak wajib isi saat edit
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Data akun berhasil diperbarui!');
    }

    public function destroy($id)
    {
        // PROTEKSI: Hanya superadmin@gmail.com yang bisa menghapus
        if (Auth::user()->email !== 'superadmin@gmail.com') {
            return back()->with('error', 'Hanya Superadmin yang memiliki otoritas menghapus akun.');
        }

        // Jangan biarkan superadmin menghapus dirinya sendiri
        if (Auth::id() == $id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        User::findOrFail($id)->delete();
        return back()->with('success', 'Akun telah dihapus!');
    }
}
