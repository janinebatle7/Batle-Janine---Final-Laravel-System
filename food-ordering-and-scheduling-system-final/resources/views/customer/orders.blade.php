@extends('layouts.app')

@section('title', 'My Order History')

@section('content')
@php
    $formattedOrders = $orders->map(function($order) {
        return [
            'id' => (string)$order->id,
            'status' => (string)$order->status,
            'type' => (string)$order->order_type,
            'scheduled' => $order->scheduled_datetime ? \Carbon\Carbon::parse($order->scheduled_datetime)->format("Y-m-d H:i") : "",
            'scheduled_formatted' => $order->scheduled_datetime ? \Carbon\Carbon::parse($order->scheduled_datetime)->format("h:i A") : "N/A",
            'method' => (string)$order->payment_method,
            'payment_status' => (string)$order->payment_status,
            'amount' => (float)$order->total_amount,
            'amount_formatted' => '₱' . number_format($order->total_amount, 2),
            'date' => $order->created_at->format("M d, Y h:i A"),
            'date_human' => $order->created_at->diffForHumans(),
            'proof_image' => $order->payment?->proof_image ?? "",
            'reference_number' => $order->payment?->reference_number ?? "",
            'cancel_url' => route('orders.cancel', $order->id),
            'delete_url' => route('orders.delete', $order->id),
            'items' => $order->details->map(function($detail) {
                return [
                    'name' => $detail->menuItem ? (string)$detail->menuItem->name : 'Deleted Item',
                    'image' => $detail->menuItem ? (string)$detail->menuItem->image : '',
                    'quantity' => (int)$detail->quantity,
                    'price' => '₱' . number_format($detail->menuItem ? $detail->menuItem->price : 0, 2),
                    'instructions' => $detail->special_instructions ?? '',
                    'subtotal' => '₱' . number_format($detail->subtotal, 2)
                ];
            })->toArray()
        ];
    })->toArray();
@endphp

<script>
    window.initialCustomerOrders = {!! json_encode($formattedOrders) !!};
</script>

<div x-data="customerOrdersComponent()" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12 pb-24 animate-in fade-in duration-300">

    <!-- Header Section banner -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 pb-6 border-b border-slate-100">
        <div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                My Feast Trail
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-orange-50 text-orange-600 border border-orange-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500 mr-2 animate-pulse"></span>
                    Order Pipeline
                </span>
            </h1>
            <p class="text-slate-500 font-bold mt-1">Track culinary preparation state, inspect GCash clears, and view standard recipe presentations</p>
        </div>
        <a href="{{ route('menu') }}" class="bg-slate-950 hover:bg-orange-600 text-white px-8 py-4 rounded-3xl font-black text-xs uppercase tracking-wider flex items-center justify-center gap-3 shadow-lg hover:shadow-orange-100 transition-all active:scale-95 shrink-0 self-start md:self-auto cursor-pointer">
            <i class="fas fa-utensils text-sm text-orange-400"></i> Browse Premium Menu
        </a>
    </div>

    <!-- Multi-Aspect Statistics Dashboard -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 select-none">
        <!-- Metric 1: Total Orders Placed -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 flex items-center gap-5 shadow-sm">
            <div class="w-12 h-12 rounded-2xl bg-orange-50 border border-orange-100 flex items-center justify-center text-orange-600 shrink-0">
                <i class="fas fa-shopping-bag text-lg"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Total Feasts</p>
                <p class="text-2xl font-black text-slate-950 mt-1" x-text="orders.length"></p>
            </div>
        </div>

        <!-- Metric 2: Completed orders -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 flex items-center gap-5 shadow-sm">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                <i class="fas fa-check-circle text-lg"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Success Feasts</p>
                <p class="text-2xl font-black text-slate-950 mt-1" x-text="orders.filter(o => o.status === 'Completed').length"></p>
            </div>
        </div>

        <!-- Metric 3: Orders in Prep state -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 flex items-center gap-5 shadow-sm">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shrink-0 animate-pulse">
                <i class="fas fa-fire-burner text-lg"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Active Kitchens</p>
                <p class="text-2xl font-black text-slate-950 mt-1" x-text="orders.filter(o => ['Confirmed', 'Preparing', 'Ready'].includes(o.status)).length"></p>
            </div>
        </div>

        <!-- Metric 4: Total Spent -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 flex items-center gap-5 shadow-sm">
            <div class="w-12 h-12 rounded-2xl bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600 shrink-0">
                <i class="fas fa-wallet text-lg"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Total Invested</p>
                <p class="text-2xl font-black text-slate-950 mt-1">
                    ₱<span x-text="orders.reduce((sum, o) => sum + o.amount, 0).toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2})"></span>
                </p>
            </div>
        </div>
    </div>

    <!-- Active Search Filtering Area -->
    <div class="bg-slate-50 border rounded-[2.5rem] p-6 flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="relative w-full md:max-w-md">
            <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" x-model="search"
                placeholder="Search meals, order ID ref, GCash codes..."
                class="w-full bg-white pl-11 pr-4 py-4 rounded-2xl border border-slate-200 text-sm font-bold placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-orange-100 focus:border-orange-500 transition-all">
            <button x-show="search.length > 0" @click="search = ''" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600">
                <i class="fas fa-times-circle text-sm"></i>
            </button>
        </div>

        <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto pb-1 md:pb-0 scrollbar-none">
            <button @click="statusFilter = 'all'"
                :class="statusFilter === 'all' ? 'bg-slate-950 text-white font-black shadow-md' : 'bg-white text-slate-600 hover:text-slate-900 border'"
                class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer">
                All Feasts
            </button>
            <button @click="statusFilter = 'Pending'"
                :class="statusFilter === 'Pending' ? 'bg-amber-500 text-white font-black shadow-md' : 'bg-white text-slate-600 hover:text-slate-900 border'"
                class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer">
                Pending Acceptance
            </button>
            <button @click="statusFilter = 'Preparing'"
                :class="statusFilter === 'Preparing' ? 'bg-orange-600 text-white font-black shadow-md' : 'bg-white text-slate-600 hover:text-slate-900 border'"
                class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer">
                Preparing
            </button>
            <button @click="statusFilter = 'Ready'"
                :class="statusFilter === 'Ready' ? 'bg-emerald-600 text-white font-black shadow-md' : 'bg-white text-slate-600 hover:text-slate-900 border'"
                class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer">
                Collection Counter
            </button>
            <button @click="statusFilter = 'Completed'"
                :class="statusFilter === 'Completed' ? 'bg-green-700 text-white font-black shadow-md' : 'bg-white text-slate-600 hover:text-slate-900 border'"
                class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer">
                Settled / Closed
            </button>
        </div>
    </div>

    <!-- Primary Order Activity Feed list -->
    <div class="space-y-10">
        <template x-for="o in filteredOrders" :key="o.id">
            <div class="bg-white rounded-[2.5rem] border border-slate-100 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300">

                <!-- Ticket Header Info block -->
                <div class="p-6 md:p-8 bg-slate-50/60 border-b border-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-slate-900 text-white rounded-2xl flex items-center justify-center font-black flex-shrink-0 text-sm shadow-md font-mono select-none">
                            #ID
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-black tracking-widest text-orange-600 uppercase">CULINARY BILLING ID</span>
                                <template x-if="o.type === 'Scheduled'">
                                    <span class="bg-purple-100 text-purple-700 text-[8px] font-black px-2 py-0.5 rounded uppercase tracking-wider">
                                        Scheduled Takeaway
                                    </span>
                                </template>
                            </div>
                            <h3 class="text-xl font-black text-slate-800 tracking-tight font-mono" x-text="'ORD-' + o.id"></h3>
                            <p class="text-xs text-slate-400 font-bold mt-0.5" x-text="o.date + ' • ' + o.date_human"></p>
                        </div>
                    </div>

                    <!-- Fulfillment Specs / GCash details -->
                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Scheduled execution hours display -->
                        <template x-if="o.type === 'Scheduled'">
                            <div class="bg-purple-50 text-purple-700 border border-purple-100 rounded-2xl py-2 px-4 text-xs font-black text-center flex items-center gap-2 select-none">
                                <i class="fas fa-calendar-alt"></i>
                                <span x-text="'Pickup Target: ' + o.scheduled_formatted"></span>
                            </div>
                        </template>

                        <!-- GCash confirmation label -->
                        <template x-if="o.method === 'GCash'">
                            <div class="bg-blue-50 text-blue-700 border border-blue-100 rounded-2xl py-2 px-4 text-xs font-black flex items-center gap-2 select-none">
                                <i class="fab fa-google-pay text-base"></i>
                                <span x-text="'GCash Ref: ' + (o.reference_number ? o.reference_number : 'None Provided')"></span>
                            </div>
                        </template>

                        <!-- Final Value -->
                        <div class="bg-white border rounded-2xl py-2 px-5 text-right flex flex-col select-none">
                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none">Gross Bill</span>
                            <span class="text-lg font-black text-slate-950 mt-0.5" x-text="o.amount_formatted"></span>
                        </div>
                    </div>
                </div>

                <!-- Custom Multi-Phase Pipeline Status Progression Tracker -->
                <div class="px-6 md:px-12 py-8 bg-white border-b border-slate-50 select-none">
                    <div class="relative">
                        <!-- Connecting Line background block -->
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="w-full h-1 bg-slate-100 rounded-full"></div>
                        </div>

                        <!-- Progress Highlight bar matching states -->
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="h-1 bg-gradient-to-r from-orange-500 to-emerald-500 rounded-full transition-all duration-500"
                                 :style="{
                                     width: o.status === 'Pending' ? '12%' :
                                            o.status === 'Confirmed' ? '37%' :
                                            o.status === 'Preparing' ? '62%' :
                                            o.status === 'Ready' ? '87%' :
                                            o.status === 'Completed' ? '100%' : '0%'
                                 }">
                            </div>
                        </div>

                        <!-- Node bullet anchors -->
                        <div class="relative flex justify-between items-center w-full">
                            <!-- Bullet 1: Pending -->
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center text-xs font-black transition-all"
                                     :class="['Pending', 'Confirmed', 'Preparing', 'Ready', 'Completed'].includes(o.status) ?
                                             'bg-orange-600 border-orange-600 text-white ring-4 ring-orange-50' :
                                             'bg-white border-slate-200 text-slate-400'">
                                    <i class="fas" :class="o.status !== 'Pending' ? 'fa-check text-[10px]' : 'fa-hourglass-start'"></i>
                                </div>
                                <span class="text-[9px] font-black uppercase tracking-wider mt-2.5"
                                      :class="['Pending', 'Confirmed', 'Preparing', 'Ready', 'Completed'].includes(o.status) ? 'text-slate-800' : 'text-slate-400'">
                                    Submitted
                                </span>
                            </div>

                            <!-- Bullet 2: Confirmed -->
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center text-xs font-black transition-all"
                                     :class="['Confirmed', 'Preparing', 'Ready', 'Completed'].includes(o.status) ?
                                             'bg-orange-600 border-orange-600 text-white ring-4 ring-orange-50' :
                                             'bg-white border-slate-200 text-slate-400'">
                                    <i class="fas" :class="['Preparing', 'Ready', 'Completed'].includes(o.status) ? 'fa-check text-[10px]' : 'fa-check-double'"></i>
                                </div>
                                <span class="text-[9px] font-black uppercase tracking-wider mt-2.5"
                                      :class="['Confirmed', 'Preparing', 'Ready', 'Completed'].includes(o.status) ? 'text-slate-800' : 'text-slate-400'">
                                    Accepted
                                </span>
                            </div>

                            <!-- Bullet 3: Preparing -->
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center text-xs font-black transition-all"
                                     :class="['Preparing', 'Ready', 'Completed'].includes(o.status) ?
                                             'bg-orange-500 border-orange-500 text-white ring-4 ring-orange-50' :
                                             'bg-white border-slate-200 text-slate-400'">
                                    <i class="fas" :class="['Ready', 'Completed'].includes(o.status) ? 'fa-check text-[10px]' : 'fa-fire-burner animate-pulse'"></i>
                                </div>
                                <span class="text-[9px] font-black uppercase tracking-wider mt-2.5"
                                      :class="['Preparing', 'Ready', 'Completed'].includes(o.status) ? 'text-slate-800' : 'text-slate-400'">
                                    Preparing
                                </span>
                            </div>

                            <!-- Bullet 4: Ready -->
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center text-xs font-black transition-all"
                                     :class="['Ready', 'Completed'].includes(o.status) ?
                                             'bg-emerald-500 border-emerald-500 text-white ring-4 ring-emerald-50' :
                                             'bg-white border-slate-200 text-slate-400'">
                                    <i class="fas" :class="o.status === 'Completed' ? 'fa-check text-[10px]' : 'fa-bell text-xs'"></i>
                                </div>
                                <span class="text-[9px] font-black uppercase tracking-wider mt-2.5"
                                      :class="['Ready', 'Completed'].includes(o.status) ? 'text-slate-800' : 'text-slate-400'">
                                    Ready
                                </span>
                            </div>

                            <!-- Bullet 5: Completed -->
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center text-xs font-black transition-all"
                                     :class="o.status === 'Completed' ?
                                             'bg-green-700 border-green-700 text-white ring-4 ring-green-100' :
                                             'bg-white border-slate-200 text-slate-400'">
                                    <i class="fas fa-handshake"></i>
                                </div>
                                <span class="text-[9px] font-black uppercase tracking-wider mt-2.5"
                                      :class="o.status === 'Completed' ? 'text-green-700 font-extrabold' : 'text-slate-400'">
                                    Completed
                                </span>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Culinary Itemized list inside Card (Professional Layout with item images) -->
                <div class="p-6 md:p-8 space-y-4">
                    <p class="text-[10px] font-black tracking-widest text-slate-400 uppercase ml-1">Purchased Culinary Selections</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <template x-for="item in o.items" :key="item.name">
                            <div class="bg-slate-50/50 p-4 rounded-3xl border border-slate-100 flex gap-4 items-start relative hover:bg-slate-50 transition-colors group">
                                <!-- High fidelity Recipe Presentation preview thumbnail -->
                                <div class="w-16 h-16 rounded-2xl overflow-hidden bg-slate-200 border border-slate-100 shadow-sm relative flex-shrink-0 select-none group">
                                    <img :src="item.image" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    <!-- Lightbox preview trigger search overlay -->
                                    <button type="button" @click="
                                        lightboxUrl = item.image;
                                        lightboxProductName = item.name;
                                        lightboxOpen = true;
                                    " class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white cursor-pointer" title="Zoom Standard Reference Image">
                                        <i class="fas fa-search-plus text-xs"></i>
                                    </button>
                                </div>

                                <!-- Text specifics -->
                                <div class="flex-grow min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <h4 class="font-extrabold text-slate-900 text-sm truncate" x-text="item.name"></h4>
                                        <span class="bg-white text-slate-800 text-[10px] font-black px-2.5 py-0.5 rounded-lg border shadow-xs" x-text="'x' + item.quantity"></span>
                                    </div>
                                    <div class="flex items-baseline gap-2 mt-0.5">
                                        <span class="text-[10px] text-slate-450 font-semibold" x-text="item.price + ' each'"></span>
                                        <span class="text-xs font-black text-slate-900 font-mono" x-text="'₱' + parseFloat(item.subtotal.replace(/[^0-9.]/g, '')).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                                    </div>

                                    <!-- Special prep criteria -->
                                    <template x-if="item.instructions && item.instructions.trim() !== ''">
                                        <div class="mt-2 text-[10px] italic text-amber-800 bg-amber-50/50 px-2 py-1 rounded-xl font-bold flex items-start gap-1 pb-2 font-black leading-snug">
                                            <i class="fas fa-exclamation-circle text-amber-500 text-[9px] mt-0.5"></i>
                                            <span x-text="'&ldquo;' + item.instructions + '&rdquo;'"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Footer details explaining actions/payment clearance -->
                <div class="px-6 py-4 bg-slate-50/40 border-t border-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 text-xs font-bold select-none">
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center text-[10px] font-black uppercase tracking-wider">
                                <span class="w-2 h-2 rounded-full mr-1.5"
                                      :class="{
                                          'bg-amber-400': o.payment_status === 'Unpaid',
                                          'bg-blue-400': o.payment_status === 'Pending Verification',
                                          'bg-emerald-500': o.payment_status === 'Paid',
                                          'bg-rose-500': o.payment_status === 'Refunded',
                                          'bg-dashed border bg-rose-200': o.payment_status === 'Failed'
                                      }"></span>
                                <span class="text-slate-500">Tender clearance: </span>
                                <span class="ml-1 text-slate-900" x-text="o.payment_status"></span>
                            </span>
                        </div>

                        <div class="text-[10px] font-black uppercase text-slate-400 tracking-wider">
                            <span class="text-slate-400">Tender Protocol:</span> <span class="bg-white border px-2 py-1 rounded-lg text-slate-700 ml-1" x-text="o.method"></span>
                        </div>
                    </div>

                    <!-- Interactive Action Form buttons -->
                    <div class="flex items-center gap-2 self-end sm:self-auto">
                        <!-- CANCEL BUTTON: Visible if 'Pending' or 'Confirmed' -->
                        <template x-if="['Pending', 'Confirmed'].includes(o.status)">
                            <form :action="o.cancel_url" method="POST" class="inline m-0" onsubmit="return confirm('Do you really want to cancel this order? This will release your booking reservation.');">
                                @csrf
                                <button type="submit" class="bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white border border-rose-100 hover:border-transparent px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all cursor-pointer flex items-center gap-1.5 shadow-sm active:scale-95">
                                    <i class="fas fa-ban"></i> Cancel Order
                                </button>
                            </form>
                        </template>

                        <!-- DELETE BUTTON: Visible if not actively under kitchen preparation -->
                        <template x-if="!['Confirmed', 'Preparing', 'Ready'].includes(o.status)">
                            <form :action="o.delete_url" method="POST" class="inline m-0" onsubmit="return confirm('Are you sure you want to permanently delete this order record from your trail? This action cannot be undone.');">
                                @csrf
                                <button type="submit" class="bg-slate-100 hover:bg-red-600 text-slate-600 hover:text-white border border-slate-200 hover:border-transparent px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all cursor-pointer flex items-center gap-1.5 shadow-sm active:scale-95">
                                    <i class="fas fa-trash-alt"></i> Delete Order
                                </button>
                            </form>
                        </template>
                    </div>
                </div>

            </div>
        </template>

        <!-- Empty state placeholder -->
        <div x-show="filteredOrders.length === 0" class="col-span-full py-24 text-center bg-white rounded-[3rem] border border-slate-100 shadow-sm">
            <div class="bg-slate-50 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xs border text-slate-200">
                <i class="fas fa-receipt text-3xl"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-900 uppercase tracking-wide">No recipes found</h3>
            <p class="text-slate-500 font-bold text-xs mt-1">Ready to satisfying primal cravings? Browse our current catalog of delicacies!</p>
            <div class="pt-6">
                <a href="{{ route('menu') }}" class="inline-flex items-center gap-2 bg-slate-950 text-white px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-wider hover:bg-orange-600 transition-all select-none">
                    Discover Culinary Menu
                </a>
            </div>
        </div>
    </div>

    <!-- Universal Immersive Lightbox Modal for Item Recipes & Plating Presentation of menu_item -->
    <div x-show="lightboxOpen"
         x-cloak
         class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md"
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
                    <span class="text-[9px] font-black text-orange-600 uppercase tracking-widest block mb-0.5">Presentation Reference</span>
                    <h4 class="text-sm font-black text-slate-900" x-text="lightboxProductName"></h4>
                </div>
                <button @click="lightboxOpen = false" class="text-slate-400 hover:text-slate-900 transition-colors cursor-pointer w-8 h-8 rounded-xl bg-white border flex items-center justify-center">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-6 flex flex-col items-center">
                <img :src="lightboxUrl" class="max-h-[385px] w-full rounded-2xl object-cover border shadow-md" alt="Dynamic Presentation Representation">
                <p class="text-slate-505 text-xs font-bold text-center mt-4 flex items-center gap-1">
                    <i class="fas fa-info-circle text-orange-500"></i> Standard chef-plated culinary layout guide.
                </p>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('customerOrdersComponent', () => ({
        search: '',
        statusFilter: 'all',
        lightboxOpen: false,
        lightboxUrl: '',
        lightboxProductName: '',

        orders: window.initialCustomerOrders || [],

        get filteredOrders() {
            return this.orders.filter(o => {
                const searchLower = this.search.toLowerCase().trim();
                const matchesSearch = !searchLower ||
                                      (o.id || '').toString().includes(searchLower) ||
                                      (o.reference_number || '').toLowerCase().includes(searchLower) ||
                                      (o.items || []).some(i => (i.name || '').toLowerCase().includes(searchLower));

                const matchesStatus = this.statusFilter === 'all' || o.status === this.statusFilter;

                return matchesSearch && matchesStatus;
            });
        }
    }));
});
</script>
@endsection
