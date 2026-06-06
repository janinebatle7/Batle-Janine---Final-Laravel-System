@extends('layouts.app')

@section('content')
<div x-data="{
    activeTab: 'all',
    ordersCount: {{ $orders->count() }},
    lightboxOpen: false,
    lightboxUrl: '',
    lightboxProductName: '',

    init() {
        // Setup sound chime for new orders
        const prevCount = parseInt(localStorage.getItem('kitchen_orders_count') || '0', 10);
        if (this.ordersCount > prevCount && prevCount > 0) {
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const now = audioCtx.currentTime;

                // First chime (A5)
                const osc1 = audioCtx.createOscillator();
                const gain1 = audioCtx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(880, now);
                gain1.gain.setValueAtTime(0.2, now);
                gain1.gain.exponentialRampToValueAtTime(0.01, now + 0.4);
                osc1.connect(gain1);
                gain1.connect(audioCtx.destination);
                osc1.start(now);
                osc1.stop(now + 0.4);

                // Second chime (C#6) slightly offset
                setTimeout(() => {
                    const osc2 = audioCtx.createOscillator();
                    const gain2 = audioCtx.createGain();
                    osc2.type = 'sine';
                    osc2.frequency.setValueAtTime(1109.73, audioCtx.currentTime);
                    gain2.gain.setValueAtTime(0.2, audioCtx.currentTime);
                    gain2.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.6);
                    osc2.connect(gain2);
                    gain2.connect(audioCtx.destination);
                    osc2.start();
                    osc2.stop(audioCtx.currentTime + 0.6);
                }, 150);
            } catch(e) {
                console.log('Chime synthesis blocked or unsupported', e);
            }
        }
        localStorage.setItem('kitchen_orders_count', this.ordersCount);
    }
}" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 pb-20">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 pb-2">
        <div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                Kitchen Control Queue
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-orange-100 text-orange-600 animate-pulse">
                    <span class="w-2.5 h-2.5 rounded-full bg-orange-500 mr-2"></span>
                    Live Broadcast
                </span>
            </h1>
            <p class="text-slate-500 font-bold mt-1">Orchestrate visual preparation streams, manage chef timelines, and push instantaneous buyer updates</p>
        </div>
    </div>

    <!-- Interactive Filters -->
    <div class="flex items-center justify-between border-b border-slate-200 pb-5 gap-4 overflow-x-auto">
        <div class="flex items-center gap-2 bg-slate-100 p-1.5 rounded-2xl shadow-sm shrink-0">
            <button @click="activeTab = 'all'"
                :class="activeTab === 'all' ? 'bg-white text-slate-900 shadow-md font-extrabold' : 'text-slate-500 hover:text-slate-950 font-bold'"
                class="px-5 py-2.5 rounded-xl text-xs transition-all flex items-center gap-2 cursor-pointer">
                All Active List
                <span class="bg-slate-200 text-slate-700 px-2 py-0.5 rounded-lg text-[9px] font-black">
                     {{ $orders->count() }}
                </span>
            </button>
            <button @click="activeTab = 'Pending'"
                :class="activeTab === 'Pending' ? 'bg-amber-500 text-white shadow-md font-extrabold' : 'text-slate-500 hover:text-slate-950 font-bold'"
                class="px-5 py-2.5 rounded-xl text-xs transition-all flex items-center gap-2 cursor-pointer">
                Pending Verification
                <span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded-lg text-[9px] font-black">
                     {{ $orders->where('status', 'Pending')->count() }}
                </span>
            </button>
            <button @click="activeTab = 'Confirmed'"
                :class="activeTab === 'Confirmed' ? 'bg-slate-950 text-white shadow-md font-extrabold' : 'text-slate-500 hover:text-slate-950 font-bold'"
                class="px-5 py-2.5 rounded-xl text-xs transition-all flex items-center gap-2 cursor-pointer font-black">
                Queued
                <span class="bg-slate-200 text-slate-700 px-2 py-0.5 rounded-lg text-[9px] font-black">
                     {{ $orders->where('status', 'Confirmed')->count() }}
                </span>
            </button>
            <button @click="activeTab = 'Preparing'"
                :class="activeTab === 'Preparing' ? 'bg-orange-500 text-white shadow-md font-extrabold' : 'text-slate-500 hover:text-slate-950 font-bold'"
                class="px-5 py-2.5 rounded-xl text-xs transition-all flex items-center gap-2 cursor-pointer font-black">
                Preparing
                <span class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded-lg text-[9px] font-black">
                     {{ $orders->where('status', 'Preparing')->count() }}
                </span>
            </button>
            <button @click="activeTab = 'Ready'"
                :class="activeTab === 'Ready' ? 'bg-emerald-600 text-white shadow-md font-extrabold' : 'text-slate-500 hover:text-slate-950 font-bold'"
                class="px-5 py-2.5 rounded-xl text-xs transition-all flex items-center gap-2 cursor-pointer font-black">
                Ready Counter
                <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-lg text-[9px] font-black">
                     {{ $orders->where('status', 'Ready')->count() }}
                </span>
            </button>
        </div>

        <div class="hidden sm:block text-xs font-black text-slate-400 uppercase tracking-widest bg-slate-100 py-1.5 px-4 rounded-xl">
             Chef Space Orchestrator
        </div>
    </div>

    <!-- Active Orders grid wrapper -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        @forelse($orders as $order)
            <div x-show="activeTab === 'all' || activeTab === '{{ $order->status }}'"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-[2.5rem] shadow-sm border-2 overflow-hidden flex flex-col group transition-all hover:shadow-2xl
                 {{ $order->status == 'Pending' ? 'border-amber-400 ring-4 ring-amber-50 animate-pulse' : '' }}
                 {{ $order->status == 'Confirmed' ? 'border-slate-100 shadow-sm' : '' }}
                 {{ $order->status == 'Preparing' ? 'border-orange-500 ring-4 ring-orange-100' : '' }}
                 {{ $order->status == 'Ready' ? 'border-emerald-500 ring-4 ring-emerald-50' : '' }}
                 ">

                <!-- Card Header -->
                <div class="p-6 border-b border-slate-50 flex items-start justify-between bg-slate-50/50 relative overflow-hidden">
                    <div class="space-y-1 z-10">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">#ORD-{{ $order->id }}</p>
                        <h3 class="text-lg font-black text-slate-900 group-hover:text-orange-600 transition-colors">{{ $order->user->name }}</h3>

                        <!-- Elapsed Timeline counter -->
                        <div x-data="{
                            elapsedMins: 0,
                            init() {
                                const createdAt = new Date('{{ $order->created_at->toIso8601String() }}');
                                const update = () => {
                                    const diffMs = Date.now() - createdAt.getTime();
                                    this.elapsedMins = Math.floor(diffMs / 60000);
                                };
                                update();
                                setInterval(update, 30000);
                            }
                        }" class="pt-1 select-none">
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-1 rounded-lg border
                                {{ $order->status == 'Pending' ? 'bg-amber-50 text-amber-700 border-amber-100' : '' }}
                                {{ $order->status == 'Confirmed' ? 'bg-slate-50 text-slate-600 border-slate-100' : '' }}
                                {{ $order->status == 'Preparing' ? 'bg-orange-50 text-orange-700 border-orange-100' : '' }}
                                {{ $order->status == 'Ready' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : '' }}
                            ">
                                <i class="fas fa-clock text-[9px] animate-spin" style="animation-duration: 9s;"></i>
                                <span class="font-bold" x-text="elapsedMins + 'm'"></span> elapsed
                            </span>
                        </div>
                    </div>

                    <!-- Prep timing/method layout -->
                    <div class="text-right z-10 shrink-0">
                        @if($order->order_type == 'Scheduled')
                            <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-lg text-[10px] font-black uppercase flex items-center justify-end gap-1">
                                <i class="fas fa-clock"></i> Scheduled
                            </span>
                            <p class="text-xs font-black text-purple-900 mt-1">{{ $order->scheduled_datetime ? \Carbon\Carbon::parse($order->scheduled_datetime)->format('H:i') : 'N/A' }}</p>
                        @else
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-lg text-[10px] font-black uppercase inline-block">Immediate</span>
                        @endif

                        <div class="mt-2.5 text-right">
                            <span class="text-[9px] font-black px-2 py-0.5 rounded uppercase tracking-wide border
                                {{ $order->payment_status == 'Paid' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-red-50 text-red-600 border-red-100' }}">
                                {{ $order->payment_status }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Card Body (Items inside order) -->
                <div class="p-6 flex-grow space-y-4 bg-white">
                    @foreach($order->details as $detail)
                        <div class="flex items-start gap-4">
                            <!-- Menu Item Image container -->
                            @if($detail->menuItem->image)
                                <div class="relative w-12 h-12 rounded-xl overflow-hidden shadow-sm border border-slate-100 shrink-0 group/img cursor-pointer bg-slate-50"
                                     @click="lightboxUrl = '{{ $detail->menuItem->image }}'; lightboxProductName = '{{ addslashes($detail->menuItem->name) }}'; lightboxOpen = true;">
                                    <img src="{{ $detail->menuItem->image }}" class="w-full h-full object-cover group-hover/img:scale-110 transition-transform duration-300" alt="{{ $detail->menuItem->name }}">
                                    <div class="absolute inset-0 bg-black/35 opacity-0 group-hover/img:opacity-100 flex items-center justify-center transition-opacity duration-200">
                                        <i class="fas fa-search-plus text-white text-xs"></i>
                                    </div>
                                </div>
                            @else
                                <div class="w-12 h-12 rounded-xl bg-slate-50 border border-dashed border-slate-200 flex items-center justify-center text-slate-300 shrink-0">
                                    <i class="fas fa-utensils text-sm"></i>
                                </div>
                            @endif

                            <div class="flex-grow space-y-1">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-sm font-black text-slate-800 leading-snug">{{ $detail->menuItem->name }}</p>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-black bg-slate-100 text-slate-700">
                                        {{ $detail->quantity }}x
                                    </span>
                                </div>
                                @if($detail->special_instructions)
                                    <div class="p-2 bg-amber-50/50 border border-amber-100 rounded-xl text-amber-800 font-bold text-[10.5px] italic mt-1 flex items-start gap-1 pb-2 font-black leading-snug">
                                        <i class="fas fa-exclamation-circle text-amber-500 text-[10px] mt-0.5 shrink-0"></i>
                                        <span>"{{ $detail->special_instructions }}"</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Card Footer Action Panel -->
                <div class="p-6 bg-slate-50/50 border-t border-slate-50 space-y-3 shrink-0">
                    @if($order->status == 'Pending')
                        <!-- Two-action confirm flow for Pending orders -->
                        <div class="grid grid-cols-2 gap-3">
                            <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="col-span-1">
                                @csrf
                                <input type="hidden" name="status" value="Confirmed">
                                <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-black py-3.5 rounded-2xl text-[11px] uppercase tracking-wide transition-all shadow active:scale-95 cursor-pointer">
                                    Confirm
                                </button>
                            </form>
                            <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="col-span-1">
                                @csrf
                                <input type="hidden" name="status" value="Preparing">
                                <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-black py-3.5 rounded-2xl text-[11px] uppercase tracking-wide transition-all shadow active:scale-95 cursor-pointer">
                                    Prep Now
                                </button>
                            </form>
                        </div>
                        <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" onsubmit="return confirm('Cancel this order?');">
                            @csrf
                            <input type="hidden" name="status" value="Cancelled">
                            <button type="submit" class="w-full text-slate-500 hover:text-red-600 bg-white hover:bg-slate-100 border border-slate-200 font-black py-3 rounded-2xl text-[10px] uppercase tracking-wide transition-colors cursor-pointer text-center">
                                Void/Cancel Order
                            </button>
                        </form>
                    @elseif($order->status == 'Confirmed')
                        <!-- Start prep -->
                        <div class="grid grid-cols-1 gap-2">
                            <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="Preparing">
                                <button type="submit" class="w-full bg-slate-900 hover:bg-orange-600 text-white font-black py-4 rounded-2xl transition-all shadow shadow-orange-100 active:scale-95 cursor-pointer text-sm">
                                    Start Preparation
                                </button>
                            </form>
                            <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" onsubmit="return confirm('Void/Cancel this order?');">
                                @csrf
                                <input type="hidden" name="status" value="Cancelled">
                                <button type="submit" class="w-full text-xs text-slate-400 font-black hover:underline py-1.5 cursor-pointer text-center">
                                    Void Order
                                </button>
                            </form>
                        </div>
                    @elseif($order->status == 'Preparing')
                        <!-- Ready -->
                        <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="Ready">
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 rounded-2xl transition-all shadow-lg active:scale-95 cursor-pointer text-sm font-black">
                                Mark as Ready
                            </button>
                        </form>
                    @elseif($order->status == 'Ready')
                        <!-- Complete -->
                        <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="Completed">
                            <button type="submit" class="w-full bg-slate-900 hover:bg-slate-700 text-white font-black py-4 rounded-2xl transition-all shadow-lg active:scale-95 cursor-pointer text-sm font-black">
                                Deliver & Complete
                            </button>
                        </form>
                    @endif
                </div>

            </div>
        @empty
            <div class="col-span-full py-24 text-center">
                <div class="bg-white w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm border">
                    <i class="fas fa-utensils text-3xl text-slate-200"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-900">Kitchen is Zen.</h3>
                <p class="text-slate-500 font-medium mt-1">All orders are up to date! Breathe in, breathe out.</p>
            </div>
        @endforelse
    </div>

    <!-- Lightbox Modal for Menu Item Plating/Presentation Reference -->
    <div x-show="lightboxOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div @click.away="lightboxOpen = false"
             class="bg-white rounded-[2.5rem] border border-slate-100 shadow-2xl overflow-hidden max-w-lg w-full transform transition-all"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <!-- Modal Header -->
            <div class="px-6 py-4 bg-slate-50 border-b flex items-center justify-between">
                <div>
                    <span class="text-[9px] font-black text-orange-600 uppercase tracking-widest block mb-0.5">Kitchen Recipe Reference</span>
                    <h4 class="text-sm font-black text-slate-900" x-text="lightboxProductName"></h4>
                </div>
                <button @click="lightboxOpen = false" class="text-slate-400 hover:text-slate-900 transition-colors cursor-pointer w-8 h-8 rounded-xl bg-white border flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-6 flex flex-col items-center">
                <img :src="lightboxUrl" class="max-h-[380px] w-full rounded-2xl object-cover border shadow-md" alt="Dynamic Menu Item Image">
                <p class="text-slate-500 text-xs font-bold text-center mt-4">
                    <i class="fas fa-info-circle mr-1 text-slate-400"></i> Standard presentation and plating visual reference.
                </p>
            </div>
        </div>
    </div>

</div>
@endsection
