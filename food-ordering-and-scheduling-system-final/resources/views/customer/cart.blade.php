@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

        <!-- Cart Items -->
        <div class="lg:col-span-7 space-y-8">
            <div class="flex items-center justify-between">
                <h2 class="text-4xl font-black text-slate-900 tracking-tight">Your Feast</h2>
                <span class="bg-orange-100 text-orange-600 px-4 py-1.5 rounded-full text-xs font-black uppercase">{{ count($cart) }} Items Selected</span>
            </div>

            @forelse($cart as $id => $details)
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 flex flex-col sm:flex-row gap-8 hover:border-orange-200 transition-colors">
                    <img src="{{ $details['image'] }}" class="w-32 h-32 rounded-[2rem] object-cover shadow-lg border-4 border-white">
                    <div class="flex-grow space-y-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-2xl font-black text-slate-900 leading-none">{{ $details['name'] }}</h3>
                                <p class="text-slate-400 font-bold text-sm mt-2">₱{{ number_format($details['price']) }} per portion</p>
                            </div>
                            <form action="{{ route('cart.remove') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $id }}">
                                <button type="submit" class="p-3 text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-2xl transition-all">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-slate-50">
                            <!-- Quantity Controls -->
                            <div class="flex items-center bg-slate-100 rounded-2xl p-1">
                                <!-- Decrement -->
                                <form action="{{ route('cart.update') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $id }}">
                                    <input type="hidden" name="quantity" value="{{ $details['quantity'] - 1 }}">
                                    <button type="submit" class="p-2 w-10 h-10 flex items-center justify-center hover:bg-white rounded-xl transition-all font-black cursor-pointer text-slate-700" {{ $details['quantity'] <= 1 ? 'disabled' : '' }}>
                                        <i class="fas fa-minus text-xs"></i>
                                    </button>
                                </form>

                                <span class="w-12 text-center font-black text-slate-900 text-sm">{{ $details['quantity'] }}</span>

                                <!-- Increment -->
                                <form action="{{ route('cart.update') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $id }}">
                                    <input type="hidden" name="quantity" value="{{ $details['quantity'] + 1 }}">
                                    <button type="submit" class="p-2 w-10 h-10 flex items-center justify-center hover:bg-white rounded-xl transition-all font-black cursor-pointer text-slate-700">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </form>
                            </div>

                            <!-- Subtotal display -->
                            <div class="text-right">
                                <p class="text-slate-400 text-[10px] font-black uppercase">Subtotal</p>
                                <p class="text-xl font-black text-slate-900">₱{{ number_format($details['price'] * $details['quantity']) }}</p>
                            </div>
                        </div>

                        <!-- Special Instructions -->
                        <div class="pt-3 border-t border-slate-100/60">
                            <form action="{{ route('cart.update') }}" method="POST" class="flex gap-2 items-center">
                                @csrf
                                <input type="hidden" name="id" value="{{ $id }}">
                                <div class="relative flex-grow">
                                    <i class="fas fa-comment-dots text-slate-300 absolute left-4 top-1/2 -translate-y-1/2 text-xs"></i>
                                    <input type="text" name="special_instructions"
                                        value="{{ old('special_instructions', $details['special_instructions'] ?? '') }}"
                                        placeholder="Add preparation notes (e.g. extra cheese, no onions)"
                                        class="w-full pl-10 pr-4 py-2 text-xs font-bold bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all placeholder-slate-400">
                                </div>
                                <button type="submit" class="bg-slate-900 hover:bg-orange-600 text-white px-4 py-2.5 rounded-xl text-[10px] font-extrabold uppercase tracking-wider transition-colors shrink-0">
                                    Apply
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-[3rem] p-20 text-center border-2 border-dashed border-slate-200">
                    <div class="bg-slate-50 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-shopping-basket text-4xl text-slate-300"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-2">Your basket is resting.</h3>
                    <p class="text-slate-500 font-medium mb-8">Fuel up your den with some beastly meals.</p>
                    <a href="{{ route('menu') }}" class="inline-flex items-center gap-2 bg-orange-600 text-white px-8 py-4 rounded-2xl font-black shadow-xl shadow-orange-100 hover:bg-orange-700 transition-all">
                        Browse Menu <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Checkout Sidebar -->
        @if(count($cart) > 0)
        <div class="lg:col-span-5">
            <div class="bg-white p-10 rounded-[3rem] shadow-2xl shadow-slate-200 border border-slate-100 sticky top-24 space-y-10">
                <h3 class="text-2xl font-black text-slate-900">Finalize Order</h3>

                <form action="{{ route('checkout') }}" method="POST" enctype="multipart/form-data"
                    x-data="{
                        orderType: '{{ old('order_type', 'Immediate') }}',
                        paymentMethod: '{{ old('payment_method', 'Cash') }}',
                        scheduledDatetime: '{{ old('scheduled_datetime', '') }}',
                        gcashRef: '{{ old('gcash_ref', '') }}',
                        proofFileName: '',
                        clientError: '',

                        validateAndSubmit(e) {
                            this.clientError = '';

                            if (this.orderType === 'Scheduled') {
                                if (!this.scheduledDatetime) {
                                    this.clientError = 'Please pick a preferred Pickup or Delivery Date & Time for Pre-Order.';
                                    e.preventDefault();
                                    return false;
                                }

                                const selectedDate = new Date(this.scheduledDatetime);
                                const now = new Date();
                                if (selectedDate <= now) {
                                    this.clientError = 'Scheduled pre-order time must be in the future. Please pick a later slot!';
                                    e.preventDefault();
                                    return false;
                                }
                            }

                            if (this.paymentMethod === 'GCash') {
                                if (!this.gcashRef || this.gcashRef.trim() === '') {
                                    this.clientError = 'Reference Number is required for GCash transfers. Please enter the last 6 digits.';
                                    e.preventDefault();
                                    return false;
                                }

                                const cleanRef = this.gcashRef.replace(/\s+/g, '');
                                if (cleanRef.length < 4 || cleanRef.length > 20) {
                                    this.clientError = 'Please enter a valid GCash Reference Number.';
                                    e.preventDefault();
                                    return false;
                                }
                            }
                            return true;
                        },
                        handleProofChange(e) {
                            const file = e.target.files[0];
                            if (file) {
                                if (file.size > 5 * 1024 * 1024) {
                                    this.clientError = 'The uploaded screenshot exceeds 5MB size limit. Please select a smaller image!';
                                    this.proofFileName = '';
                                    e.target.value = '';
                                } else {
                                    this.proofFileName = file.name;
                                    this.clientError = '';
                                }
                            } else {
                                this.proofFileName = '';
                            }
                        }
                    }"
                    @submit="validateAndSubmit($event)"
                    class="space-y-8">
                    @csrf

                    <!-- Client-side Validation Alert panel -->
                    <template x-if="clientError">
                        <div class="px-5 py-4 bg-red-50 border border-red-100 text-red-600 rounded-2xl flex items-start gap-3 text-xs font-black leading-tight animate-bounce">
                            <i class="fas fa-exclamation-circle text-base text-red-500 shrink-0 mt-0.5"></i>
                            <div class="flex-grow">
                                <span x-text="clientError"></span>
                            </div>
                            <button type="button" @click="clientError = ''" class="hover:text-red-800 cursor-pointer"><i class="fas fa-times"></i></button>
                        </div>
                    </template>

                    <!-- Order Type -->
                    <div class="space-y-4">
                        <label class="block text-sm font-black text-slate-900 uppercase tracking-widest">Fulfillment Type</label>
                        <div class="grid grid-cols-2 gap-3 p-1.5 bg-slate-100 rounded-[1.5rem]">
                            <button type="button" @click="orderType = 'Immediate'" :class="orderType === 'Immediate' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-500'" class="py-3 rounded-2xl text-sm font-black transition-all">ASAP</button>
                            <button type="button" @click="orderType = 'Scheduled'" :class="orderType === 'Scheduled' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-500'" class="py-3 rounded-2xl text-sm font-black transition-all">Pre-Order</button>
                        </div>
                        <input type="hidden" name="order_type" :value="orderType">

                        <div x-show="orderType === 'Scheduled'" x-cloak class="pt-4 space-y-2 animate-in fade-in slide-in-from-top-4 duration-500">
                            <label class="text-[10px] font-black text-slate-400 uppercase">Preferred Pickup/Delivery Time</label>
                            <input type="datetime-local" name="scheduled_datetime" x-model="scheduledDatetime" class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all">
                            @php
                                $openHours = \App\Http\Controllers\AdminDashboardController::getOpenHoursSettings();
                                if ($openHours['always_open']) {
                                    $cartHrsText = 'Open 24/7';
                                } elseif ($openHours['override_closed']) {
                                    $cartHrsText = 'Temporarily Closed';
                                } else {
                                    $todayName = \Carbon\Carbon::now('Asia/Manila')->format('l');
                                    $todayConfig = $openHours['days'][$todayName] ?? ['is_open' => true, 'open_time' => '09:00', 'close_time' => '21:00'];
                                    if (!$todayConfig['is_open']) {
                                        $cartHrsText = 'Closed Today';
                                    } else {
                                        try {
                                            $openFormatted = \Carbon\Carbon::createFromFormat('H:i', $todayConfig['open_time'])->format('g:i A');
                                            $closeFormatted = \Carbon\Carbon::createFromFormat('H:i', $todayConfig['close_time'])->format('g:i A');
                                            $cartHrsText = $openFormatted . ' - ' . $closeFormatted;
                                        } catch(\Exception $e) {
                                            $cartHrsText = '9:00 AM - 9:00 PM';
                                        }
                                    }
                                }
                            @endphp
                            <p class="text-[10px] text-slate-400 font-bold italic">* Business Hours today: {{ $cartHrsText }}</p>

                            <div class="mt-2 bg-slate-50 p-3 rounded-xl border border-slate-100 text-[10px] text-slate-500 font-medium space-y-1">
                                <span class="font-black text-slate-700 block uppercase tracking-wider mb-1"><i class="fas fa-calendar-alt mr-1"></i> Weekly operating schedule:</span>
                                @if($openHours['always_open'])
                                    <p class="text-emerald-500 font-black">Store is open 24/7. Feasts are always unlocked!</p>
                                @elseif($openHours['override_closed'])
                                    <p class="text-red-500 font-black">Store is force-closed: {{ $openHours['override_closed_message'] }}</p>
                                @else
                                    <div class="grid grid-cols-1 gap-y-1">
                                        @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                            @php $dayConfig = $openHours['days'][$day] ?? null; @endphp
                                            <div class="flex justify-between border-b border-dashed border-slate-200/60 pb-0.5">
                                                <span class="font-extrabold text-slate-600">{{ $day }}:</span>
                                                <span class="font-bold">
                                                    @if($dayConfig && ($dayConfig['is_open'] ?? true))
                                                        @php
                                                            try {
                                                                $formattedHours = \Carbon\Carbon::createFromFormat('H:i', $dayConfig['open_time'])->format('g:i A') . ' - ' . \Carbon\Carbon::createFromFormat('H:i', $dayConfig['close_time'])->format('g:i A');
                                                            } catch (\Exception $ex) {
                                                                $formattedHours = '09:00 AM - 09:00 PM';
                                                            }
                                                        @endphp
                                                        {{ $formattedHours }}
                                                    @else
                                                        <span class="text-red-500 font-black uppercase text-[9px]">Closed</span>
                                                    @endif
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="space-y-4">
                        <label class="block text-sm font-black text-slate-900 uppercase tracking-widest">Payment Strategy</label>
                        <div class="space-y-3">
                            <label :class="paymentMethod === 'Cash' ? 'border-orange-500 bg-orange-50/50 ring-2 ring-orange-100' : 'border-slate-100 hover:border-slate-300'" class="flex items-center gap-4 p-5 rounded-[1.5rem] border-2 cursor-pointer transition-all">
                                <input type="radio" name="payment_method" value="Cash" @click="paymentMethod = 'Cash'" checked class="hidden">
                                <div class="bg-emerald-100 h-10 w-10 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-money-bill-wave text-emerald-600"></i>
                                </div>
                                <div class="flex-grow">
                                    <p class="font-black text-slate-900">Cash on Collection</p>
                                    <p class="text-[10px] text-slate-400 font-black uppercase">Pickup or Delivery</p>
                                </div>
                                <div :class="paymentMethod === 'Cash' ? 'border-orange-500 border-4' : 'border-slate-200 border-2'" class="w-5 h-5 rounded-full transition-all"></div>
                            </label>

                            <label :class="paymentMethod === 'GCash' ? 'border-orange-500 bg-orange-50/50 ring-2 ring-orange-100' : 'border-slate-100 hover:border-slate-300'" class="flex items-center gap-4 p-5 rounded-[1.5rem] border-2 cursor-pointer transition-all">
                                <input type="radio" name="payment_method" value="GCash" @click="paymentMethod = 'GCash'" class="hidden">
                                <div class="bg-blue-100 h-10 w-10 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-mobile-screen-button text-blue-600"></i>
                                </div>
                                <div class="flex-grow">
                                    <p class="font-black text-slate-900">Digital GCash</p>
                                    <p class="text-[10px] text-slate-400 font-black uppercase">Manual Verification</p>
                                </div>
                                <div :class="paymentMethod === 'GCash' ? 'border-orange-500 border-4' : 'border-slate-200 border-2'" class="w-5 h-5 rounded-full transition-all"></div>
                            </label>
                        </div>

                        <!-- GCash Proof Upload -->
                        <div x-show="paymentMethod === 'GCash'" x-cloak class="pt-6 space-y-6 animate-in fade-in slide-in-from-top-4 duration-500">
                            @php
                                $gcashSettings = \App\Http\Controllers\AdminDashboardController::getGcashSettings();
                            @endphp
                            <div class="bg-blue-600 text-white p-6 rounded-[2rem] shadow-xl shadow-blue-100 relative overflow-hidden group">
                                <i class="fas fa-qrcode absolute -bottom-6 -right-6 text-9xl opacity-10 group-hover:rotate-12 transition-transform duration-700"></i>
                                <div class="relative z-10 space-y-2">
                                    <p class="text-[10px] font-black uppercase opacity-60">Send Payment To</p>
                                    <p class="text-2xl font-black">{{ $gcashSettings['number'] }}</p>
                                    <p class="text-xs font-bold">{{ $gcashSettings['name'] }}</p>
                                </div>
                            </div>
                            @if(!empty($gcashSettings['qr_code']))
                                <div class="p-4 bg-slate-50 border border-slate-100 rounded-[2rem] flex flex-col items-center">
                                    <p class="text-[10px] font-black uppercase text-slate-400 tracking-wider mb-2">Scan QR Code to Transfer</p>
                                    <img src="{{ $gcashSettings['qr_code'] }}" class="max-w-[200px] h-auto object-contain rounded-2xl border-4 border-white shadow-xl hover:scale-105 transition-transform duration-300 pointer-events-auto" alt="GCash QR Code">
                                </div>
                            @endif
                            <div class="space-y-4">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-slate-400 uppercase">Reference Number</label>
                                    <input type="text" name="gcash_ref" x-model="gcashRef" placeholder="Enter last 6 digits" class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition-all">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-slate-400 uppercase">Payment Proof</label>
                                    <div class="relative group">
                                        <input type="file" name="proof_image" @change="handleProofChange($event)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                        <div :class="proofFileName ? 'border-emerald-400 bg-emerald-50/20' : 'border-slate-200 bg-slate-50'" class="border-2 border-dashed rounded-2xl p-6 text-center group-hover:border-blue-400 transition-colors">
                                            <template x-if="proofFileName">
                                                <div>
                                                    <i class="fas fa-file-image text-emerald-500 text-2xl mb-2"></i>
                                                    <p class="text-xs font-extrabold text-emerald-700" x-text="'Sent: ' + proofFileName"></p>
                                                    <p class="text-[9px] text-slate-400 mt-1 font-bold">Screenshot attached ready for submit</p>
                                                </div>
                                            </template>
                                            <template x-if="!proofFileName">
                                                <div>
                                                    <i class="fas fa-cloud-upload-alt text-slate-400 text-2xl mb-2 group-hover:text-blue-500"></i>
                                                    <p class="text-xs font-bold text-slate-500">Upload Screenshot</p>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Totals -->
                    <div class="pt-10 border-t-2 border-slate-50 space-y-6">
                        @php $total = 0; @endphp
                        @foreach($cart as $item) @php $total += $item['price'] * $item['quantity']; @endphp @endforeach

                        <div class="flex justify-between items-end">
                            <p class="text-slate-400 font-bold">Subtotal</p>
                            <p class="text-xl font-bold text-slate-900">₱{{ number_format($total) }}</p>
                        </div>
                        <div class="flex justify-between items-end">
                            <h2 class="text-3xl font-black text-slate-900">Total</h2>
                            <h2 class="text-4xl font-black text-orange-600 tracking-tight">₱{{ number_format($total) }}</h2>
                        </div>

                        <input type="hidden" name="total_amount" value="{{ $total }}">
                        <button type="submit" class="w-full bg-slate-900 hover:bg-orange-600 text-white font-black py-6 rounded-[2rem] text-xl shadow-2xl shadow-slate-200 hover:-translate-y-1 transition-all active:scale-95 cursor-pointer">
                            Place Order Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
