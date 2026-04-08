@extends('layouts.admin')

@section('title', 'Manajemen User')

@section('content')
<div class="p-6">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen User</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola staf klinik, akun akses, dan penugasan peran sistem.</p>
        </div>
        @canDo('manajemen_user', 'add')
        <button onclick="openAddModal()" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg shadow transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah User
        </button>
        @endCanDo
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

    {{-- Filter Bar --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-5">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <div class="relative">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan nama atau email..."
                           class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                </div>
            </div>
            <div>
                <select name="role" class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                    <option value="">Semua Peran</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->slug }}" {{ request('role') == $r->slug ? 'selected' : '' }}>{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="status" class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <button type="submit" class="text-sm bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">Filter</button>
            @if(request()->hasAny(['search','role','status']))
                <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:text-gray-700 px-3 py-2">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Peran / Jabatan</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Terakhir Login</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 font-semibold flex items-center justify-center text-sm flex-shrink-0">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        @php
                            $roleColors = [
                                'admin'     => 'bg-indigo-100 text-indigo-700',
                                'owner'     => 'bg-purple-100 text-purple-700',
                                'doctor'    => 'bg-teal-100 text-teal-700',
                                'frontdesk' => 'bg-yellow-100 text-yellow-700',
                            ];
                            $color = $roleColors[$user->role] ?? 'bg-gray-100 text-gray-700';
                            $roleName = $user->roleModel?->name ?? ucfirst($user->role);
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                            {{ $roleName }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        @if($user->is_active !== false)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Nonaktif
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-sm text-gray-500">
                        {{ $user->last_login_at ? $user->last_login_at->format('d M Y, H:i') : '-' }}
                    </td>
                    <td class="px-5 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                    {{-- View --}}
                            <button onclick="openViewModal({{ $user->id }})" class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded transition" title="Lihat Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                            {{-- Edit --}}
                            @canDo('manajemen_user', 'edit')
                            <button
                                data-id="{{ $user->id }}"
                                onclick="openEditModal(this.dataset.id)"
                                class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded transition" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            @endCanDo
                            {{-- Delete --}}
                            @canDo('manajemen_user', 'delete')
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                @csrf @method('DELETE')
                                <button class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                            @endif
                            @endCanDo
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-12 text-center text-gray-400 text-sm">
                        <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Tidak ada user ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- User data lookup for JS modals --}}
    <script>
    const usersMap = {
        @foreach($users as $u)
        {{ $u->id }}: {
            id:        {{ $u->id }},
            name:      @json($u->name),
            email:     @json($u->email),
            role:      @json($u->role),
            role_id:   {{ $u->role_id ?? 'null' }},
            is_active: {{ $u->is_active ? 'true' : 'false' }},
        },
        @endforeach
    };
    </script>

    {{-- Pagination --}}
    @if($users->hasPages())
    <div class="mt-4 flex items-center justify-between text-sm text-gray-500">
        <p>Menampilkan {{ $users->firstItem() }}-{{ $users->lastItem() }} dari {{ $users->total() }} user</p>
        <div class="flex items-center gap-1">
            {{-- Previous --}}
            @if($users->onFirstPage())
                <span class="px-3 py-1.5 rounded-lg text-gray-300 border border-gray-100 cursor-not-allowed">← Sebelumnya</span>
            @else
                <a href="{{ $users->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg text-gray-600 border border-gray-200 hover:bg-gray-50 transition">← Sebelumnya</a>
            @endif

            @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                @if($page == $users->currentPage())
                    <span class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white font-medium">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="px-3 py-1.5 rounded-lg text-gray-600 border border-gray-200 hover:bg-gray-50 transition">{{ $page }}</a>
                @endif
            @endforeach

            @if($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg text-gray-600 border border-gray-200 hover:bg-gray-50 transition">Selanjutnya →</a>
            @else
                <span class="px-3 py-1.5 rounded-lg text-gray-300 border border-gray-100 cursor-not-allowed">Selanjutnya →</span>
            @endif
        </div>
    </div>
    @endif

</div>

{{-- ===================== ADD USER MODAL ===================== --}}
<div id="addModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeAddModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">Tambah User Baru</h3>
                        <p class="text-sm text-gray-500 mt-0.5">Lengkapi formulir di bawah untuk menambahkan staf baru ke dalam sistem klinik.</p>
                    </div>
                    <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" required placeholder="Masukkan nama staf..."
                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" required placeholder="email@klinik.com"
                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" required placeholder="Min. 8 karakter"
                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role / Jabatan</label>
                        <select name="role_id" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                            <option value="">— Pilih Role —</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="px-6 pb-6 flex justify-end gap-3">
                    <button type="button" onclick="closeAddModal()" class="px-4 py-2 text-sm text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-50 transition">Batal</button>
                    <button type="submit" class="px-5 py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Simpan &amp; Tambahkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================== EDIT USER MODAL ===================== --}}
<div id="editModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeEditModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">Edit User</h3>
                        <p class="text-sm text-gray-500 mt-0.5">Perbarui informasi akun staf.</p>
                    </div>
                    <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            <form id="editForm" method="POST" action="">
                @csrf @method('PUT')
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" id="editName" name="name" required
                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="editEmail" name="email" required
                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru <span class="text-gray-400 font-normal">(kosongkan jika tidak diubah)</span></label>
                        <input type="password" name="password" placeholder="Min. 8 karakter"
                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role / Jabatan</label>
                        <select id="editRoleId" name="role_id" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                            <option value="">— Pilih Role —</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                        <span class="text-sm text-gray-700 font-medium">Status Akun</span>
                        <button type="button" id="toggleStatusBtn"
                                onclick="submitToggleStatus()"
                                class="text-sm px-4 py-1.5 rounded-lg border transition font-medium">
                        </button>
                    </div>
                </div>
                <div class="px-6 pb-6 flex justify-end gap-3">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-50 transition">Batal</button>
                    <button type="submit" class="px-5 py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Toggle status form (outside modal to avoid nested form) --}}
<form id="toggleStatusForm" method="POST" action="" class="hidden">
    @csrf
</form>

{{-- ===================== VIEW USER MODAL ===================== --}}
<div id="viewModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeViewModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Detail User</h3>
                <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-4" id="viewContent">
                <div class="flex items-center gap-4">
                    <div id="viewAvatar" class="w-14 h-14 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xl flex items-center justify-center flex-shrink-0"></div>
                    <div>
                        <p id="viewName" class="text-base font-semibold text-gray-900"></p>
                        <p id="viewEmail" class="text-sm text-gray-400"></p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-400 mb-1">Peran</p>
                        <p id="viewRole" class="font-medium text-gray-800"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-400 mb-1">Status</p>
                        <p id="viewStatus" class="font-medium"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-400 mb-1">Terakhir Login</p>
                        <p id="viewLastLogin" class="font-medium text-gray-800"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-400 mb-1">Bergabung</p>
                        <p id="viewCreatedAt" class="font-medium text-gray-800"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const toggleRouteBase = "{{ url('admin/users') }}";
const roleLabels = { admin:'Admin', owner:'Owner', doctor:'Doctor', frontdesk:'Frontdesk' };

// ---- ADD ----
function openAddModal() { document.getElementById('addModal').classList.remove('hidden'); }
function closeAddModal() { document.getElementById('addModal').classList.add('hidden'); }

// ---- EDIT ----
function openEditModal(id) {
    const user = usersMap[id];
    if (!user) return;

    document.getElementById('editForm').action           = `${toggleRouteBase}/${id}`;
    document.getElementById('toggleStatusForm').action   = `${toggleRouteBase}/${id}/toggle-status`;
    document.getElementById('editName').value            = user.name;
    document.getElementById('editEmail').value           = user.email;

    // Set role dropdown
    const sel = document.getElementById('editRoleId');
    sel.value = user.role_id || '';

    // Toggle status button
    const statusBtn = document.getElementById('toggleStatusBtn');
    if (user.is_active) {
        statusBtn.textContent = 'Nonaktifkan';
        statusBtn.className   = 'text-sm px-4 py-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 transition font-medium';
    } else {
        statusBtn.textContent = 'Aktifkan';
        statusBtn.className   = 'text-sm px-4 py-1.5 rounded-lg border border-green-200 text-green-600 hover:bg-green-50 transition font-medium';
    }

    document.getElementById('editModal').classList.remove('hidden');
}
function closeEditModal() { document.getElementById('editModal').classList.add('hidden'); }

function submitToggleStatus() {
    if (confirm('Yakin ingin mengubah status akun ini?')) {
        document.getElementById('toggleStatusForm').submit();
    }
}

// ---- VIEW ----
function openViewModal(id) {
    fetch(`${toggleRouteBase}/${id}`)
        .then(r => r.json())
        .then(u => {
            document.getElementById('viewAvatar').textContent = u.name.charAt(0).toUpperCase();
            document.getElementById('viewName').textContent = u.name;
            document.getElementById('viewEmail').textContent = u.email;
            document.getElementById('viewRole').textContent = roleLabels[u.role] || u.role;
            const statusEl = document.getElementById('viewStatus');
            if (u.is_active !== false) {
                statusEl.textContent = 'Aktif';
                statusEl.className = 'font-medium text-green-600';
            } else {
                statusEl.textContent = 'Nonaktif';
                statusEl.className = 'font-medium text-red-500';
            }
            document.getElementById('viewLastLogin').textContent = u.last_login_at || '-';
            document.getElementById('viewCreatedAt').textContent = u.created_at;
            document.getElementById('viewModal').classList.remove('hidden');
        });
}
function closeViewModal() { document.getElementById('viewModal').classList.add('hidden'); }

// Close modals on ESC
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeAddModal(); closeEditModal(); closeViewModal(); }
});
</script>
@endpush
