@extends('layouts.app')

@section('title', 'Historical Order Logs')

@section('content')
@php
    $formattedOrders = $orders->map(function($o) {
        return [
            'id' => (string)$o->id,
            'status' => (string)$o->status,
            'type' => (string)$o->order_type,
            'scheduled' => $o->scheduled_datetime ? \Carbon\Carbon::parse($o->scheduled_datetime)->format("Y-m-d H:i") : "",
            'scheduled_formatted' => $o->scheduled_datetime ? \Carbon\Carbon::parse($o->scheduled_datetime)->format("h:i A") : "N/A",
            'customer_name' => $o->user ? (string)$o->user->name : 'Guest',
            'customer_email' => $o->user ? (string)$o->user->email : 'N/A',
            'avatar' => $o->user ? strtoupper(substr($o->user->name, 0, 1)) : 'G',
            'method' => (string)$o->payment_method,
            'payment_status' => (string)$o->payment_status,
            'amount' => (float)$o->total_amount,
            'amount_formatted' => '₱' . number_format($o->total_amount, 2),
            'date' => $o->created_at->format("M d, Y h:i A"),
            'date_human' => $o->created_at->diffForHumans(),
            'raw_date' => $o->created_at->toIso8601String(),
            'proof_image' => $o->payment?->proof_image ?? "",
            'reference_number' => $o->payment?->reference_number ?? "N/A",
            'verify_url' => route("admin.orders.verify", $o->id),
            'status_url' => route("admin.orders.status", $o->id),
            'delete_url' => route("admin.orders.destroy", $o->id),
            'items' => $o->details->map(function($d) {
                return [
                    'name' => $d->menuItem ? (string)$d->menuItem->name : 'Deleted Item',
                    'image' => $d->menuItem ? (string)$d->menuItem->image : '',
                    'category' => $d->menuItem ? (string)$d->menuItem->category : '',
                    'quantity' => (int)$d->quantity,
                    'price' => '₱' . number_format($d->menuItem ? $d->menuItem->price : 0, 2),
                    'instructions' => $d->special_instructions ?? '',
                    'subtotal' => '₱' . number_format($d->subtotal, 2)
                ];
            })->toArray()
        ];
    })->toArray();
@endphp

<script>
    window.initialOrdersLog = {!! json_encode($formattedOrders) !!};
</script>

<div x-data="orderLogComponent()" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12 pb-24 animate-in fade-in duration-300">

    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 pb-6 border-b border-sidebar-divider">
        <div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                Order Activity Ledger
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-orange-50 text-orange-600 border border-orange-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500 mr-2 animate-pulse"></span>
                    Central Audit Stream
                </span>
            </h1>
            <p class="text-slate-500 font-bold mt-1">Cross-check historical volume index, audit payment status, and verify GCash receipts securely</p>
        </div>

        <div class="flex flex-wrap items-center gap-3 shrink-0 self-start lg:self-auto">
            <!-- Back navigation button -->
            <a href="{{ route('admin.dashboard') }}" class="bg-white hover:bg-slate-50 text-slate-700 px-6 py-3.5 rounded-2xl font-black text-xs uppercase tracking-wider border shadow-sm flex items-center justify-center gap-2 cursor-pointer transition-all active:scale-95">
                <i class="fas fa-chart-pie text-orange-500 text-sm"></i> Dashboard Overview
            </a>

            <!-- PDF report export -->
            <button @click="exportPDF" class="bg-slate-950 hover:bg-orange-600 text-white px-6 py-3.5 rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg transition-all active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
                <i class="fas fa-file-pdf text-red-500 text-sm"></i> Export PDF Report
            </button>
        </div>
    </div>

    <!-- Live Performance Metrics Card Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 select-none">
        <!-- Stat Card 1 -->
        <div class="bg-white rounded-3xl p-6 border-2 border-slate-50 flex items-center gap-5 shadow-sm">
            <div class="w-12 h-12 rounded-2xl bg-slate-50 border flex items-center justify-center text-slate-700 shrink-0">
                <i class="fas fa-book-open text-lg"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Catalog Records</p>
                <p class="text-2xl font-black text-slate-950 mt-1" x-text="orders.length"></p>
            </div>
        </div>

        <!-- Stat Card 2 -->
        <div class="bg-white rounded-3xl p-6 border-2 border-slate-50 flex items-center gap-5 shadow-sm">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                <i class="fas fa-check-double text-lg animate-bounce"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Settled & Closed</p>
                <p class="text-2xl font-black text-slate-950 mt-1" x-text="orders.filter(o => o.status === 'Completed').length"></p>
            </div>
        </div>

        <!-- Stat Card 3 -->
        <div class="bg-white rounded-3xl p-6 border-2 border-slate-50 flex items-center gap-5 shadow-sm">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                <i class="fas fa-clock text-lg"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Needs Action</p>
                <p class="text-2xl font-black text-slate-950 mt-1" x-text="orders.filter(o => o.status === 'Pending').length"></p>
            </div>
        </div>

        <!-- Stat Card 4 -->
        <div class="bg-white rounded-3xl p-6 border-2 border-slate-50 flex items-center gap-5 shadow-sm">
            <div class="w-12 h-12 rounded-2xl bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600 shrink-0">
                <i class="fas fa-coins text-lg"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Gross Net Stream</p>
                <p class="text-2xl font-black text-slate-950 mt-1">
                    ₱<span x-text="orders.filter(o => o.payment_status === 'Paid').reduce((sum, o) => sum + o.amount, 0).toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2})"></span>
                </p>
            </div>
        </div>
    </div>

    <!-- Advanced Searching & Multidimensional Filters Block -->
    <div class="bg-slate-100/50 rounded-[2.5rem] border p-6 lg:p-8 space-y-6">
        <div class="flex flex-col lg:flex-row gap-5 items-stretch lg:items-center justify-between">
            <!-- Modern Search with Icon -->
            <div class="relative flex-grow max-w-lg">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="fas fa-search text-sm"></i>
                </span>
                <input type="text" x-model="search"
                    placeholder="Search by ID, customer credentials, GCash transactions..."
                    class="w-full bg-white pl-11 pr-4 py-4 rounded-2xl border border-slate-200 text-sm font-bold placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-orange-100 focus:border-orange-500 transition-all">
                <button x-show="search.length > 0" @click="search = ''" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600">
                    <i class="fas fa-times-circle text-sm"></i>
                </button>
            </div>

            <div class="text-[11px] font-black text-slate-400 uppercase tracking-widest bg-white border py-2 px-4 rounded-xl shadow-sm self-start lg:self-auto flex items-center gap-1.5">
                 Currently showing <span class="text-orange-600 font-mono text-xs" x-text="filteredOrders.length"></span> orders matching filters
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
            <!-- Filter 1: Progress Status -->
            <div class="space-y-1.5">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Progress state</label>
                <select x-model="statusFilter" class="w-full bg-white px-4 py-3 rounded-xl border border-slate-200 text-xs font-bold focus:outline-none focus:ring-4 focus:ring-orange-50">
                    <option value="all">All Progress Channels</option>
                    <option value="Pending">Pending Awaiting Accept</option>
                    <option value="Confirmed">Confirmed In Queue</option>
                    <option value="Preparing">Currently Preparing</option>
                    <option value="Ready">Ready For Collection</option>
                    <option value="Completed">Completed / Closed</option>
                    <option value="Cancelled">Cancelled Orders</option>
                </select>
            </div>

            <!-- Filter 2: Fulfillment Channel -->
            <div class="space-y-1.5">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Order Type</label>
                <select x-model="typeFilter" class="w-full bg-white px-4 py-3 rounded-xl border border-slate-200 text-xs font-bold focus:outline-none focus:ring-4 focus:ring-orange-50">
                    <option value="all">All Channels</option>
                    <option value="Immediate">Immediate / Fast Track</option>
                    <option value="Scheduled">Scheduled Takeaway / Dine</option>
                </select>
            </div>

            <!-- Filter 3: Safe Payment Parameters -->
            <div class="space-y-1.5">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Payment Clearance Status</label>
                <select x-model="paymentStatusFilter" class="w-full bg-white px-4 py-3 rounded-xl border border-slate-200 text-xs font-bold focus:outline-none focus:ring-4 focus:ring-orange-50">
                    <option value="all">All Statuses</option>
                    <option value="Unpaid">Unpaid / Tender Pending</option>
                    <option value="Pending Verification">Pending Verification</option>
                    <option value="Paid">Cleared / Paid Successfully</option>
                    <option value="Refunded">Refunded Accounts</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Main Live Interactive Order Ledger Grid -->
    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-2xl overflow-hidden min-h-[400px]">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50/70 border-b border-slate-100">
                    <tr>
                        <th class="px-8 py-5 text-[10px] font-black uppercase text-slate-400">Order Ref</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase text-slate-400">Timeline / Date</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase text-slate-400">Customer profile</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase text-slate-400 text-center">Fulfillment & Type</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase text-slate-400">Status</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase text-slate-400 text-right">Receipt & Tender</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase text-slate-400 text-center">Manage</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <!-- Dynamic client rendered Alpine row looping -->
                    <template x-for="o in filteredOrders" :key="o.id">
                        <tr class="hover:bg-slate-50/40 transition-colors animate-in fade-in duration-150">

                            <!-- ORG ID -->
                            <td class="px-8 py-5">
                                <span class="font-black text-xs text-orange-600 block">#ORD</span>
                                <span class="font-bold text-sm text-slate-900 font-mono tracking-tight" x-text="'ID-' + o.id"></span>
                            </td>

                            <!-- Timeline -->
                            <td class="px-8 py-5">
                                <div class="space-y-0.5 select-none">
                                    <p class="text-sm font-black text-slate-900" x-text="o.date_human"></p>
                                    <p class="text-[10px] font-bold text-slate-400" x-text="o.date"></p>
                                </div>
                            </td>

                            <!-- Customer Profile -->
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="bg-slate-100 text-slate-700 h-9 w-9 rounded-xl flex items-center justify-center font-black text-xs border uppercase tracking-wider shadow-sm select-none" x-text="o.avatar">
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-slate-800 leading-tight" x-text="o.customer_name"></p>
                                        <p class="text-[10px] font-bold text-slate-400 lowercase tracking-tight mt-0.5" x-text="o.customer_email"></p>
                                    </div>
                                </div>
                            </td>

                            <!-- Fulfillment & Type -->
                            <td class="px-8 py-5 text-center">
                                <div class="inline-block select-none">
                                    <template x-if="o.type === 'Scheduled'">
                                        <div class="space-y-1">
                                            <span class="inline-flex items-center gap-1 bg-purple-50 text-purple-700 border border-purple-100 px-2.5 py-1 rounded-xl text-[9px] font-black uppercase tracking-wider">
                                                <i class="fas fa-calendar-alt text-[8px]"></i> Scheduled
                                            </span>
                                            <p class="text-xs font-black text-purple-950 font-mono" x-text="o.scheduled_formatted"></p>
                                        </div>
                                    </template>
                                    <template x-if="o.type === 'Immediate'">
                                        <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 border border-blue-100 px-2.5 py-1 rounded-xl text-[9px] font-black uppercase tracking-wider">
                                            <i class="fas fa-bolt text-[8px] animate-pulse"></i> Immediate
                                        </span>
                                    </template>
                                </div>
                            </td>

                            <!-- Progress Status badge -->
                            <td class="px-8 py-5">
                                <span :class="{
                                    'bg-amber-50 text-amber-700 border-amber-200': o.status === 'Pending',
                                    'bg-sky-50 text-sky-700 border-sky-200': o.status === 'Confirmed',
                                    'bg-orange-50 text-orange-700 border-orange-200': o.status === 'Preparing',
                                    'bg-emerald-50 text-emerald-700 border-emerald-200': o.status === 'Ready',
                                    'bg-green-100 text-green-800 border-green-200': o.status === 'Completed',
                                    'bg-rose-50 text-rose-700 border-rose-200': o.status === 'Cancelled'
                                }" class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border" x-text="o.status">
                                </span>
                            </td>

                            <!-- Receipt & Tender -->
                            <td class="px-8 py-5 text-right font-mono">
                                <div class="space-y-1 block md:inline-block">
                                    <div class="text-sm font-black text-slate-900" x-text="o.amount_formatted"></div>
                                    <div class="flex items-center justify-end gap-1.5 select-none">
                                        <!-- GCash triggers -->
                                        <template x-if="o.method === 'GCash'">
                                            <div class="flex flex-col items-end gap-1">
                                                <span class="inline-flex items-center gap-1 text-[9px] font-black uppercase tracking-wider text-blue-600 bg-blue-50 border border-blue-100 rounded-lg px-2 py-0.5">
                                                    <i class="fab fa-google-pay"></i> GCash
                                                </span>
                                                <!-- Action link to verify if pending -->
                                                <template x-if="o.payment_status === 'Pending Verification'">
                                                    <button type="button" @click="
                                                        receiptUrl = o.proof_image;
                                                        receiptRef = o.reference_number;
                                                        verifyUrl = o.verify_url;
                                                        customerName = o.customer_name;
                                                        orderAmount = o.amount_formatted;
                                                        orderId = '#ORD-' + o.id;
                                                        showReceiptModal = true;
                                                    " class="text-[9px] font-black uppercase tracking-wider text-blue-700 underline hover:text-orange-500 cursor-pointer">
                                                        Verify proof
                                                    </button>
                                                </template>
                                                <template x-if="o.payment_status === 'Paid'">
                                                    <span class="text-[9px] font-black text-emerald-600 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded-lg flex items-center gap-1">
                                                        <i class="fas fa-check-circle"></i> Paid
                                                    </span>
                                                </template>
                                            </div>
                                        </template>

                                        <!-- Cash triggers -->
                                        <template x-if="o.method === 'Cash'">
                                            <div class="flex flex-col items-end gap-1">
                                                <span class="inline-flex items-center gap-1 text-[9px] font-black uppercase tracking-wider text-purple-600 bg-purple-50 border border-purple-100 rounded-lg px-2 py-0.5">
                                                    <i class="fas fa-money-bill-wave"></i> Cash
                                                </span>
                                                <template x-if="o.payment_status === 'Paid'">
                                                    <span class="text-[9px] font-black text-emerald-600 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded-lg flex items-center gap-1">
                                                        <i class="fas fa-check-double"></i> Settled
                                                    </span>
                                                </template>
                                                <template x-if="o.payment_status !== 'Paid'">
                                                    <span class="text-[9px] font-black text-amber-600 bg-amber-50 border border-amber-100 px-2 py-0.5 rounded-lg">
                                                        Collection
                                                    </span>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </td>

                            <!-- Management Inspect & Interactive Controls -->
                            <td class="px-8 py-5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="activeOrder = o; showInspectModal = true;"
                                        class="p-2.5 bg-slate-50 hover:bg-orange-50 text-slate-500 hover:text-orange-600 transition-colors rounded-xl border border-slate-100 cursor-pointer flex items-center justify-center shadow-sm"
                                        title="Inspect item lists & fast controls">
                                        <i class="fas fa-eye text-xs"></i>
                                    </button>

                                    @if(auth()->user()->role === 'admin')
                                        <form :action="o.delete_url" method="POST" class="inline" onsubmit="return confirm('Ensure absolute certainty to delete this order? All related payments and billing details will be flushed permanently.');">
                                            @csrf
                                            <button type="submit" class="p-2.5 bg-rose-50 hover:bg-rose-100 border border-rose-100 text-rose-600 rounded-xl cursor-pointer flex items-center justify-center shadow-sm" title="Delete Order permanently">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Quick Advance Controller -->
                                    <template x-if="o.status === 'Pending'">
                                        <form :action="o.status_url" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="status" value="Confirmed">
                                            <button type="submit" class="p-2.5 bg-emerald-50 hover:bg-emerald-100 border border-emerald-100 text-emerald-600 rounded-xl cursor-pointer flex items-center justify-center shadow-sm" title="Accept instantly">
                                                <i class="fas fa-check text-xs"></i>
                                            </button>
                                        </form>
                                    </template>
                                    <template x-if="o.status === 'Confirmed'">
                                        <form :action="o.status_url" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="status" value="Preparing">
                                            <button type="submit" class="p-2.5 bg-orange-50 hover:bg-orange-100 border border-orange-100 text-orange-600 rounded-xl cursor-pointer flex items-center justify-center shadow-sm" title="Mark as preparing">
                                                <i class="fas fa-fire-burner text-xs animate-pulse"></i>
                                            </button>
                                        </form>
                                    </template>
                                    <template x-if="o.status === 'Preparing'">
                                        <form :action="o.status_url" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="status" value="Ready">
                                            <button type="submit" class="p-2.5 bg-emerald-50 hover:bg-emerald-100 border border-emerald-100 text-emerald-600 rounded-xl cursor-pointer flex items-center justify-center shadow-sm" title="Mark as ready on counter">
                                                <i class="fas fa-bell text-xs"></i>
                                            </button>
                                        </form>
                                    </template>
                                    <template x-if="o.status === 'Ready'">
                                        <form :action="o.status_url" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="status" value="Completed">
                                            <button type="submit" class="p-2.5 bg-slate-900 hover:bg-emerald-600 border border-slate-950 text-white hover:border-transparent rounded-xl cursor-pointer flex items-center justify-center shadow-sm" title="Close and complete transaction">
                                                <i class="fas fa-handshake text-xs"></i>
                                            </button>
                                        </form>
                                    </template>
                                </div>
                            </td>

                        </tr>
                    </template>

                    <!-- Empty state -->
                    <tr x-show="filteredOrders.length === 0">
                        <td colspan="7" class="py-24 text-center">
                            <div class="bg-slate-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm border border-dashed border-slate-200 text-slate-300">
                                <i class="fas fa-receipt text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-black text-slate-900 uppercase tracking-wide">No matched logs</h3>
                            <p class="text-slate-400 font-bold text-xs mt-1">Refine filters or typing patterns to surface older transcripts</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 1. Spectacular Recipe/Order Detail inspection modal -->
    <div x-show="showInspectModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div @click.away="showInspectModal = false"
             class="bg-white rounded-[3rem] border border-slate-100 shadow-2xl overflow-hidden max-w-2xl w-full transform transition-all flex flex-col max-h-[90vh]"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <!-- Modal Header -->
            <div class="px-8 py-6 bg-slate-50 border-b flex items-center justify-between">
                <div>
                    <span class="text-[9px] font-black text-orange-600 uppercase tracking-widest block mb-0.5">Comprehensive Ticket Audit</span>
                    <h4 class="text-xl font-black text-slate-900 flex items-center gap-2" x-text="activeOrder ? '#ORD-ID-' + activeOrder.id : ''"></h4>
                </div>
                <button @click="showInspectModal = false" class="text-slate-400 hover:text-slate-950 transition-colors cursor-pointer w-9 h-9 rounded-xl bg-white border flex items-center justify-center shadow-xs">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <!-- Modal Content (Scrollable) -->
            <div class="p-8 space-y-6 overflow-y-auto flex-grow" x-show="activeOrder">
                <!-- Customer info & dates -->
                <div class="grid grid-cols-2 gap-4 bg-slate-50/50 p-5 rounded-2xl border text-xs leading-relaxed">
                    <div>
                        <p class="font-bold text-slate-400 uppercase text-[9px] tracking-wider mb-1">Customer Profile</p>
                        <p class="font-black text-slate-800" x-text="activeOrder ? activeOrder.customer_name : ''"></p>
                        <p class="text-slate-500 font-semibold lowercase" x-text="activeOrder ? activeOrder.customer_email : ''"></p>
                    </div>
                    <div>
                        <p class="font-bold text-slate-400 uppercase text-[9px] tracking-wider mb-1">Order Timing</p>
                        <p class="font-black text-slate-800" x-text="activeOrder ? activeOrder.date : ''"></p>
                        <p class="text-orange-600 font-bold" x-text="activeOrder ? activeOrder.date_human : ''"></p>
                    </div>
                </div>

                <!-- Fulfilment Specs -->
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-indigo-50/30 p-4 rounded-xl border border-indigo-100/30 text-center">
                        <span class="text-[8px] font-black text-indigo-400 uppercase tracking-wider block">Fulfillment Channel</span>
                        <span class="font-black text-indigo-700 text-xs block mt-1" x-text="activeOrder ? activeOrder.type : ''"></span>
                    </div>
                    <div class="bg-amber-50/30 p-4 rounded-xl border border-amber-100/30 text-center">
                        <span class="text-[8px] font-black text-amber-500 uppercase tracking-wider block">Scheduled Target</span>
                        <span class="font-black text-amber-700 text-xs block mt-1" x-text="activeOrder && activeOrder.scheduled ? activeOrder.scheduled_formatted : 'Immediate'"></span>
                    </div>
                    <div class="bg-purple-50/30 p-4 rounded-xl border border-purple-100/30 text-center">
                        <span class="text-[8px] font-black text-purple-400 uppercase tracking-wider block">Tender System</span>
                        <span class="font-black text-purple-700 text-xs block mt-1" x-text="activeOrder ? activeOrder.method : ''"></span>
                    </div>
                </div>

                <!-- Itemized tickets list -->
                <div class="space-y-4">
                    <h5 class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">Purchased culinary selection</h5>

                    <div class="space-y-3">
                        <template x-for="item in (activeOrder ? activeOrder.items : [])" :key="item.name">
                            <div class="flex items-start gap-3 bg-white p-4 border rounded-2xl shadow-xs">
                                <template x-if="item.image">
                                    <img :src="item.image" class="w-12 h-12 rounded-xl object-cover border shadow-sm shrink-0">
                                </template>
                                <div class="flex-grow min-w-0">
                                    <div class="flex items-center justify-between gap-1.5">
                                        <span class="text-sm font-black text-slate-800 truncate" x-text="item.name"></span>
                                        <span class="bg-slate-100 text-slate-800 text-[10px] font-black px-2 py-0.5 rounded-lg shrink-0" x-text="item.quantity + 'x'"></span>
                                    </div>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-0.5" x-text="item.category"></p>

                                    <!-- Special Prep rules -->
                                    <template x-if="item.instructions && item.instructions.trim() !== ''">
                                        <div class="mt-2 text-[10px] italic text-amber-800 bg-amber-50/50 px-2.5 py-1.5 border border-amber-100 rounded-lg font-bold flex items-start gap-1 pb-2 font-black leading-snug">
                                            <i class="fas fa-exclamation-circle text-amber-500 text-[9px] mt-0.5"></i>
                                            <span x-text="'&ldquo;' + item.instructions + '&rdquo;'"></span>
                                        </div>
                                    </template>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-xs font-semibold text-slate-400" x-text="item.price"></p>
                                    <p class="text-sm font-black text-slate-900 mt-0.5" x-text="item.subtotal"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Ticket value sum -->
                <div class="pt-4 border-t border-slate-50 flex items-center justify-between">
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider leading-none">Complete Gross Tender</p>
                        <p class="text-[9px] text-slate-400 font-bold">Inclusive of all dynamic sales options</p>
                    </div>
                    <p class="text-2xl font-black text-slate-900 font-mono" x-text="activeOrder ? activeOrder.amount_formatted : ''"></p>
                </div>
            </div>

            <!-- Modal Action Footer -->
            <div class="px-8 py-5 bg-slate-50 border-t flex flex-wrap items-center justify-end gap-3 shrink-0">
                @if(auth()->user()->role === 'admin')
                    <template x-if="activeOrder">
                        <form :action="activeOrder.delete_url" method="POST" class="inline" onsubmit="return confirm('Ensure absolute certainty to delete this order? All related payments and billing details will be flushed permanently.');">
                            @csrf
                            <button type="submit" class="px-5 py-3.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-black text-xs uppercase tracking-wider cursor-pointer shadow-sm flex items-center gap-1.5">
                                <i class="fas fa-trash-alt"></i> Delete Order
                            </button>
                        </form>
                    </template>
                @endif

                <button type="button" @click="showInspectModal = false" class="px-6 py-3.5 rounded-xl border bg-white hover:bg-slate-50 text-slate-500 font-black text-xs uppercase tracking-wider cursor-pointer">
                    Dismiss
                </button>

                <!-- Status Updater Form triggers inside Inspect Modal on activeOrder -->
                <template x-if="activeOrder">
                    <div class="flex items-center gap-2">
                        <!-- If Pending -->
                        <template x-if="activeOrder.status === 'Pending'">
                            <div class="flex items-center gap-2">
                                <form :action="activeOrder.status_url" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="Confirmed">
                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-3.5 rounded-xl font-black text-xs uppercase tracking-wider cursor-pointer shadow-sm">
                                        Accept Order
                                    </button>
                                </form>
                                <form :action="activeOrder.status_url" method="POST" class="inline" onsubmit="return confirm('Ensure absolute certainty to cancel this order?');">
                                    @csrf
                                    <input type="hidden" name="status" value="Cancelled">
                                    <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-600 px-5 py-3.5 rounded-xl font-black text-xs uppercase tracking-wider cursor-pointer border border-rose-100">
                                        Cancel Transaction
                                    </button>
                                </form>
                            </div>
                        </template>

                        <!-- If Confirmed -->
                        <template x-if="activeOrder.status === 'Confirmed'">
                            <form :action="activeOrder.status_url" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="Preparing">
                                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-3.5 rounded-xl font-black text-xs uppercase tracking-wider cursor-pointer shadow-sm">
                                    Commence Prep Work
                                </button>
                            </form>
                        </template>

                        <!-- If Preparing -->
                        <template x-if="activeOrder.status === 'Preparing'">
                            <form :action="activeOrder.status_url" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="Ready">
                                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-3.5 rounded-xl font-black text-xs uppercase tracking-wider cursor-pointer shadow-sm">
                                    Complete Recipe Prep
                                </button>
                            </form>
                        </template>

                        <!-- If Ready -->
                        <template x-if="activeOrder.status === 'Ready'">
                            <form :action="activeOrder.status_url" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="Completed">
                                <button type="submit" class="bg-slate-900 hover:bg-emerald-600 text-white px-5 py-3.5 rounded-xl font-black text-xs uppercase tracking-wider cursor-pointer shadow-sm">
                                    Finalize Collection / Deliver
                                </button>
                            </form>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- 2. Spectacular dynamic GCash receipt verification lightbox modal -->
    <div x-show="showReceiptModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md animate-in fade-in duration-200">

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
                        <div class="w-18 h-18 bg-amber-50 rounded-full flex items-center justify-center border-2 border-dashed border-amber-200 animate-pulse text-amber-500">
                            <i class="fas fa-image text-2xl"></i>
                        </div>
                        <div>
                            <p class="font-black text-slate-800 text-base uppercase tracking-wider">No Image Attachment</p>
                            <p class="text-slate-400 font-bold text-xs mt-1">Order submitted reference number only. Please manually verify against GCash account ledger records.</p>
                        </div>
                    </div>
                </template>
                <div class="mt-4 w-full bg-slate-100 rounded-2xl py-3 px-4 text-center">
                    <p class="text-[10px] font-black tracking-widest text-slate-400 uppercase">Verification Stream</p>
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
                        <span class="text-xs font-black text-blue-500 uppercase tracking-widest block mb-1">GCash Evidence Audit</span>
                        <h3 class="text-3xl font-black text-slate-900 tracking-tight" x-text="orderId"></h3>
                    </div>
                    <button type="button" @click="showReceiptModal = false" class="p-3 bg-slate-50 hover:bg-slate-100 text-slate-400 hover:text-slate-705 rounded-2xl transition-colors cursor-pointer">
                        <i class="fas fa-times text-xs"></i>
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
                        <span class="text-[10px] text-blue-505 font-bold block pt-1 leading-relaxed">* Cross-confirm with GCash dashboard before checking verified.</span>
                    </div>
                </div>

                <!-- Action form -->
                <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3 w-full">
                    <button type="button" @click="showReceiptModal = false" class="px-6 py-4 rounded-2xl text-slate-500 hover:bg-slate-50 text-sm font-black transition-colors">
                        Close Log
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

<!-- jsPDF and AutoTable plugins for client-side PDF Generation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('orderLogComponent', () => ({
        search: '',
        statusFilter: 'all',
        typeFilter: 'all',
        paymentStatusFilter: 'all',

        // Receipt Lightbox State
        showReceiptModal: false,
        receiptUrl: '',
        receiptRef: '',
        verifyUrl: '',
        customerName: '',
        orderAmount: '',
        orderId: '',

        // Detailed Inspect Modal State
        showInspectModal: false,
        activeOrder: null,

        // Orders from PHP formatted array
        orders: window.initialOrdersLog || [],

        // Computed filtered orders
        get filteredOrders() {
            return this.orders.filter(o => {
                const searchLower = this.search.toLowerCase().trim();
                const matchesSearch = !searchLower ||
                                      (o.id || '').toString().includes(searchLower) ||
                                      (o.customer_name || '').toLowerCase().includes(searchLower) ||
                                      (o.customer_email || '').toLowerCase().includes(searchLower) ||
                                      (o.reference_number || '').toLowerCase().includes(searchLower);

                const matchesStatus = this.statusFilter === 'all' || o.status === this.statusFilter;
                const matchesType = this.typeFilter === 'all' || o.type === this.typeFilter;
                const matchesPayment = this.paymentStatusFilter === 'all' || o.payment_status === this.paymentStatusFilter;

                return matchesSearch && matchesStatus && matchesType && matchesPayment;
            });
        },

        // PDF Activity Report Export Logic
        exportPDF() {
            if (!window.jspdf) {
                alert('Export libraries are still loading. Please try again in a moment!');
                return;
            }

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'mm', 'a4'); // Landscape fits the columns perfectly

            // Professional Colors
            const darkSlate = [15, 23, 42];      // Slate-900
            const softOrange = [249, 115, 22];    // Orange-500

            // 1. Decorative Header Banner
            doc.setFillColor(15, 23, 42); // Slate-900 background
            doc.rect(0, 0, 297, 34, 'F');

            // Draw orange top highlight line
            doc.setFillColor(249, 115, 22);
            doc.rect(0, 0, 297, 3, 'F');

            // Header Title
            doc.setTextColor(255, 255, 255);
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(20);
            doc.text("CLARA’S BEAST", 14, 16);
            doc.setFontSize(9);
            doc.setFont('helvetica', 'normal');
            doc.setTextColor(249, 115, 22);
            doc.text("PREMIUM FOOD ORDERING AND MANAGEMENT PORTAL", 14, 21);
            doc.setTextColor(156, 163, 175);
            doc.text("HISTORICAL LOGS & TRANSACTION AUDIT REPORT", 14, 26);

            // Report Meta Information on the right
            doc.setTextColor(255, 255, 255);
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(9);
            doc.text("REPORT GENERATED:", 210, 14);
            doc.setFont('helvetica', 'normal');
            doc.setTextColor(209, 213, 219);
            doc.text(new Date().toLocaleString('en-US', { hour12: true }), 210, 18);

            const totalFilteredCount = this.filteredOrders.length;
            const totalFilteredAmount = this.filteredOrders.reduce((acc, curr) => acc + parseFloat(curr.amount || 0), 0);

            doc.setTextColor(255, 255, 255);
            doc.setFont('helvetica', 'bold');
            doc.text("FILTER DETAILS:", 210, 24);
            doc.setFont('helvetica', 'normal');
            doc.setTextColor(249, 115, 22);
            doc.text(`${totalFilteredCount} Orders (Total: PHP ${totalFilteredAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})})`, 210, 28);

            // 2. Metrics Summary Boxes (below header)
            doc.setFillColor(248, 250, 252); // Soft light blue-slate back plate
            doc.roundedRect(14, 38, 269, 16, 3, 3, 'F');

            doc.setTextColor(100, 116, 139);
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(8);
            doc.text("RECORD COUNT", 20, 44);
            doc.text("TOTAL VOLUME SPENT", 100, 44);
            doc.text("AUDIT SECURE INDEX", 200, 44);

            doc.setTextColor(15, 23, 42);
            doc.setFontSize(10);
            doc.text(`${totalFilteredCount} Orders matching filters`, 20, 49);
            doc.setTextColor(249, 115, 22);
            doc.text(`PHP ${totalFilteredAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`, 100, 49);
            doc.setTextColor(16, 185, 129); // Green
            doc.text("VERIFIED SECURE", 200, 49);

            // Table of Orders using AutoTable
            const tableRows = this.filteredOrders.map(o => {
                const itemsStr = o.items.map(i => `${i.name} (x${i.quantity})`).join('\n');
                return [
                    '#ORD-' + o.id,
                    o.date,
                    `${o.customer_name}\n(${o.customer_email})`,
                    o.type.toUpperCase(),
                    o.status.toUpperCase(),
                    `${o.method.toUpperCase()}\n(${o.payment_status.toUpperCase()})`,
                    `PHP ${parseFloat(o.amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`,
                    itemsStr
                ];
            });

            doc.autoTable({
                startY: 59,
                margin: { left: 14, right: 14 },
                head: [['Order ID', 'Date & Time', 'Customer Info', 'Type', 'Order Status', 'Payment Details', 'Amount', 'Itemized Items']],
                body: tableRows,
                theme: 'grid',
                styles: {
                    fontSize: 7.5,
                    cellPadding: 3,
                    font: "helvetica",
                    textColor: [51, 65, 85], // Slate-700
                    lineColor: [226, 232, 240], // Light gray border
                    lineWidth: 0.1
                },
                headStyles: {
                    fillColor: [15, 23, 42], // Dark Slate Header
                    textColor: [255, 255, 255],
                    fontStyle: 'bold',
                    fontSize: 8,
                    halign: 'left'
                },
                columnStyles: {
                    0: { cellWidth: 20, fontStyle: 'bold', textColor: [15, 23, 42] }, // Order ID
                    1: { cellWidth: 28 }, // Date
                    2: { cellWidth: 42 }, // Customer
                    3: { cellWidth: 18, halign: 'center' }, // Type
                    4: { cellWidth: 22, fontStyle: 'bold', halign: 'center' }, // Status
                    5: { cellWidth: 32 }, // Payment
                    6: { cellWidth: 26, fontStyle: 'bold', textColor: [194, 65, 12], halign: 'right' }, // Amount (deep orange/red font)
                    7: { cellWidth: 81 } // Items
                },
                alternateRowStyles: {
                    fillColor: [248, 250, 252] // Cool slate background pattern
                },
                willDrawCell: function (data) {
                    if (data.section === 'body' && data.column.index === 4) {
                        const statusVal = data.cell.raw || '';
                        if (statusVal.includes('COMPLETED') || statusVal.includes('DELIVERED')) {
                            data.cell.styles.textColor = [16, 185, 129]; // Emerald Green
                        } else if (statusVal.includes('PENDING')) {
                            data.cell.styles.textColor = [245, 158, 11]; // Amber Gold
                        } else if (statusVal.includes('CANCELLED')) {
                            data.cell.styles.textColor = [239, 68, 68]; // Crimson Red
                        }
                    }
                },
                didDrawPage: function (data) {
                    const pageCount = doc.internal.getNumberOfPages();
                    doc.setFontSize(7.5);
                    doc.setFont('helvetica', 'normal');
                    doc.setTextColor(148, 163, 184); // Slate-400

                    // Left align: security token or system name
                    doc.text("CLARA’S BEAST SECURE REPORT ENGINE", 14, 203);

                    // Right align: Page X of Y
                    const footerStr = `Page ${data.pageNumber} of ${pageCount}`;
                    doc.text(footerStr, 297 - 14 - doc.getTextWidth(footerStr), 203);
                }
            });

            const dateStr = new Date().toISOString().split('T')[0];
            doc.save(`claras_beast_order_report_${dateStr}.pdf`);
        }
    }));
});
</script>
@endsection
