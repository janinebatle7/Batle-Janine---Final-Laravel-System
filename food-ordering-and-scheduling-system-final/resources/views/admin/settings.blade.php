@extends('layouts.app')

@section('title', 'Admin Settings')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">System Settings</h1>
            <p class="text-slate-500 font-bold mt-1">Manage administrative account profiles, update secure credentials, or configure payment gateways</p>
        </div>
    </div>

    @if($errors->any())
        <div class="p-6 bg-red-50 border border-red-100 text-red-600 font-bold rounded-3xl text-sm leading-relaxed">
            <p class="font-black uppercase tracking-wider text-xs mb-1">
                <i class="fas fa-exclamation-triangle mr-1"></i> Form Validation Failures
            </p>
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        <!-- Left Side: Profile & Password settings split columns -->
        <div class="lg:col-span-7 space-y-10">
            <!-- Account Profile Settings form -->
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-8 md:p-10 space-y-6">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 flex items-center gap-3">
                        <span class="bg-orange-50 text-orange-600 w-10 h-10 rounded-xl flex items-center justify-center text-sm">
                            <i class="fas fa-user-circle"></i>
                        </span>
                        Account Profile
                    </h2>
                    <p class="text-xs text-slate-400 font-bold mt-1">Refine your administrative metadata and email keys</p>
                </div>

                <form action="{{ route('admin.settings.profile') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Administrator Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all">
                        </div>
                    </div>
                    <div class="pt-2 flex justify-end">
                        <button type="submit" class="bg-slate-950 hover:bg-orange-600 text-white font-black px-6 py-3.5 rounded-2xl text-xs transition-colors shadow-md active:scale-95 cursor-pointer">
                            Update Profile Details
                        </button>
                    </div>
                </form>
            </div>

            <!-- Password Credentials Profile Form -->
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-8 md:p-10 space-y-6">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 flex items-center gap-3">
                        <span class="bg-orange-50 text-orange-600 w-10 h-10 rounded-xl flex items-center justify-center text-sm">
                            <i class="fas fa-key"></i>
                        </span>
                        Change Security Key
                    </h2>
                    <p class="text-xs text-slate-400 font-bold mt-1">Keep administrative endpoints secure. Enter a strong key.</p>
                </div>

                <form action="{{ route('admin.settings.password') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Current Password</label>
                            <input type="password" name="current_password" required
                                class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">New Password</label>
                                <input type="password" name="password" required
                                    class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Confirm Password</label>
                                <input type="password" name="password_confirmation" required
                                    class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all">
                            </div>
                        </div>
                    </div>
                    <div class="pt-2 flex justify-end">
                        <button type="submit" class="bg-slate-950 hover:bg-orange-600 text-white font-black px-6 py-3.5 rounded-2xl text-xs transition-colors shadow-md active:scale-95 cursor-pointer">
                            Reset Password Token
                        </button>
                    </div>
                </form>
            </div>

            <!-- Open Hours Settings Form -->
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-8 md:p-10 space-y-6">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 flex items-center gap-3">
                        <span class="bg-emerald-50 text-emerald-600 w-10 h-10 rounded-xl flex items-center justify-center text-sm">
                            <i class="fas fa-clock"></i>
                        </span>
                        Open Hours Settings
                    </h2>
                    <p class="text-xs text-slate-400 font-bold mt-1">Configure business operating schedule and manual closure overrides</p>
                </div>

                <form action="{{ route('admin.settings.open_hours') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Global Toggles -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="flex items-center gap-4 p-4 rounded-2xl border border-slate-100 bg-slate-50 cursor-pointer hover:bg-slate-100/50 transition-colors">
                            <input type="checkbox" name="always_open" value="1" {{ ($openHours['always_open'] ?? false) ? 'checked' : '' }}
                                class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                            <div>
                                <p class="text-xs font-black text-slate-900">Always Open 24/7</p>
                                <p class="text-[9px] text-slate-400 font-bold">Unlocks picking and delivery at all times</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-4 p-4 rounded-2xl border border-slate-100 bg-slate-50 cursor-pointer hover:bg-slate-100/50 transition-colors">
                            <input type="checkbox" name="override_closed" value="1" {{ ($openHours['override_closed'] ?? false) ? 'checked' : '' }}
                                class="w-5 h-5 rounded text-red-600 focus:ring-red-500 border-slate-300">
                            <div>
                                <p class="text-xs font-black text-red-600">Force Store Closed</p>
                                <p class="text-[9px] text-slate-400 font-bold">Overrides regular hours to show as offline</p>
                            </div>
                        </label>
                    </div>

                    <!-- Custom Closure Message -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Custom Offline / Closed Message</label>
                        <textarea name="override_closed_message" rows="2" required
                            class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition-all text-xs"
                            placeholder="Display message shown to customers when kitchen is closed...">{{ old('override_closed_message', $openHours['override_closed_message'] ?? '') }}</textarea>
                    </div>

                    <!-- Day by Day Grid -->
                    <div class="space-y-3 pt-4 border-t border-slate-100">
                        <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest block mb-1">Weekly Operating Schedule</label>
                        <div class="space-y-2">
                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                @php
                                    $dayConfig = $openHours['days'][$day] ?? ['is_open' => true, 'open_time' => '09:00', 'close_time' => '21:00'];
                                @endphp
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-3 bg-slate-50 rounded-2xl border border-slate-100"
                                     x-data="{ isOpen: {{ ($dayConfig['is_open'] ?? true) ? 'true' : 'false' }} }">

                                    <!-- Day Toggle -->
                                    <div class="flex items-center gap-3 min-w-[120px]">
                                        <input type="hidden" name="days[{{ $day }}][is_open]" value="0">
                                        <input type="checkbox" name="days[{{ $day }}][is_open]" value="1" x-model="isOpen"
                                            class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                                        <span class="text-xs font-black" :class="isOpen ? 'text-slate-900' : 'text-slate-400'" x-text="'{{ $day }}'"></span>
                                    </div>

                                    <!-- Time Inputs -->
                                    <div class="flex items-center gap-2" x-show="isOpen" x-cloak>
                                        <input type="time" name="days[{{ $day }}][open_time]" value="{{ $dayConfig['open_time'] ?? '09:00' }}"
                                            class="p-2 border border-slate-200 rounded-xl bg-white font-bold text-xs focus:ring-2 focus:ring-emerald-50 focus:border-emerald-500 outline-none">
                                        <span class="text-[10px] font-black text-slate-400 uppercase">to</span>
                                        <input type="time" name="days[{{ $day }}][close_time]" value="{{ $dayConfig['close_time'] ?? '21:00' }}"
                                            class="p-2 border border-slate-200 rounded-xl bg-white font-bold text-xs focus:ring-2 focus:ring-emerald-50 focus:border-emerald-500 outline-none">
                                    </div>

                                    <div class="text-left py-1 text-[11px] font-bold text-slate-400" x-show="!isOpen" x-cloak>
                                        <i class="fas fa-door-closed mr-1 text-red-400"></i> Closed all day
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-black px-6 py-3.5 rounded-2xl text-xs transition-colors shadow-md active:scale-95 cursor-pointer">
                            Apply Business Hours
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Side: Fully manageable digital GCash details -->
        <div class="lg:col-span-5 space-y-10" x-data="{
            number: '{{ old('number', $gcash['number'] ?? '0912 345 6789') }}',
            name: '{{ old('name', $gcash['name'] ?? 'Clara B. | GCash Professional') }}',
            qrType: '{{ $gcash['qr_code'] && str_starts_with($gcash['qr_code'], '/uploads/gcash/') ? 'file' : 'url' }}',
            qrCodeUrl: '{{ $gcash['qr_code'] && !str_starts_with($gcash['qr_code'], '/uploads/gcash/') ? $gcash['qr_code'] : '' }}',
            qrCodeFilePreview: '{{ $gcash['qr_code'] && str_starts_with($gcash['qr_code'], '/uploads/gcash/') ? $gcash['qr_code'] : null }}',

            get computedQrCode() {
                if (this.qrType === 'file' && this.qrCodeFilePreview) {
                    return this.qrCodeFilePreview;
                }
                if (this.qrType === 'url' && this.qrCodeUrl && this.qrCodeUrl.trim() !== '') {
                    return this.qrCodeUrl;
                }
                return null;
            },

            updateFilePreview(event) {
                const file = event.target.files[0];
                if (file) {
                    this.qrCodeFilePreview = URL.createObjectURL(file);
                }
            }
        }">
            <!-- GCash manager form -->
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-8 md:p-10 space-y-8">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 flex items-center gap-3">
                        <span class="bg-blue-50 text-blue-600 w-10 h-10 rounded-xl flex items-center justify-center text-sm">
                            <i class="fas fa-qrcode"></i>
                        </span>
                        GCash Settings
                    </h2>
                    <p class="text-xs text-slate-400 font-bold mt-1">Configure live details displayed to users at checkout</p>
                </div>

                <form action="{{ route('admin.settings.gcash') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Active Mobile Number</label>
                            <input type="text" name="number" x-model="number" required
                                class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition-all"
                                placeholder="e.g. 0912 345 6789">
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Recipient Account Name</label>
                            <input type="text" name="name" x-model="name" required
                                class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition-all"
                                placeholder="e.g. Clara B. | Store Owner">
                        </div>

                        <!-- Image Strategy Selector -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2">QR Code Method</label>
                            <div class="grid grid-cols-2 gap-2 p-1.5 bg-slate-100 rounded-2xl">
                                <button type="button" @click="qrType = 'url'" :class="qrType === 'url' ? 'bg-white text-blue-600 shadow' : 'text-slate-500'" class="py-2.5 rounded-xl text-xs font-black transition-all">
                                    Direct URL
                                </button>
                                <button type="button" @click="qrType = 'file'" :class="qrType === 'file' ? 'bg-white text-blue-600 shadow' : 'text-slate-500'" class="py-2.5 rounded-xl text-xs font-black transition-all">
                                    Upload QR File
                                </button>
                            </div>
                        </div>

                        <!-- URL input -->
                        <div x-show="qrType === 'url'" class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2">QR Code Image URL</label>
                            <input type="url" name="qr_code_url" x-model="qrCodeUrl"
                                class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition-all placeholder-slate-300"
                                placeholder="https://">
                        </div>

                        <!-- File upload -->
                        <div x-show="qrType === 'file'" class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2">Upload QR Code File</label>
                            <div class="relative group">
                                <input type="file" name="qr_code_file" @change="updateFilePreview($event)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <div class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center group-hover:border-blue-400 transition-colors bg-slate-50">
                                    <template x-if="qrCodeFilePreview">
                                        <div class="flex items-center justify-center gap-2">
                                            <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                                            <span class="text-xs font-bold text-slate-700">QR Asset Loaded!</span>
                                        </div>
                                    </template>
                                    <template x-if="!qrCodeFilePreview">
                                        <div>
                                            <i class="fas fa-qrcode text-slate-400 text-xl mb-1 group-hover:text-blue-500"></i>
                                            <p class="text-[11px] font-bold text-slate-500">Drag/Select QR Code Image file</p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black px-6 py-3.5 rounded-2xl text-xs transition-colors shadow-md active:scale-95 cursor-pointer">
                            Persist Payment Assets
                        </button>
                    </div>
                </form>
            </div>

            <!-- Customer Checkout preview box -->
            <div class="space-y-4">
                <p class="text-xs font-extrabold text-slate-400 uppercase tracking-widest text-center">Live Checkout Preview</p>
                <div class="bg-white border rounded-[3rem] p-8 shadow-2xl relative overflow-hidden flex flex-col items-center text-center space-y-6">
                    <div class="bg-gradient-to-tr from-blue-600 to-sky-500 text-white p-6 rounded-[2rem] shadow-xl w-full relative overflow-hidden group">
                        <i class="fas fa-qrcode absolute -bottom-6 -right-6 text-9xl opacity-10"></i>
                        <div class="relative z-10 space-y-1.5 text-left">
                            <p class="text-[9px] font-black uppercase opacity-60 tracking-wider">Send Payment To</p>
                            <p class="text-2xl font-black font-sans" x-text="number ? number : '0912 345 6789'"></p>
                            <p class="text-xs font-bold" x-text="name ? name : 'Clara B. | GCash Professional'"></p>
                        </div>
                    </div>

                    <!-- Scan Block -->
                    <template x-if="computedQrCode">
                        <div class="p-3 bg-slate-50 border border-slate-100 rounded-3xl flex flex-col items-center">
                            <p class="text-[10px] font-black uppercase text-slate-400 tracking-wider mb-2">SCAN QR TO PAY</p>
                            <img :src="computedQrCode" class="w-48 h-48 object-contain rounded-2xl border-4 border-white shadow-xl" alt="QR Code Preview">
                        </div>
                    </template>
                    <template x-if="!computedQrCode">
                        <div class="p-4 bg-slate-50 border border-dashed border-slate-200 rounded-3xl w-full flex flex-col items-center justify-center min-h-[140px] text-center">
                            <i class="fas fa-qrcode text-3xl text-slate-300 mb-2"></i>
                            <p class="text-[11px] text-slate-500 font-bold">No Active Scan QR Code Uploaded</p>
                            <p class="text-[9px] text-slate-400 font-bold mt-0.5">Customers will carry out transfer manually to the mobile number displayed above</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
