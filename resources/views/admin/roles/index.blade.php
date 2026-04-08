@extends('layouts.admin')

@section('title', 'Manajemen Role')

@section('content')
<div class="p-6">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Role</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola hak akses dan perizinan staf klinik Anda.</p>
        </div>
        <div class="flex items-center gap-2">
            @canDo('manajemen_role', 'add')
            <button onclick="openCreateModal()" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg shadow transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Role Baru
            </button>
            @endCanDo
        </div>
    </div>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-lg px-4 py-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/></svg>
            {{ $errors->first() }}
        </div>
    @endif

    <div class="flex gap-6">
        {{-- ===== LEFT: Role List ===== --}}
        <aside class="w-64 flex-shrink-0 space-y-2">
            {{-- Search --}}
            <div class="relative mb-3">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z"/></svg>
                <input type="text" id="roleSearch" placeholder="Cari role..." onkeyup="filterRoles()"
                       class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
            </div>

            @foreach($roles as $role)
            <button id="roleTab_{{ $role->id }}"
                    onclick="selectRole({{ $role->id }})"
                    class="role-tab w-full text-left px-4 py-3 rounded-xl border transition
                        {{ $selectedRole && $selectedRole->id === $role->id
                            ? 'bg-indigo-50 border-indigo-200 text-indigo-700'
                            : 'bg-white border-gray-100 text-gray-700 hover:bg-gray-50' }}">
                <p class="font-semibold text-sm">{{ $role->name }}</p>
                <p class="text-xs mt-0.5 {{ $selectedRole && $selectedRole->id === $role->id ? 'text-indigo-500' : 'text-gray-400' }}">
                    {{ $role->users_count }} User Aktif
                </p>
            </button>
            @endforeach

            {{-- Tip --}}
            <div class="mt-4 p-3 bg-blue-50 border border-blue-100 rounded-xl text-xs text-blue-700 leading-relaxed">
                <svg class="w-4 h-4 mb-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/></svg>
                <strong>Tips:</strong> Gunakan Role Manager untuk membatasi fitur staf Anda sendiri seperti Laporan Pendapatan dan Manajemen User.
            </div>
        </aside>

        {{-- ===== RIGHT: Role Detail ===== --}}
        <div class="flex-1">
            @if($selectedRole)
            {{-- Role Header --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 mb-5 flex items-start justify-between">
                <div class="flex items-start gap-3">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Detail Role: {{ $selectedRole->name }}</h2>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $selectedRole->description ?? 'Tidak ada deskripsi.' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0">
                    @canDo('manajemen_role', 'edit')
                    <button type="button" onclick="openEditModal()"
                            class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-600 border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-gray-50 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6.071-6.071a2 2 0 112.828 2.828L11.828 13.828A2 2 0 0110 14H8v-2a2 2 0 01.586-1.414z"/></svg>
                        Edit
                    </button>
                    @endCanDo
                    <span class="text-xs text-gray-400">ID: R{{ $selectedRole->id }}</span>
                    <div class="text-right">
                        <p class="text-xs text-gray-400 mb-1">Status Role</p>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium {{ $selectedRole->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $selectedRole->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                            {{ $selectedRole->is_active ? 'Aktif & Digunakan' : 'Nonaktif' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Permission Matrix --}}
            <form method="POST" action="{{ route('admin.roles.permissions', $selectedRole) }}">
                @csrf @method('PUT')
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100">
                        <p class="text-sm font-semibold text-gray-700">Permission Matrix</p>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="selectAllPermissions()" class="text-xs text-indigo-600 hover:underline">Pilih Semua</button>
                            <span class="text-gray-300">|</span>
                            <button type="button" onclick="resetPermissions()" class="text-xs text-gray-500 hover:underline">Reset</button>
                        </div>
                    </div>
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Fitur / Modul</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Lihat (View)</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Tambah (Add)</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Ubah (Edit)</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Hapus (Delete)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($modules as $moduleKey => $moduleLabel)
                            @php
                                $perms = $selectedRole->permissions[$moduleKey] ?? [];
                            @endphp
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-5 py-3.5 text-sm font-medium text-gray-800">{{ $moduleLabel }}</td>
                                @foreach(['view', 'add', 'edit', 'delete'] as $action)
                                <td class="px-5 py-3.5 text-center">
                                    <input type="checkbox"
                                           name="permissions[{{ $moduleKey }}][{{ $action }}]"
                                           value="1"
                                           class="perm-checkbox w-4 h-4 accent-indigo-600 rounded cursor-pointer"
                                           {{ !empty($perms[$action]) ? 'checked' : '' }}>
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex justify-end gap-2">
                    @canDo('manajemen_role', 'edit')
                    <form method="POST" action="{{ route('admin.roles.toggle-status', $selectedRole) }}" class="inline">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 text-sm border rounded-lg transition
                                    {{ $selectedRole->is_active
                                        ? 'border-red-200 text-red-600 hover:bg-red-50'
                                        : 'border-green-200 text-green-600 hover:bg-green-50' }}">
                            {{ $selectedRole->is_active ? 'Nonaktifkan Role' : 'Aktifkan Role' }}
                        </button>
                    </form>
                    <button type="submit" class="px-5 py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow">
                        Simpan Perubahan
                    </button>
                    @endCanDo
                </div>
            </form>
            @else
                <div class="flex flex-col items-center justify-center h-64 text-gray-400 text-sm">
                    <svg class="w-12 h-12 mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    Pilih role di sebelah kiri untuk melihat detail.
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ===================== CREATE ROLE MODAL ===================== --}}
<div id="createModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeCreateModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">Buat Role Baru</h3>
                        <p class="text-sm text-gray-500 mt-0.5">Tambahkan peran baru untuk staf klinik. Anda dapat mengatur izin aksesnya nanti.</p>
                    </div>
                    <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.roles.store') }}">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Role</label>
                        <input type="text" name="name" required placeholder="Contoh: Apoteker"
                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="description" rows="3" placeholder="Tugas dan tanggung jawab role ini..."
                                  class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none resize-none"></textarea>
                    </div>
                </div>
                <div class="px-6 pb-6 flex justify-end gap-3">
                    <button type="button" onclick="closeCreateModal()" class="px-4 py-2 text-sm text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-50 transition">Batal</button>
                    <button type="submit" class="px-5 py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Simpan Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

{{-- ===================== EDIT ROLE MODAL ===================== --}}
<div id="editModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeEditModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6.071-6.071a2 2 0 112.828 2.828L11.828 13.828A2 2 0 0110 14H8v-2a2 2 0 01.586-1.414z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">Edit Role</h3>
                        <p class="text-sm text-gray-500 mt-0.5">Ubah nama dan deskripsi role ini.</p>
                    </div>
                    <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            @if($selectedRole)
            <form method="POST" action="{{ route('admin.roles.update', $selectedRole) }}">
                @csrf @method('PUT')
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Role</label>
                        <input type="text" name="name" required
                               value="{{ $selectedRole->name }}"
                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="description" rows="3"
                                  placeholder="Tugas dan tanggung jawab role ini..."
                                  class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none resize-none">{{ $selectedRole->description }}</textarea>
                    </div>
                </div>
                <div class="px-6 pb-6 flex justify-end gap-3">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-50 transition">Batal</button>
                    <button type="submit" class="px-5 py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Simpan</button>
                </div>
            </form>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
const rolesData = @json($roles->keyBy('id'));
const routeBase = "{{ url('admin/roles') }}";

// Navigate to role detail by reloading with ?role= param
function selectRole(id) {
    window.location.href = `${routeBase}?selected=${id}`;
}

// Filter role list
function filterRoles() {
    const q = document.getElementById('roleSearch').value.toLowerCase();
    document.querySelectorAll('.role-tab').forEach(btn => {
        const name = btn.querySelector('p').textContent.toLowerCase();
        btn.style.display = name.includes(q) ? '' : 'none';
    });
}

// Select all permission checkboxes
function selectAllPermissions() {
    document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = true);
}

// Reset all permission checkboxes
function resetPermissions() {
    document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = false);
}

// Create modal
function openCreateModal() { document.getElementById('createModal').classList.remove('hidden'); }
function closeCreateModal() { document.getElementById('createModal').classList.add('hidden'); }

// Edit modal
function openEditModal() { document.getElementById('editModal').classList.remove('hidden'); }
function closeEditModal() { document.getElementById('editModal').classList.add('hidden'); }

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeCreateModal(); closeEditModal(); }
});
</script>
@endpush
