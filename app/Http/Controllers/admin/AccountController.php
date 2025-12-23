<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%");
        }

        $users = $query->latest()->paginate(10);
        return view('admin.account.index', compact('users'));
    }

    // Di dalam AccountController.php

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin', // Set otomatis ke admin
        ]);

        return back()->with('success', 'Akun admin berhasil dibuat!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        // Role tetap admin
        $user->save();

        return back()->with('success', 'Data akun berhasil diperbarui!');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return back()->with('success', 'Akun telah dihapus!');
    }
}
