@extends('layouts.app')

@section('title', 'Manajemen Akun')

@section('content')
    <div class="p-8">
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">Manajemen Akun</h1>
                <p class="text-gray-500 mt-1">Kelola daftar pengguna administrator sistem.</p>
            </div>

            <div class="flex items-center space-x-4">
                <form action="{{ request()->url() }}" method="GET" class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari email atau nama..."
                        class="bg-white border border-gray-200 text-gray-600 text-sm rounded-xl pl-10 pr-4 py-2.5 w-64 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm transition-all">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                </form>

                <button onclick="openAddModal()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-blue-200 transition-all flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Akun
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-emerald-600 text-white p-4 rounded-xl shadow-lg mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-blue-900 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-blue-900 uppercase tracking-wider">Dibuat Pada
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-blue-900 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach ($users as $user)
                        <tr class="hover:bg-blue-50/30 transition-colors duration-200">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div
                                        class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold mr-3">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 italic">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center space-x-2">
                                    <button onclick="openEditModal({{ json_encode($user) }})"
                                        class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </button>
                                    @if (auth()->id() !== $user->id)
                                        <button onclick="openDeleteModal({{ $user->id }}, '{{ $user->name }}')"
                                            class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="accountModal"
        class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8 transform transition-all">
            <h2 id="modalTitle" class="text-2xl font-bold text-gray-800 mb-6">Tambah Akun Baru</h2>
            <form id="accountForm" method="POST">
                @csrf
                <input type="hidden" id="methodField" name="_method" value="POST">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" id="userName" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" id="userEmail" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Password <span id="pwdNote"
                                class="text-[10px] font-normal text-gray-400"></span></label>
                        <input type="password" name="password" id="userPassword"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <input type="hidden" name="role" value="admin">
                </div>

                <div class="flex space-x-3 mt-8">
                    <button type="button" onclick="closeModal('accountModal')"
                        class="flex-1 px-4 py-3 border border-gray-200 text-gray-500 rounded-xl font-bold hover:bg-gray-50 transition-all">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all">Simpan
                        Akun</button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteModal"
        class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8 transform transition-all text-center">
            <div class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Hapus Akun?</h2>
            <p class="text-gray-500 mb-8">Apakah Anda yakin ingin menghapus akun <span id="deleteUserName"
                    class="font-bold text-gray-800"></span>? Tindakan ini tidak dapat dibatalkan.</p>

            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex space-x-3">
                    <button type="button" onclick="closeModal('deleteModal')"
                        class="flex-1 px-4 py-3 border border-gray-200 text-gray-500 rounded-xl font-bold hover:bg-gray-50 transition-all">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 shadow-lg shadow-red-200 transition-all">Ya,
                        Hapus</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Logic for Add/Edit Modal
        const accountModal = document.getElementById('accountModal');
        const accountForm = document.getElementById('accountForm');
        const modalTitle = document.getElementById('modalTitle');
        const methodField = document.getElementById('methodField');

        function openAddModal() {
            accountForm.reset();
            modalTitle.innerText = "Tambah Akun Baru";
            accountForm.action = "{{ route('admin.account.store') }}";
            methodField.value = "POST";
            document.getElementById('pwdNote').innerText = "";
            document.getElementById('userPassword').required = true;
            accountModal.classList.remove('hidden');
        }

        function openEditModal(user) {
            modalTitle.innerText = "Edit Data Akun";
            accountForm.action = `/admin/account/update/${user.id}`;
            methodField.value = "PUT";

            document.getElementById('userName').value = user.name;
            document.getElementById('userEmail').value = user.email;
            document.getElementById('pwdNote').innerText = "(Kosongkan jika tidak ganti)";
            document.getElementById('userPassword').required = false;

            accountModal.classList.remove('hidden');
        }

        // Logic for Delete Modal
        const deleteModal = document.getElementById('deleteModal');
        const deleteForm = document.getElementById('deleteForm');
        const deleteUserName = document.getElementById('deleteUserName');

        function openDeleteModal(id, name) {
            deleteUserName.innerText = name;
            deleteForm.action = `/admin/account/delete/${id}`;
            deleteModal.classList.remove('hidden');
        }
    </script>
@endsection
