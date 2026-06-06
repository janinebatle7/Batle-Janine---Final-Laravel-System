@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{
    name: '{{ old('name') }}',
    email: '{{ old('email') }}',
    avatarType: 'file',
    avatarUrl: '',
    avatarFilePreview: null,
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
    <div class="max-w-5xl mx-auto">
        <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200 border border-slate-100 overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-12 divide-y md:divide-y-0 md:divide-x divide-slate-100">

                <!-- Left Column: Form Controls -->
                <div class="p-8 md:p-12 md:col-span-7 space-y-8">
                    <div class="text-center md:text-left space-y-2">
                        <h2 class="text-3xl font-black text-slate-900">Join the Pride</h2>
                        <p class="text-slate-500 font-bold text-sm">Start your premium food journey with Clara’s Best</p>
                    </div>

                    @if($errors->any())
                        <div class="p-4 bg-red-50 border border-red-100 text-red-600 text-xs font-bold rounded-2xl">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register.post') }}" enctype="multipart/form-data" class="space-y-5">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Full Name</label>
                                <input type="text" name="name" x-model="name" required class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all text-xs" placeholder="John Doe">
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Email Address</label>
                                <input type="email" name="email" x-model="email" required class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all text-xs" placeholder="your@email.com">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Password</label>
                                <input type="password" name="password" required class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all text-xs" placeholder="••••••••">
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Confirm Password</label>
                                <input type="password" name="password_confirmation" required class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all text-xs" placeholder="••••••••">
                            </div>
                        </div>

                        <!-- Profile Image Section -->
                        <div class="space-y-4 pt-4 border-t border-slate-50">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Profile Picture Source</label>
                                    <p class="text-[9px] text-slate-400 font-bold">Upload an avatar photocard or supply an external link</p>
                                </div>
                                <div class="grid grid-cols-2 gap-2 p-1.5 bg-slate-100 rounded-2xl shrink-0">
                                    <button type="button" @click="avatarType = 'file'" :class="avatarType === 'file' ? 'bg-white text-orange-600 shadow' : 'text-slate-500'" class="px-4 py-2 rounded-xl text-[10px] font-black transition-all cursor-pointer">
                                        Upload
                                    </button>
                                    <button type="button" @click="avatarType = 'url'" :class="avatarType === 'url' ? 'bg-white text-orange-600 shadow' : 'text-slate-500'" class="px-4 py-2 rounded-xl text-[10px] font-black transition-all cursor-pointer">
                                        Web URL
                                    </button>
                                </div>
                            </div>

                            <!-- File Uploader -->
                            <div x-show="avatarType === 'file'" class="space-y-1 animate-in fade-in duration-200">
                                <label class="text-[10px] font-black text-slate-400 uppercase ml-2">Local Image Document</label>
                                <div class="relative group">
                                    <input type="file" name="profile_image_file" @change="updateAvatarPreview($event)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <div class="border-2 border-dashed border-slate-200 rounded-2xl p-5 text-center group-hover:border-orange-400 transition-colors bg-slate-50">
                                        <template x-if="avatarFilePreview">
                                            <div class="flex items-center justify-center gap-2">
                                                <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                                                <span class="text-xs font-black text-slate-700">Avatar Image Selected!</span>
                                            </div>
                                        </template>
                                        <template x-if="!avatarFilePreview">
                                            <div>
                                                <i class="fas fa-cloud-upload-alt text-slate-400 text-lg mb-1 group-hover:text-orange-500"></i>
                                                <p class="text-[10px] font-bold text-slate-500">Touch/Drag to load personal photo (JPEG, PNG, WebP)</p>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- URL Input -->
                            <div x-show="avatarType === 'url'" class="space-y-1 animate-in fade-in duration-200" x-cloak>
                                <label class="text-[10px] font-black text-slate-400 uppercase ml-2">External Avatar Link</label>
                                <input type="url" name="profile_image_url" x-model="avatarUrl" class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all text-xs" placeholder="https://images.unsplash.com/photo-...">
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-slate-900 hover:bg-orange-600 text-white font-black py-4.5 rounded-2xl text-xs uppercase tracking-widest shadow-xl shadow-slate-100 transition-all active:scale-95 mt-4">
                            Complete Registration
                        </button>
                    </form>

                    <div class="pt-6 border-t border-slate-100 text-center md:text-left">
                        <p class="text-xs font-bold text-slate-500">
                            Already have an account?
                            <a href="{{ route('login') }}" class="text-orange-600 font-black hover:underline">Sign In Now</a>
                        </p>
                    </div>
                </div>

                <!-- Right Column: Card Preview Panel -->
                <div class="p-8 md:p-12 md:col-span-5 bg-slate-50/50 flex flex-col justify-center items-center text-center space-y-6">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Active Member Preview</p>

                    <div class="bg-white border border-slate-100 rounded-[2.5rem] p-6 shadow-xl relative overflow-hidden flex flex-col items-center text-center space-y-6 w-full max-w-[280px]">
                        <!-- Decorative top gradient bar -->
                        <div class="w-full h-24 rounded-[1.8rem] bg-gradient-to-tr from-orange-600 to-red-500 absolute top-0 left-0 right-0 z-0"></div>

                        <!-- Avatar Circle inside preview -->
                        <div class="relative z-10 mt-10">
                            <div class="h-24 w-24 rounded-full border-4 border-white shadow-lg overflow-hidden flex items-center justify-center bg-slate-100">
                                <template x-if="computedAvatar">
                                    <img :src="computedAvatar" class="h-full w-full object-cover">
                                </template>
                                <template x-if="!computedAvatar">
                                    <div class="bg-slate-200 h-full w-full flex items-center justify-center font-black text-2xl text-slate-500">
                                        <span x-text="name ? name.substring(0, 2).toUpperCase() : 'CB'"></span>
                                    </div>
                                </template>
                            </div>
                            <span class="absolute bottom-1 right-1 w-4.5 h-4.5 rounded-full border-2 border-white bg-emerald-500" title="Profile connected"></span>
                        </div>

                        <!-- Bio info -->
                        <div class="space-y-2 z-10 w-full">
                            <h3 class="text-base font-black text-slate-900 truncate" x-text="name ? name : 'Your Name'"></h3>
                            <p class="text-[10px] text-slate-400 font-bold truncate" x-text="email ? email : 'your.email@example.com'"></p>

                            <div class="pt-2 flex justify-center">
                                <span class="inline-flex items-center gap-1 bg-orange-50 border border-orange-100/50 px-3.5 py-1 rounded-full text-[8px] font-black uppercase text-orange-700 tracking-wider">
                                    <i class="fas fa-user-circle"></i> NEW MEMBER
                                </span>
                            </div>
                        </div>

                        <!-- Member metrics details -->
                        <div class="grid grid-cols-2 gap-2 w-full pt-4 border-t border-slate-50">
                            <div class="text-left bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">JOIN DATE</p>
                                <p class="text-[10px] font-bold text-slate-800 mt-0.5">Today</p>
                            </div>
                            <div class="text-left bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">TIER</p>
                                <p class="text-[10px] font-black text-orange-600 uppercase mt-0.5">CUSTOMER</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
