@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10" x-data="{
    showReceiptModal: false,
    receiptUrl: '',
    receiptRef: '',
    verifyUrl: '',
    customerName: '',
    orderAmount: '',
    orderId: ''
}">
    <div class="flex items-center justify-between">
        <h1 class="text-4xl font-black text-slate-900 tracking-tight">Admin Control</h1>
        <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-2xl border shadow-sm">
            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
            <p class="text-xs font-black uppercase text-slate-500">System Live</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 space-y-4">
            <div class="bg-orange-100 w-12 h-12 rounded-2xl flex items-center justify-center text-orange-600">
                <i class="fas fa-coins text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Total Revenue</p>
                <p class="text-3xl font-black text-slate-900">₱{{ number_format($stats['total_sales']) }}</p>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 space-y-4">
            <div class="bg-blue-100 w-12 h-12 rounded-2xl flex items-center justify-center text-blue-600">
                <i class="fas fa-receipt text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Pending Verification</p>
                <p class="text-3xl font-black text-slate-900">{{ $stats['needs_verification'] }}</p>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 space-y-4">
            <div class="bg-emerald-100 w-12 h-12 rounded-2xl flex items-center justify-center text-emerald-600">
                <i class="fas fa-fire text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Kitchen Queue</p>
                <p class="text-3xl font-black text-slate-900">{{ $stats['kitchen_queue'] }}</p>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 space-y-4">
            <div class="bg-purple-100 w-12 h-12 rounded-2xl flex items-center justify-center text-purple-600">
                <i class="fas fa-clock text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Pending Orders</p>
                <p class="text-3xl font-black text-slate-900">{{ $stats['pending_orders'] }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <!-- Recent Orders -->
        <div class="lg:col-span-8 bg-white rounded-[3rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-8 border-b border-slate-50 flex items-center justify-between">
                <h3 class="text-xl font-black text-slate-900 uppercase tracking-widest">Recent Activity</h3>
                <a href="{{ route('admin.orders.log') }}" class="text-xs font-black text-orange-600 hover:underline">View All Log</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-8 py-4 text-[10px] font-black uppercase text-slate-400">Order ID</th>
                            <th class="px-8 py-4 text-[10px] font-black uppercase text-slate-400">Customer</th>
                            <th class="px-8 py-4 text-[10px] font-black uppercase text-slate-400">Status</th>
                            <th class="px-8 py-4 text-[10px] font-black uppercase text-slate-400 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($recent_orders as $order)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-8 py-6">
                                <div class="space-y-1">
                                    <p class="text-sm font-black text-slate-900">#ORD-{{ $order->id }}</p>
                                    <p class="text-[10px] font-bold text-slate-400">{{ $order->created_at->diffForHumans() }}</p>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="bg-gradient-to-tr from-orange-500 to-amber-400 text-white h-9 w-9 rounded-full flex items-center justify-center font-black text-xs shadow-md shadow-orange-100">
                                        {{ substr($order->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-slate-800 leading-tight">{{ $order->user->name }}</p>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">{{ $order->order_type }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-extrabold uppercase tracking-wider {{ $order->status == 'Completed' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : ($order->status == 'Pending' ? 'bg-amber-50 text-amber-600 border border-amber-100 animate-pulse' : 'bg-orange-50 text-orange-600 border border-orange-100') }}">
                                        {{ $order->status }}
                                    </span>

                                    @if($order->status === 'Pending')
                                        <div class="flex items-center gap-2 shrink-0">
                                            <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="Confirmed">
                                                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-black text-[10px] uppercase py-1.5 px-3 rounded-lg transition-colors cursor-pointer shadow-sm shadow-emerald-100 flex items-center gap-1">
                                                    <i class="fas fa-check font-black"></i> Accept
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="inline" onsubmit="return confirm('Cancel this order?');">
                                                @csrf
                                                <input type="hidden" name="status" value="Cancelled">
                                                <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-600 font-black text-[10px] uppercase py-1.5 px-3 rounded-lg transition-colors cursor-pointer border border-rose-100">
                                                    <i class="fas fa-times"></i> Cancel
                                                </button>
                                            </form>
                                        </div>
                                    @endif

                                    @if($order->payment_method === 'GCash')
                                        @if($order->payment_status === 'Pending Verification' && auth()->user()->role === 'admin')
                                            <button type="button"
                                                @click="receiptUrl = '{{ $order->payment?->proof_image ?? '' }}';
                                                        receiptRef = '{{ $order->payment?->reference_number ?? 'N/A' }}';
                                                        verifyUrl = '{{ route('admin.orders.verify', $order->id) }}';
                                                        customerName = '{{ $order->user->name }}';
                                                        orderAmount = '₱{{ number_format($order->total_amount) }}';
                                                        orderId = '#ORD-{{ $order->id }}';
                                                        showReceiptModal = true;"
                                                class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-black text-[10px] uppercase tracking-wider py-1.5 px-3 rounded-lg transition-colors cursor-pointer shadow-sm shadow-blue-100 gap-1">
                                                <i class="fas fa-qrcode text-[9px]"></i> View & Verify GCash
                                            </button>
                                        @elseif($order->payment_status === 'Paid')
                                            <span class="inline-flex items-center gap-1.5 text-emerald-600 text-[10px] font-black uppercase bg-emerald-50 border border-emerald-100 px-2.5 py-1 rounded-lg">
                                                <i class="fas fa-check-double text-[9px]"></i> Paid (GCash)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 text-blue-600 text-[10px] font-black uppercase bg-blue-50 border border-blue-100 px-2.5 py-1 rounded-lg">
                                                <i class="fas fa-qrcode text-[9px]"></i> {{ $order->payment_status }}
                                            </span>
                                        @endif
                                    @else
                                        <!-- Cash on Collection -->
                                        @if($order->payment_status === 'Paid')
                                            <span class="inline-flex items-center gap-1.5 text-emerald-600 text-[10px] font-black uppercase bg-emerald-50 border border-emerald-100 px-2.5 py-1 rounded-lg">
                                                <i class="fas fa-money-bill-wave text-[9px]"></i> Paid (Cash)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 text-amber-600 text-[10px] font-black uppercase bg-amber-50 border border-amber-100 px-2.5 py-1 rounded-lg">
                                                <i class="fas fa-hand-holding-dollar text-[9px]"></i> Pay on Collection
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-6 text-right font-black text-slate-900 text-base">
                                ₱{{ number_format($order->total_amount) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Category Sales -->
        <div class="lg:col-span-4 bg-slate-900 rounded-[3rem] p-10 text-white space-y-8 shadow-2xl">
            <h3 class="text-xl font-black uppercase tracking-widest opacity-60">Sales Performance</h3>
            <div class="space-y-6">
                @foreach($category_sales as $cat)
                <div class="space-y-2">
                    <div class="flex justify-between items-end text-sm">
                        <span class="font-bold opacity-80">{{ $cat->category }}</span>
                        <span class="font-black">₱{{ number_format($cat->total) }}</span>
                    </div>
                    <div class="w-full bg-white/10 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-orange-500 h-full rounded-full" style="width: 75%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="pt-8 border-t border-white/10">
                <div class="bg-white/5 p-6 rounded-3xl border border-white/5 space-y-2">
                    <p class="text-xs font-black uppercase opacity-40">System Efficiency</p>
                    <p class="text-3xl font-black">98.4%</p>
                    <p class="text-[10px] font-bold text-emerald-400 flex items-center gap-1">
                        <i class="fas fa-caret-up"></i> +2.3% from last month
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Spectacular dynamic receipt lightbox modal -->
    <div x-show="showReceiptModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">

        <div class="bg-white rounded-[3rem] shadow-2xl max-w-4xl w-full overflow-hidden border border-slate-100 mx-auto flex flex-col md:flex-row">
            <!-- Left Panel: Large visual preview space -->
            <div class="md:w-1/2 bg-slate-50 border-r border-slate-100 flex flex-col justify-between items-center relative p-8">
                <template x-if="receiptUrl && receiptUrl.trim() !== ''">
                    <div class="w-full flex-grow flex items-center justify-center p-3">
                        <img :src="receiptUrl" class="max-h-[380px] w-auto border-4 border-white shadow-2xl rounded-3xl object-contain hover:scale-105 transition-transform duration-300 pointer-events-auto" alt="GCash Screenshot Proof">
                    </div>
                </template>
                <template x-if="!receiptUrl || receiptUrl.trim() === ''">
                    <div class="flex-grow flex flex-col items-center justify-center p-8 text-center space-y-4 min-h-[300px]">
                        <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center border-2 border-dashed border-amber-200 animate-pulse text-amber-500">
                            <i class="fas fa-image text-3xl"></i>
                        </div>
                        <div>
                            <p class="font-black text-slate-800 text-lg uppercase tracking-wider">No Image Attachment</p>
                            <p class="text-slate-400 font-bold text-xs mt-1">Order submitted reference number only. Please manually verify against GCash account.</p>
                        </div>
                    </div>
                </template>
                <div class="mt-4 w-full bg-slate-100 rounded-2xl py-3 px-4 text-center">
                    <p class="text-[10px] font-black tracking-widest text-slate-400 uppercase">Payment Method</p>
                    <p class="text-sm font-black text-blue-600 flex items-center justify-center gap-1.5 mt-1">
                        <i class="fab fa-google-pay text-lg"></i> MOBILE DIGITAL GCASH
                    </p>
                </div>
            </div>

            <!-- Right Panel: Metadata details + Verification triggers -->
            <div class="md:w-1/2 p-10 flex flex-col justify-between space-y-8">
                <!-- Header -->
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-xs font-black text-blue-500 uppercase tracking-widest block mb-1">GCash Payment Evidence</span>
                        <h3 class="text-3xl font-black text-slate-900 tracking-tight" x-text="orderId"></h3>
                    </div>
                    <button type="button" @click="showReceiptModal = false" class="p-3 bg-slate-50 hover:bg-slate-100 text-slate-400 hover:text-slate-700 rounded-2xl transition-colors cursor-pointer">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Specs -->
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100">
                            <span class="text-[9px] font-black uppercase text-slate-400 tracking-widest block">Customer Name</span>
                            <span class="font-extrabold text-slate-800 text-sm block mt-1" x-text="customerName"></span>
                        </div>
                        <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100">
                            <span class="text-[9px] font-black uppercase text-slate-400 tracking-widest block">Transaction Value</span>
                            <span class="font-extrabold text-orange-600 text-sm block mt-1" x-text="orderAmount"></span>
                        </div>
                    </div>

                    <div class="bg-blue-50/50 p-5 rounded-3xl border border-blue-100/50 space-y-1">
                        <span class="text-[9px] font-black uppercase text-blue-400 tracking-widest block">Submitted GCash Reference</span>
                        <p class="text-xl font-black text-blue-700 tracking-wider font-mono flex items-center gap-2 mt-1">
                            <i class="fas fa-fingerprint text-xs opacity-60"></i>
                            <span x-text="receiptRef"></span>
                        </p>
                        <span class="text-[10px] text-blue-500 font-bold block pt-1 leading-relaxed">* Cross-confirm with GCash dashboard before checking verified.</span>
                    </div>
                </div>

                <!-- Action form -->
                <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3 w-full">
                    <button type="button" @click="showReceiptModal = false" class="px-6 py-4 rounded-2xl text-slate-500 hover:bg-slate-50 text-sm font-black transition-colors">
                        Close View
                    </button>
                    <form :action="verifyUrl" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-4 rounded-2xl text-sm font-black transition-all shadow-xl shadow-emerald-100 active:scale-95 flex items-center gap-2 cursor-pointer">
                            <i class="fas fa-check-circle"></i> Approve & Verify
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
