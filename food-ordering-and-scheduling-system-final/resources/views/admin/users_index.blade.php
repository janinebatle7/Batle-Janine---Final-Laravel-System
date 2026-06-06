@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 animate-in fade-in duration-300"
     x-data="adminUsersHandler()">

    <!-- Header Area -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
        <div class="text-left">
            <div class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-100/60 px-4 py-2 rounded-full text-[10px] font-black uppercase text-emerald-600 tracking-wider shadow-sm mb-3">
                <i class="fas fa-users-cog"></i> Access Control
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">User & Staff Management</h1>
            <p class="text-xs font-bold text-slate-400 mt-1">Audit all platform users, manage permissions, and recruit official staff members</p>
        </div>

        <button @click="openAddStaffModal()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-black px-6 py-4 rounded-2xl text-xs uppercase tracking-wider transition-all shadow-lg shadow-emerald-200 hover:scale-[1.02] active:scale-95 cursor-pointer flex items-center justify-center gap-2 shrink-0 md:self-end">
            <i class="fas fa-user-plus text-xs"></i> Add Staff Member
        </button>
    </div>

    <!-- Error/Success Handling -->
    @if(session('error'))
        <div class="bg-red-500 text-white p-4 rounded-2xl shadow-lg shadow-red-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-xl"></i>
                <span class="font-bold text-xs">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50/80 border border-red-100 p-5 rounded-[2rem] space-y-2 text-left">
            <h4 class="text-xs font-black text-red-600 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-shield-alt"></i> Please correct the following errors:
            </h4>
            <ul class="list-disc list-inside text-xs text-red-500 font-bold space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Search & Filter Controls -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Search form -->
        <form action="{{ route('admin.users.index') }}" method="GET" class="relative group max-w-md w-full m-0">
            @if(request('role'))
                <input type="hidden" name="role" value="{{ request('role') }}">
            @endif
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search users by name or email..."
                class="w-full pl-12 pr-10 py-3.5 rounded-2xl bg-slate-50 border border-slate-200/60 font-bold focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition-all text-xs"
            >
            <i class="fas fa-search absolute left-4.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-emerald-500 transition-colors text-xs"></i>

            @if($search)
                <a href="{{ route('admin.users.index', ['role' => request('role')]) }}" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    <i class="fas fa-times-circle"></i>
                </a>
            @endif
        </form>

        <!-- Role Filters -->
        <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto pb-2 md:pb-0 Scrollbar-none">
            <a href="{{ route('admin.users.index', ['search' => $search]) }}"
               class="px-5 py-3 rounded-xl font-black text-[10px] uppercase tracking-wider transition-all border {{ !$role ? 'bg-slate-900 border-slate-900 text-white shadow-md' : 'bg-slate-50 border-slate-100 text-slate-500 hover:border-slate-300 hover:text-slate-700' }}">
                All Roles
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'admin', 'search' => $search]) }}"
               class="px-5 py-3 rounded-xl font-black text-[10px] uppercase tracking-wider transition-all border {{ $role === 'admin' ? 'bg-amber-600 border-amber-600 text-white shadow-md' : 'bg-slate-50 border-slate-100 text-slate-500 hover:border-slate-300 hover:text-slate-700' }}">
                Admins
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'staff', 'search' => $search]) }}"
               class="px-5 py-3 rounded-xl font-black text-[10px] uppercase tracking-wider transition-all border {{ $role === 'staff' ? 'bg-emerald-600 border-emerald-600 text-white shadow-md' : 'bg-slate-50 border-slate-100 text-slate-500 hover:border-slate-300 hover:text-slate-700' }}">
                Staff
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'customer', 'search' => $search]) }}"
               class="px-5 py-3 rounded-xl font-black text-[10px] uppercase tracking-wider transition-all border {{ $role === 'customer' ? 'bg-indigo-600 border-indigo-600 text-white shadow-md' : 'bg-slate-50 border-slate-100 text-slate-500 hover:border-slate-300 hover:text-slate-700' }}">
                Customers
            </a>
        </div>
    </div>

    <!-- Desktop Grid/Table Layout -->
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100">
                        <th class="p-6 text-[10px] font-black uppercase text-slate-400 tracking-wider">User Account / Details</th>
                        <th class="p-6 text-[10px] font-black uppercase text-slate-400 tracking-wider">Assigned Access Role</th>
                        <th class="p-6 text-[10px] font-black uppercase text-slate-400 tracking-wider">Registered Since</th>
                        <th class="p-6 text-[10px] font-black uppercase text-slate-400 tracking-wider text-right">Operational Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($users as $userItem)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <!-- Avatar and Info -->
                            <td class="p-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-11 h-11 rounded-2xl overflow-hidden flex items-center justify-center font-black text-sm border shadow-sm shrink-0
                                        @if($userItem->role === 'admin') bg-amber-50 border-amber-100 text-amber-600
                                        @elseif($userItem->role === 'staff') bg-emerald-50 border-emerald-100 text-emerald-600
                                        @else bg-indigo-50 border-indigo-100 text-indigo-600 @endif">
                                        @if($userItem->profile_image)
                                            <img src="{{ $userItem->profile_image }}" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr($userItem->name, 0, 2)) }}
                                        @endif
                                    </div>
                                    <div class="text-left">
                                        <p class="text-xs font-black text-slate-900 flex items-center gap-2">
                                            {{ $userItem->name }}
                                            @if(auth()->id() === $userItem->id)
                                                <span class="bg-slate-100 text-slate-500 font-extrabold px-2 py-0.5 rounded text-[8px]">YOU</span>
                                            @endif
                                        </p>
                                        <p class="text-[10px] text-slate-400 font-bold mt-0.5">{{ $userItem->email }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Access Badge -->
                            <td class="p-6">
                                @if($userItem->role === 'admin')
                                    <span class="inline-flex items-center gap-1.5 bg-amber-50 border border-amber-100/50 px-2.5 py-1 rounded-full text-[9px] font-black uppercase text-amber-700 tracking-wider">
                                        <i class="fas fa-shield-alt text-[8px]"></i> System Admin
                                    </span>
                                @elseif($userItem->role === 'staff')
                                    <span class="inline-flex items-center gap-1.5 bg-emerald-50 border border-emerald-100/50 px-2.5 py-1 rounded-full text-[9px] font-black uppercase text-emerald-700 tracking-wider">
                                        <i class="fas fa-utensils text-[8px]"></i> Official Staff
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-indigo-50 border border-indigo-100/50 px-2.5 py-1 rounded-full text-[9px] font-black uppercase text-indigo-700 tracking-wider">
                                        <i class="fas fa-shopping-bag text-[8px]"></i> Customer
                                    </span>
                                @endif
                            </td>

                            <!-- Registration date -->
                            <td class="p-6 text-xs text-slate-500 font-bold">
                                {{ $userItem->created_at->setTimezone('Asia/Manila')->format('M d, Y \a\t g:i A') }}
                                <p class="text-[9px] text-slate-400 font-medium mt-0.5">{{ $userItem->created_at->diffForHumans() }}</p>
                            </td>

                            <!-- Actions -->
                            <td class="p-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        @click="openEditModal({{ json_encode([
                                            'id' => $userItem->id,
                                            'name' => $userItem->name,
                                            'email' => $userItem->email,
                                            'role' => $userItem->role,
                                            'profile_image' => $userItem->profile_image,
                                            'update_url' => route('admin.users.update', $userItem->id)
                                        ]) }})"
                                        class="bg-slate-50 hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 border border-slate-100 hover:border-emerald-100 p-2.5 rounded-xl transition-all cursor-pointer active:scale-90"
                                        title="Modify Settings"
                                    >
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>

                                    @if(auth()->id() !== $userItem->id)
                                        <form action="{{ route('admin.users.destroy', $userItem->id) }}" method="POST" class="inline m-0"
                                              onsubmit="return confirm('WARNING: Are you sure you want to permanently delete this user account? All corresponding dependencies may be impacted.')">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="bg-slate-50 hover:bg-red-50 text-slate-400 hover:text-red-600 border border-slate-100 hover:border-red-100 p-2.5 rounded-xl transition-all cursor-pointer active:scale-90"
                                                title="Revoke and Purge"
                                            >
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="w-8 h-8 flex items-center justify-center text-slate-300" title="Cannot delete your logged-in session">
                                            <i class="fas fa-ban text-xs"></i>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-16 text-center space-y-4">
                                <div class="w-16 h-16 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center mx-auto text-slate-400">
                                    <i class="fas fa-users-slash text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-extrabold text-slate-900">No matching accounts found</p>
                                    <p class="text-xs font-bold text-slate-400">Try modifying your role filter toggles or searching for a different name!</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-6 border-t border-slate-50 bg-slate-50/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Add Staff Modal -->
    <div
        x-show="showAddStaffModal"
        class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
    >
        <!-- Backdrop alignment -->
        <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-md" @click="closeAddStaffModal()"></div>

        <div class="flex min-h-screen items-center justify-center p-4">
            <div
                class="bg-white rounded-[2.5rem] shadow-2xl border border-slate-100 max-w-4xl w-full p-8 md:p-10 relative text-left"
                x-show="showAddStaffModal"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            >
                <!-- Closes handler -->
                <button @click="closeAddStaffModal()" class="absolute top-6 right-6 bg-slate-50 hover:bg-slate-100 text-slate-500 w-9 h-9 rounded-full flex items-center justify-center border border-slate-100 active:scale-95 cursor-pointer z-10">
                    <i class="fas fa-times text-xs"></i>
                </button>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    <!-- Left Column: Form Fields (7 cols) -->
                    <div class="lg:col-span-7 space-y-6">
                        <!-- Title Header -->
                        <div class="space-y-1">
                            <h3 class="text-xl font-black text-slate-900 flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                                    <i class="fas fa-user-tie"></i>
                                </span>
                                Add New Staff Only
                            </h3>
                            <p class="text-[11px] font-bold text-slate-400">Deploy a dedicated kitchen helper or system operator</p>
                        </div>

                        <form action="{{ route('admin.users.store_staff') }}" method="POST" enctype="multipart/form-data" class="space-y-4 m-0">
                            @csrf

                            <!-- Enforced Role Statement -->
                            <div class="bg-slate-50 rounded-2xl border border-slate-100 p-4">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block leading-none">Security Clearance</label>
                                <p class="text-xs font-black text-emerald-600 uppercase flex items-center gap-1.5 mt-1.5">
                                    <i class="fas fa-shield-alt text-[10px]"></i> role: staff (enforced)
                                </p>
                            </div>

                            <!-- Full Name input -->
                            <div class="space-y-1">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-2">Full Legal Name</label>
                                <input type="text" name="name" x-model="newStaff.name" required placeholder="e.g. Juan De La Cruz"
                                    class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200/60 font-bold focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition-all text-xs">
                            </div>

                            <!-- Email Address input -->
                            <div class="space-y-1">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-2">Email Address</label>
                                <input type="email" name="email" x-model="newStaff.email" required placeholder="e.g. juan@clarasbeast.com"
                                    class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200/60 font-bold focus:ring-4 focus:ring-emerald-550 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition-all text-xs">
                            </div>

                            <!-- Profile Image Source Type Selector -->
                            <div class="space-y-3 pt-2 border-t border-slate-100">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block">Avatar Strategy</label>
                                        <p class="text-[8px] text-slate-400 font-bold">Upload an image file or supply a live image link URL</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-xl shrink-0">
                                        <button type="button" @click="newStaff.avatarType = 'url'" :class="newStaff.avatarType === 'url' ? 'bg-white text-emerald-600 shadow-sm font-black' : 'text-slate-500 font-bold'" class="px-3 py-1.5 rounded-lg text-[9px] transition-all cursor-pointer">
                                            Link URL
                                        </button>
                                        <button type="button" @click="newStaff.avatarType = 'file'" :class="newStaff.avatarType === 'file' ? 'bg-white text-emerald-600 shadow-sm font-black' : 'text-slate-500 font-bold'" class="px-3 py-1.5 rounded-lg text-[9px] transition-all cursor-pointer">
                                            Upload Photo
                                        </button>
                                    </div>
                                </div>

                                <!-- Image URL Field -->
                                <div x-show="newStaff.avatarType === 'url'" class="space-y-1 animate-in fade-in duration-200">
                                    <label class="text-[9px] font-black text-slate-400 uppercase ml-2">Image URL</label>
                                    <input type="url" name="profile_image_url" x-model="newStaff.avatarUrl" placeholder="https://"
                                        class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200/60 font-bold focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition-all text-xs">
                                </div>

                                <!-- Image File Upload Field -->
                                <div x-show="newStaff.avatarType === 'file'" class="space-y-1 animate-in fade-in duration-200" x-cloak>
                                    <label class="text-[9px] font-black text-slate-400 uppercase ml-2">Upload Profile File</label>
                                    <div class="relative group">
                                        <input type="file" name="profile_image_file" @change="updateNewStaffAvatarPreview($event)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                        <div class="border-2 border-dashed border-slate-200 rounded-2xl p-4 text-center group-hover:border-emerald-500 transition-colors bg-slate-50">
                                            <template x-if="newStaff.avatarFilePreview">
                                                <div class="flex items-center justify-center gap-2">
                                                    <i class="fas fa-check-circle text-emerald-500 text-sm"></i>
                                                    <span class="text-[10px] font-bold text-slate-700">Photo Loaded!</span>
                                                </div>
                                            </template>
                                            <template x-if="!newStaff.avatarFilePreview">
                                                <div>
                                                    <i class="fas fa-cloud-upload-alt text-slate-400 text-lg mb-1 group-hover:text-emerald-500"></i>
                                                    <p class="text-[10px] font-bold text-slate-500">Choose custom picture asset</p>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Password fields -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-2">Set Password</label>
                                    <input type="password" name="password" required placeholder="Min 6 chars"
                                        class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200/60 font-bold focus:ring-4 focus:ring-emerald-550 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition-all text-xs">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-2">Confirm Password</label>
                                    <input type="password" name="password_confirmation" required placeholder="Repeat Password"
                                        class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200/60 font-bold focus:ring-4 focus:ring-emerald-550 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition-all text-xs">
                                </div>
                            </div>

                            <!-- Submit action -->
                            <div class="pt-2 flex justify-end">
                                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 rounded-2xl text-xs uppercase tracking-wider transition-all shadow-md active:scale-95 cursor-pointer">
                                    Deploy Staff Member
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Right Column: Visual Preview Card (5 cols) -->
                    <div class="lg:col-span-5 h-full flex flex-col justify-center pt-8 lg:pt-12">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest text-center mb-4">Preview Live Staff card</p>

                        <div class="bg-white border border-slate-100 rounded-[2.5rem] p-6 shadow-xl relative overflow-hidden flex flex-col items-center text-center space-y-4">
                            <!-- Outer colored cover segment -->
                            <div class="w-full h-24 rounded-[1.5rem] bg-gradient-to-tr from-emerald-600 to-teal-500 absolute top-0 left-0 right-0 z-0"></div>

                            <!-- Avatar Circle -->
                            <div class="relative z-10 mt-10">
                                <div class="h-20 w-20 rounded-full border-4 border-white shadow-md overflow-hidden flex items-center justify-center bg-zinc-100">
                                    <template x-if="computedNewStaffAvatar">
                                        <img :src="computedNewStaffAvatar" class="h-full w-full object-cover">
                                    </template>
                                    <template x-if="!computedNewStaffAvatar">
                                        <div class="bg-emerald-50 h-full w-full flex items-center justify-center font-black text-xl text-emerald-600">
                                            <span x-text="newStaff.name ? newStaff.name.substring(0, 2).toUpperCase() : 'ST'"></span>
                                        </div>
                                    </template>
                                </div>
                                <span class="absolute bottom-0 right-1.5 w-4 h-4 rounded-full border-2 border-white bg-emerald-500" title="Active Clearance Ready"></span>
                            </div>

                            <!-- Bio / details review of the card -->
                            <div class="space-y-1.5 z-10 w-full">
                                <h3 class="text-base font-black text-slate-900" x-text="newStaff.name ? newStaff.name : 'Staff Candidate Name'"></h3>
                                <p class="text-[10px] text-slate-400 font-bold truncate" x-text="newStaff.email ? newStaff.email : 'candidate@clarasbeast.com'"></p>

                                <div class="pt-2 flex justify-center">
                                    <span class="inline-flex items-center gap-1.5 bg-emerald-50 border border-emerald-100/50 px-3 py-1 rounded-full text-[9px] font-black uppercase text-emerald-700 tracking-wider">
                                        <i class="fas fa-id-badge"></i> Role: Staff
                                    </span>
                                </div>
                            </div>

                            <!-- Statistics Grid / Meta markers -->
                            <div class="grid grid-cols-2 gap-2 w-full pt-4 border-t border-slate-50 text-left">
                                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-wider">Status</p>
                                    <p class="text-[9px] font-black text-emerald-600 uppercase mt-0.5">READY FOR DUTY</p>
                                </div>
                                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-wider">Authority</p>
                                    <p class="text-[9px] font-black text-slate-600 uppercase mt-0.5">KITCHEN OPS</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div
        x-show="showEditModal"
        class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
    >
        <!-- Backdrop alignment -->
        <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-md" @click="closeEditModal()"></div>

        <div class="flex min-h-screen items-center justify-center p-4">
            <div
                class="bg-white rounded-[2.5rem] shadow-2xl border border-slate-100 max-w-4xl w-full p-8 md:p-10 relative text-left"
                x-show="showEditModal"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            >
                <!-- Closes handler -->
                <button @click="closeEditModal()" class="absolute top-6 right-6 bg-slate-50 hover:bg-slate-100 text-slate-500 w-9 h-9 rounded-full flex items-center justify-center border border-slate-100 active:scale-95 cursor-pointer z-10">
                    <i class="fas fa-times text-xs"></i>
                </button>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    <!-- Left Column: Form Fields (7 cols) -->
                    <div class="lg:col-span-7 space-y-6">
                        <!-- Title Header -->
                        <div class="space-y-1">
                            <h3 class="text-xl font-black text-slate-900 flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-xs">
                                    <i class="fas fa-edit"></i>
                                </span>
                                Update User Account
                            </h3>
                            <p class="text-[11px] font-bold text-slate-400">Modify authentication rules and access level structures</p>
                        </div>

                        <form :action="editUser.update_url" method="POST" enctype="multipart/form-data" class="space-y-4 m-0">
                            @csrf

                            <!-- Role toggle custom selection -->
                            <div class="space-y-1">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-2">Access Permissions</label>
                                <select name="role" x-model="editUser.role" required
                                    class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200/60 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all text-xs appearance-none">
                                    <option value="admin">System Admin</option>
                                    <option value="staff">Official Staff</option>
                                    <option value="customer">Customer</option>
                                </select>
                            </div>

                            <!-- Full Name input -->
                            <div class="space-y-1">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-2">Legal Name</label>
                                <input type="text" name="name" x-model="editUser.name" required
                                    class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200/60 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all text-xs">
                            </div>

                            <!-- Email Address input -->
                            <div class="space-y-1">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-2">Email Address</label>
                                <input type="email" name="email" x-model="editUser.email" required
                                    class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200/60 font-bold focus:ring-4 focus:ring-orange-550 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all text-xs">
                            </div>

                            <!-- Profile Avatar Strategy / Selection -->
                            <div class="space-y-3 pt-2 border-t border-slate-100">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block font-black">Avatar Strategy</label>
                                        <p class="text-[8px] text-slate-400 font-bold">Upload an image file or supply a live image link URL</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-xl shrink-0">
                                        <button type="button" @click="editUser.avatarType = 'url'" :class="editUser.avatarType === 'url' ? 'bg-white text-orange-600 shadow-sm font-black' : 'text-slate-500 font-bold'" class="px-3 py-1.5 rounded-lg text-[9px] transition-all cursor-pointer">
                                            Link URL
                                        </button>
                                        <button type="button" @click="editUser.avatarType = 'file'" :class="editUser.avatarType === 'file' ? 'bg-white text-orange-600 shadow-sm font-black' : 'text-slate-500 font-bold'" class="px-3 py-1.5 rounded-lg text-[9px] transition-all cursor-pointer">
                                            Upload Photo
                                        </button>
                                    </div>
                                </div>

                                <!-- Image URL Field -->
                                <div x-show="editUser.avatarType === 'url'" class="space-y-1 animate-in fade-in duration-200">
                                    <label class="text-[9px] font-black text-slate-400 uppercase ml-2">Image URL</label>
                                    <input type="url" name="profile_image_url" x-model="editUser.profile_image" placeholder="https://"
                                        class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200/60 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all text-xs">
                                </div>

                                <!-- Image File Upload Field -->
                                <div x-show="editUser.avatarType === 'file'" class="space-y-1 animate-in fade-in duration-200" x-cloak>
                                    <label class="text-[9px] font-black text-slate-400 uppercase ml-2">Upload Profile File</label>
                                    <div class="relative group">
                                        <input type="file" name="profile_image_file" @change="updateEditUserAvatarPreview($event)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                        <div class="border-2 border-dashed border-slate-200 rounded-2xl p-4 text-center group-hover:border-orange-500 transition-colors bg-slate-50">
                                            <template x-if="editUser.avatarFilePreview">
                                                <div class="flex items-center justify-center gap-2">
                                                    <i class="fas fa-check-circle text-emerald-500 text-sm"></i>
                                                    <span class="text-[10px] font-bold text-slate-700">Photo Loaded!</span>
                                                </div>
                                            </template>
                                            <template x-if="!editUser.avatarFilePreview">
                                                <div>
                                                    <i class="fas fa-cloud-upload-alt text-slate-400 text-lg mb-1 group-hover:text-orange-500"></i>
                                                    <p class="text-[10px] font-bold text-slate-500">Choose custom picture asset</p>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Optional Password changes -->
                            <div class="pt-3 border-t border-slate-100 space-y-3">
                                <span class="text-[8px] font-black text-slate-400 uppercase block tracking-wider"><i class="fas fa-lock mr-1"></i> Optional Password Refresh</span>

                                <div class="grid grid-cols-2 gap-3">
                                    <div class="space-y-1">
                                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-2">New Password</label>
                                        <input type="password" name="password" placeholder="Leave blank to keep"
                                            class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200/60 font-bold focus:ring-4 focus:ring-orange-550 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all text-xs search-no">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-2">Confirm Password</label>
                                        <input type="password" name="password_confirmation" placeholder="Leave blank to keep"
                                            class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200/60 font-bold focus:ring-4 focus:ring-orange-550 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all text-xs search-no">
                                    </div>
                                </div>
                            </div>

                            <!-- Submit action -->
                            <div class="pt-4 flex justify-end">
                                <button type="submit" class="w-full bg-slate-900 hover:bg-orange-600 text-white font-black py-4 rounded-2xl text-xs uppercase tracking-wider transition-all shadow-md active:scale-95 cursor-pointer">
                                    Apply Account Updates
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Right Column: Visual Preview Card (5 cols) -->
                    <div class="lg:col-span-5 h-full flex flex-col justify-center pt-8 lg:pt-12">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest text-center mb-4">Preview Live User card</p>

                        <div class="bg-white border border-slate-100 rounded-[2.5rem] p-6 shadow-xl relative overflow-hidden flex flex-col items-center text-center space-y-4">
                            <!-- Outer colored cover segment based on role -->
                            <div class="w-full h-24 rounded-[1.5rem] absolute top-0 left-0 right-0 z-0 transition-all duration-300"
                                :class="{
                                    'bg-gradient-to-tr from-orange-600 to-red-500': editUser.role === 'admin',
                                    'bg-gradient-to-tr from-emerald-600 to-teal-500': editUser.role === 'staff',
                                    'bg-gradient-to-tr from-indigo-600 to-violet-500': editUser.role === 'customer'
                                }"></div>

                            <!-- Avatar Circle -->
                            <div class="relative z-10 mt-10">
                                <div class="h-20 w-20 rounded-full border-4 border-white shadow-md overflow-hidden flex items-center justify-center bg-zinc-100">
                                    <template x-if="computedEditUserAvatar">
                                        <img :src="computedEditUserAvatar" class="h-full w-full object-cover">
                                    </template>
                                    <template x-if="!computedEditUserAvatar">
                                        <div class="h-full w-full flex items-center justify-center font-black text-xl"
                                            :class="{
                                                'bg-orange-50 text-orange-600': editUser.role === 'admin',
                                                'bg-emerald-50 text-emerald-600': editUser.role === 'staff',
                                                'bg-indigo-50 text-indigo-600': editUser.role === 'customer'
                                            }">
                                            <span x-text="editUser.name ? editUser.name.substring(0, 2).toUpperCase() : 'US'"></span>
                                        </div>
                                    </template>
                                </div>
                                <span class="absolute bottom-0 right-1.5 w-4 h-4 rounded-full border-2 border-white"
                                    :class="{
                                        'bg-orange-500': editUser.role === 'admin',
                                        'bg-emerald-500': editUser.role === 'staff',
                                        'bg-indigo-500': editUser.role === 'customer'
                                    }"></span>
                            </div>

                            <!-- Bio / details review of the card -->
                            <div class="space-y-1.5 z-10 w-full">
                                <h3 class="text-base font-black text-slate-900" x-text="editUser.name ? editUser.name : 'User Name'"></h3>
                                <p class="text-[10px] text-slate-400 font-bold truncate" x-text="editUser.email ? editUser.email : 'user@clarasbeast.com'"></p>

                                <div class="pt-2 flex justify-center">
                                    <span class="inline-flex items-center gap-1.5 border px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider"
                                        :class="{
                                            'bg-orange-550/10 border-orange-200/60 text-orange-700 bg-orange-50': editUser.role === 'admin',
                                            'bg-emerald-550/10 border-emerald-200/60 text-emerald-700 bg-emerald-50': editUser.role === 'staff',
                                            'bg-indigo-550/10 border-indigo-200/60 text-indigo-700 bg-indigo-50': editUser.role === 'customer'
                                        }">
                                        <i class="fas fa-id-badge"></i> Role: <span x-text="editUser.role"></span>
                                    </span>
                                </div>
                            </div>

                            <!-- Statistics Grid / Meta markers -->
                            <div class="grid grid-cols-2 gap-2 w-full pt-4 border-t border-slate-50 text-left">
                                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-wider">Status</p>
                                    <p class="text-[9px] font-black uppercase mt-0.5"
                                        :class="{
                                            'text-orange-600': editUser.role === 'admin',
                                            'text-emerald-600': editUser.role === 'staff',
                                            'text-indigo-600': editUser.role === 'customer'
                                        }">ACTIVE</p>
                                </div>
                                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-wider">Authority</p>
                                    <p class="text-[9px] font-black text-slate-600 uppercase mt-0.5"
                                        x-text="editUser.role === 'admin' ? 'FULL ADMIN' : (editUser.role === 'staff' ? 'KITCHEN OPS' : 'REGULAR')"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('adminUsersHandler', () => ({
        showAddStaffModal: false,
        showEditModal: false,
        newStaff: {
            name: '',
            email: '',
            avatarType: 'url',
            avatarUrl: '',
            avatarFilePreview: null
        },
        editUser: {
            id: null,
            name: '',
            email: '',
            role: 'staff',
            profile_image: '',
            avatarType: 'url',
            avatarFilePreview: null,
            update_url: ''
        },

        get computedNewStaffAvatar() {
            if (this.newStaff.avatarType === 'file' && this.newStaff.avatarFilePreview) {
                return this.newStaff.avatarFilePreview;
            }
            if (this.newStaff.avatarType === 'url' && this.newStaff.avatarUrl && this.newStaff.avatarUrl.trim() !== '') {
                return this.newStaff.avatarUrl;
            }
            return null;
        },

        updateNewStaffAvatarPreview(event) {
            const file = event.target.files[0];
            if (file) {
                this.newStaff.avatarFilePreview = URL.createObjectURL(file);
            }
        },

        get computedEditUserAvatar() {
            if (this.editUser.avatarType === 'file' && this.editUser.avatarFilePreview) {
                return this.editUser.avatarFilePreview;
            }
            if (this.editUser.avatarType === 'url' && this.editUser.profile_image && this.editUser.profile_image.trim() !== '') {
                return this.editUser.profile_image;
            }
            return null;
        },

        updateEditUserAvatarPreview(event) {
            const file = event.target.files[0];
            if (file) {
                this.editUser.avatarFilePreview = URL.createObjectURL(file);
            }
        },

        openAddStaffModal() {
            this.newStaff = {
                name: '',
                email: '',
                avatarType: 'url',
                avatarUrl: '',
                avatarFilePreview: null
            };
            this.showAddStaffModal = true;
        },

        closeAddStaffModal() {
            this.showAddStaffModal = false;
        },

        openEditModal(userData) {
            this.editUser = {
                ...userData,
                avatarType: 'url',
                avatarFilePreview: null
            };
            this.showEditModal = true;
        },

        closeEditModal() {
            this.showEditModal = false;
        }
    }));
});
</script>
@endsection
