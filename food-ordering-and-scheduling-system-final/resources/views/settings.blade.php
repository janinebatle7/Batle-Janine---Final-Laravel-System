@extends('layouts.app')

@section('title', 'My Settings')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12 animate-in fade-in duration-300">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-6 border-b border-slate-200 bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
        <div class="text-left">
            <div class="inline-flex items-center gap-2 bg-orange-50 border border-orange-100/60 px-4 py-2 rounded-full text-[10px] font-black uppercase text-orange-600 tracking-wider shadow-sm mb-3">
                <i class="fas fa-user-cog"></i> Account Portal
            </div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">Personal Profile Settings</h1>
            <p class="text-slate-400 font-bold mt-1 text-xs">Configure your personal metadata, secure passwords, or upload premium avatars</p>
        </div>
    </div>

    <!-- System Message / Errors -->
    @if($errors->any())
        <div class="p-6 bg-red-550 border border-red-100 text-red-600 font-bold rounded-3xl text-sm leading-relaxed text-left">
            <p class="font-black uppercase tracking-wider text-xs mb-1">
                <i class="fas fa-exclamation-triangle mr-1"></i> Form Validation Failures
            </p>
            <ul class="list-disc pl-5 space-y-1 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12" x-data="{
        name: '{{ old('name', $user->name) }}',
        email: '{{ old('email', $user->email) }}',
        avatarType: '{{ $user->profile_image && str_starts_with($user->profile_image, '/uploads/profile/') ? 'file' : ($user->profile_image ? 'url' : 'url') }}',
        avatarUrl: '{{ $user->profile_image && !str_starts_with($user->profile_image, '/uploads/profile/') ? $user->profile_image : '' }}',
        avatarFilePreview: '{{ $user->profile_image && str_starts_with($user->profile_image, '/uploads/profile/') ? $user->profile_image : null }}',

        get computedAvatar() {
            if (this.avatarType === 'file' && this.avatarFilePreview) {
                return this.avatarFilePreview;
            }
            if (this.avatarType === 'url' && this.avatarUrl && this.avatarUrl.trim() !== '') {
                return this.avatarUrl;
            }
            return null;
        },

        updateAvatarPreview(event) {
            const file = event.target.files[0];
            if (file) {
                this.avatarFilePreview = URL.createObjectURL(file);
            }
        }
    }">
        <!-- Left Column: Form Controls -->
        <div class="lg:col-span-7 space-y-10">
            <!-- Profile Info Form -->
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-8 md:p-10 text-left space-y-6">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 flex items-center gap-3">
                        <span class="bg-orange-50 text-orange-600 w-10 h-10 rounded-xl flex items-center justify-center text-sm">
                            <i class="fas fa-user-circle"></i>
                        </span>
                        Profile Information
                    </h2>
                    <p class="text-xs text-slate-400 font-bold mt-1">Refine your public display identity and verified email address</p>
                </div>

                <form action="{{ route('settings.profile') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Display Name</label>
                            <input type="text" name="name" x-model="name" required
                                class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all text-xs">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Email Address</label>
                            <input type="email" name="email" x-model="email" required
                                class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all text-xs">
                        </div>
                    </div>

                    <!-- Profile Image Strategy -->
                    <div class="space-y-4 pt-4 border-t border-slate-50">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Avatar Image Source</label>
                                <p class="text-[9px] text-slate-400 font-bold">Pick whether to load from a high-quality link or upload a file</p>
                            </div>
                            <div class="grid grid-cols-2 gap-2 p-1.5 bg-slate-100 rounded-2xl shrink-0">
                                <button type="button" @click="avatarType = 'url'" :class="avatarType === 'url' ? 'bg-white text-orange-600 shadow' : 'text-slate-500'" class="px-4 py-2 rounded-xl text-[10px] font-black transition-all cursor-pointer">
                                    Direct URL
                                </button>
                                <button type="button" @click="avatarType = 'file'" :class="avatarType === 'file' ? 'bg-white text-orange-600 shadow' : 'text-slate-500'" class="px-4 py-2 rounded-xl text-[10px] font-black transition-all cursor-pointer">
                                    Upload Photo
                                </button>
                            </div>
                        </div>

                        <!-- URL configuration option -->
                        <div x-show="avatarType === 'url'" class="space-y-1 animate-in fade-in duration-200">
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2">Web Image URL</label>
                            <input type="url" name="profile_image_url" x-model="avatarUrl"
                                class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all text-xs placeholder-slate-300"
                                placeholder="https://images.unsplash.com/photo-...">
                        </div>

                        <!-- Image file selector option -->
                        <div x-show="avatarType === 'file'" class="space-y-1 animate-in fade-in duration-200" x-cloak>
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2">Upload Profile Document</label>
                            <div class="relative group">
                                <input type="file" name="profile_image_file" @change="updateAvatarPreview($event)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <div class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center group-hover:border-orange-400 transition-colors bg-slate-50">
                                    <template x-if="avatarFilePreview">
                                        <div class="flex items-center justify-center gap-2">
                                            <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                                            <span class="text-xs font-black text-slate-700">Photo Loaded!</span>
                                        </div>
                                    </template>
                                    <template x-if="!avatarFilePreview">
                                        <div>
                                            <i class="fas fa-cloud-upload-alt text-slate-400 text-xl mb-1 group-hover:text-orange-500"></i>
                                            <p class="text-[11px] font-bold text-slate-500">Drag/Click to upload image file (JPG, PNG, WebP)</p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit" class="bg-slate-900 hover:bg-orange-600 text-white font-black px-6 py-3.5 rounded-2xl text-xs transition-colors shadow-md active:scale-95 cursor-pointer uppercase tracking-wider">
                            Apply Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Password Adjustments form -->
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-8 md:p-10 text-left space-y-6">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 flex items-center gap-3">
                        <span class="bg-orange-50 text-orange-600 w-10 h-10 rounded-xl flex items-center justify-center text-sm">
                            <i class="fas fa-key"></i>
                        </span>
                        Change Security Key
                    </h2>
                    <p class="text-xs text-slate-400 font-bold mt-1">Change your passcode to defend against unauthorized logins</p>
                </div>

                <form action="{{ route('settings.password') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Current Password</label>
                            <input type="password" name="current_password" required
                                class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all text-xs">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">New Password</label>
                                <input type="password" name="password" required
                                    class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all text-xs">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Confirm Password</label>
                                <input type="password" name="password_confirmation" required
                                    class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all text-xs">
                            </div>
                        </div>
                    </div>
                    <div class="pt-2 flex justify-end">
                        <button type="submit" class="bg-slate-900 hover:bg-orange-600 text-white font-black px-6 py-3.5 rounded-2xl text-xs transition-colors shadow-md active:scale-95 cursor-pointer uppercase tracking-wider">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column: Premium Visual Profile Preview Card -->
        <div class="lg:col-span-5 space-y-8">
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest text-center md:text-left ml-4">Profile Card Preview</p>
            <div class="bg-white border border-slate-100 rounded-[3rem] p-8 shadow-xl relative overflow-hidden flex flex-col items-center text-center space-y-6">
                <!-- Outer colored cover segment -->
                <div class="w-full h-32 rounded-[2rem] bg-gradient-to-tr from-orange-600 to-red-500 absolute top-0 left-0 right-0 z-0"></div>

                <!-- Avatar Circle -->
                <div class="relative z-10 mt-14">
                    <div class="h-28 w-28 rounded-full border-4 border-white shadow-lg overflow-hidden flex items-center justify-center bg-slate-100">
                        <template x-if="computedAvatar">
                            <img :src="computedAvatar" class="h-full w-full object-cover">
                        </template>
                        <template x-if="!computedAvatar">
                            <div class="bg-slate-200 h-full w-full flex items-center justify-center font-black text-3xl text-slate-500">
                                <span x-text="name ? name.substring(0, 2).toUpperCase() : 'CB'"></span>
                            </div>
                        </template>
                    </div>
                    <span class="absolute bottom-1 right-2 w-5 h-5 rounded-full border-2 border-white bg-emerald-500" title="Online profile status"></span>
                </div>

                <!-- Bio / details review of the card -->
                <div class="space-y-2 z-10 w-full">
                    <h3 class="text-xl font-black text-slate-900" x-text="name ? name : 'Full Name'"></h3>
                    <p class="text-xs text-slate-400 font-bold" x-text="email ? email : 'your.email@clarasbest.com'"></p>

                    <div class="pt-4 flex justify-center">
                        <span class="inline-flex items-center gap-1.5 bg-orange-50 border border-orange-100/50 px-4 py-1.5 rounded-full text-[10px] font-black uppercase text-orange-700 tracking-wider">
                            <i class="fas fa-id-badge"></i> Role: {{ auth()->user()->role }}
                        </span>
                    </div>
                </div>

                <!-- Statistics Grid -->
                <div class="grid grid-cols-2 gap-4 w-full pt-4 border-t border-slate-50">
                    <div class="text-left bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Registered</p>
                        <p class="text-xs font-bold text-slate-800 mt-1">{{ auth()->user()->created_at->setTimezone('Asia/Manila')->format('M d, Y') }}</p>
                    </div>
                    <div class="text-left bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Membership</p>
                        <p class="text-xs font-black text-orange-600 uppercase mt-1">Official Member</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
