@extends('layouts.app')

@section('content')
@php
    $openHours = \App\Http\Controllers\AdminDashboardController::getOpenHoursSettings();
    $todayName = \Carbon\Carbon::now('Asia/Manila')->format('l');
    $todayConfig = $openHours['days'][$todayName] ?? ['is_open' => true, 'open_time' => '09:00', 'close_time' => '21:00'];

    if ($openHours['always_open']) {
        $displayText = 'Always Open (24/7)';
    } elseif ($openHours['override_closed']) {
        $displayText = 'Temporarily Closed';
    } elseif (!($todayConfig['is_open'] ?? true)) {
        $displayText = 'Closed Today (' . $todayName . ')';
    } else {
        $openTimeStr = $todayConfig['open_time'] ?? '09:00';
        $closeTimeStr = $todayConfig['close_time'] ?? '21:00';

        try {
            $openFormatted = \Carbon\Carbon::createFromFormat('H:i', $openTimeStr)->format('g:i A');
            $closeFormatted = \Carbon\Carbon::createFromFormat('H:i', $closeTimeStr)->format('g:i A');
            $displayText = $openFormatted . ' - ' . $closeFormatted;
        } catch (\Exception $e) {
            $displayText = '9:00 AM - 9:00 PM';
        }
    }

    // Format all menu items for Alpine client-side live engine
    $formattedItems = $items->map(function($item) {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'price' => (float)$item->price,
            'category' => $item->category,
            'image' => $item->image,
            'description' => $item->description,
            'availability_status' => (bool)$item->availability_status,
            'add_url' => route('cart.add', $item->id)
        ];
    })->values()->toArray();
@endphp

<!-- Shared data declaration -->
<script>
    window.initialMenuItems = {!! json_encode($formattedItems) !!};
    window.isStoreForceClosed = {{ ($openHours['override_closed'] ?? false) ? 'true' : 'false' }};
</script>

<div x-data="customerMenuComponent()" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12 pb-24 animate-in fade-in duration-300">

    <!-- Hero Section -->
    <div class="flex flex-col lg:flex-row items-center justify-between gap-12 py-8 md:py-12 border-b border-slate-100">
        <div class="max-w-xl space-y-6 text-left">
            <div class="inline-flex items-center gap-2 bg-orange-50 border border-orange-100/60 px-4 py-2 rounded-full text-[10px] font-black uppercase text-orange-600 tracking-wider shadow-sm">
                <i class="fas fa-fire-alt animate-bounce"></i> Uncompromising Quality
            </div>
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-black text-slate-900 leading-[1.15] tracking-tight">
                Satisfy Your <br>
                <span class="text-orange-600 relative inline-block">
                    Primal Cravings
                    <span class="absolute left-0 bottom-1 w-full h-[6px] bg-orange-200/60 rounded -z-10"></span>
                </span>
            </h1>
            <p class="text-base sm:text-lg text-slate-500 font-bold leading-relaxed">
                Premium best Puto, handcrafted for those who live wild. Order now for immediate pickup or schedule your next gourmet feast.
            </p>
            <div class="flex flex-wrap items-center gap-4 pt-2">
                <a href="#menu-catalog" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-wider shadow-xl shadow-slate-950/10 hover:bg-orange-600 active:scale-95 transition-all text-center">
                    Explore Feast Menu
                </a>

                <div class="flex items-center gap-3 bg-white px-6 py-3.5 rounded-2xl border border-slate-100 shadow-sm">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 border border-orange-100/30 flex items-center justify-center">
                        <i class="fas fa-clock text-orange-600 text-base"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase text-slate-400 tracking-wider">Open Operating Hours</p>
                        <p class="text-xs font-black text-slate-900 leading-none mt-0.5">{{ $displayText }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="relative w-full max-w-md aspect-square rounded-[2.5rem] md:rounded-[3.5rem] bg-orange-50 overflow-hidden shadow-2xl border-[12px] border-white ring-1 ring-slate-100 shrink-0">
            <img src="https://deliciouslyrushed.com/wp-content/uploads/2024/06/Puto-720x720.jpg" class="w-full h-full object-cover">
            <div class="absolute bottom-6 right-6 bg-slate-900/90 backdrop-blur-md px-5 py-3 rounded-2xl shadow-xl flex items-center gap-3 border border-slate-800">
                <div class="text-right">
                    <p class="text-[8px] font-black uppercase text-orange-400 tracking-widest leading-none">Easy Steamed Rice Cakes</p>
                    <p class="text-xs font-extrabold text-white mt-1">Filipino Puto</p>
                </div>
                <div class="h-6 w-[1px] bg-slate-700"></div>
                <span class="text-xs font-black text-orange-500">P350</span>
            </div>
        </div>
    </div>

    <!-- Sticky Filters & Search Section -->
    <div id="menu-catalog" class="pt-2">
        <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-6 bg-white p-5 rounded-[2rem] border border-slate-100 shadow-sm">

            <!-- Horizontal Scrolling Categories -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2 lg:pb-0 no-scrollbar" style="scrollbar-width: none;">
                <button
                    @click="activeCategory = 'All'"
                    class="px-5 py-3 rounded-xl font-black text-xs uppercase tracking-wider transition-all border shrink-0 cursor-pointer"
                    :class="activeCategory === 'All' ? 'bg-orange-600 border-orange-600 text-white shadow-lg shadow-orange-200/50' : 'bg-slate-50 border-slate-100 text-slate-500 hover:border-orange-400 hover:text-orange-600 hover:bg-white'"
                >
                    All Feasts
                </button>
                @foreach($categories as $cat)
                    <button
                        @click="activeCategory = '{{ $cat->name }}'"
                        class="px-5 py-3 rounded-xl font-black text-xs uppercase tracking-wider tracking-widest transition-all border shrink-0 cursor-pointer"
                        :class="activeCategory === '{{ $cat->name }}' ? 'bg-orange-600 border-orange-600 text-white shadow-lg shadow-orange-200/50' : 'bg-slate-50 border-slate-100 text-slate-500 hover:border-orange-400 hover:text-orange-600 hover:bg-white'"
                    >
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>

            <!-- Instant Search Box -->
            <div class="relative group max-w-md w-full">
                <input
                    type="text"
                    x-model="search"
                    placeholder="Search delicious beast foods..."
                    class="w-full pl-12 pr-10 py-3.5 rounded-xl bg-slate-50 border border-slate-100 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all text-xs"
                >
                <i class="fas fa-search absolute left-4.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-orange-500 transition-colors text-xs"></i>

                <button
                    x-show="search.length > 0"
                    @click="search = ''"
                    type="button"
                    class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                    x-cloak
                >
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Active Filter Counter Display -->
    <div x-show="isFiltered" x-cloak class="flex items-center justify-between bg-orange-50/50 border border-orange-100/50 p-4 rounded-2xl animate-in slide-in-from-top-4 duration-300">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-orange-500 animate-ping"></span>
            <span class="text-xs font-bold text-slate-600">
                Showing <strong class="text-slate-900" x-text="filteredItems.length"></strong> results matching your search terms.
            </span>
        </div>
        <button
            type="button"
            @click="resetFilters()"
            class="text-xs font-black text-orange-600 hover:text-orange-700 underline cursor-pointer"
        >
            Reset Filters
        </button>
    </div>

    <!-- Menu Catalog Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 pb-20">
        <template x-for="item in filteredItems" :key="item.id">
            <div
                class="bg-white rounded-[2rem] overflow-hidden border border-slate-100/80 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 group flex flex-col cursor-pointer"
                @click="openModal(item)"
            >
                <!-- Image container with ratio standard -->
                <div class="relative h-56 overflow-hidden bg-slate-100 shrink-0">
                    <img :src="item.image" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-md px-3 py-1.5 rounded-full text-[9px] font-black uppercase text-orange-600 tracking-wider shadow-sm border border-white" x-text="item.category"></div>
                </div>

                <!-- Product Specifications -->
                <div class="p-6 flex flex-col flex-grow justify-between gap-4">
                    <div class="space-y-2 text-left">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="text-base font-extrabold text-slate-900 group-hover:text-orange-600 transition-colors leading-snug" x-text="item.name"></h3>
                            <span class="text-base font-black text-slate-900 shrink-0">₱<span x-text="Number(item.price).toLocaleString()"></span></span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium leading-relaxed line-clamp-2" x-text="item.description"></p>
                    </div>

                    <!-- Dual Action Command layout -->
                    <div class="pt-2 flex gap-2">
                        <!-- Direct Action POST form -->
                        <form :action="item.add_url" method="POST" class="flex-grow m-0" @click.stop>
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <input type="hidden" name="special_instructions" value="">
                            <button
                                type="submit"
                                :disabled="isStoreClosed"
                                class="w-full font-extrabold py-3 rounded-xl transition-all flex items-center justify-center gap-2 border text-xs"
                                :class="isStoreClosed ? 'bg-slate-100 text-slate-400 border-slate-200/40 cursor-not-allowed' : 'bg-slate-50 hover:bg-orange-600 hover:text-white text-slate-800 border-slate-200/60 active:scale-95 cursor-pointer'"
                            >
                                <template x-if="isStoreClosed">
                                    <span><i class="fas fa-lock text-[10px]"></i> Closed</span>
                                </template>
                                <template x-if="!isStoreClosed">
                                    <span><i class="fas fa-plus"></i> Quick Add</span>
                                </template>
                            </button>
                        </form>

                        <!-- Open Customizer modal -->
                        <button
                            type="button"
                            @click.stop="openModal(item)"
                            class="bg-slate-900 hover:bg-orange-600 text-white w-10 h-10 rounded-xl flex items-center justify-center transition-all shrink-0 active:scale-95 cursor-pointer"
                            title="Personalize Item"
                        >
                            <i class="fas fa-sliders-h text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <!-- Beautiful Empty Search results -->
        <div
            x-show="filteredItems.length === 0"
            class="col-span-full py-16 px-4 text-center space-y-4"
            x-cloak
        >
            <div class="bg-orange-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto border border-orange-100">
                <i class="fas fa-search text-orange-500 text-2xl animate-pulse"></i>
            </div>
            <div class="max-w-md mx-auto space-y-1">
                <h4 class="text-lg font-extrabold text-slate-900">No feasts found</h4>
                <p class="text-xs font-bold text-slate-400">We couldn't track down matching meals. Try resetting search parameters!</p>
            </div>
            <button
                type="button"
                @click="resetFilters()"
                class="bg-slate-900 hover:bg-orange-600 text-white font-black px-6 py-3 rounded-xl text-xs uppercase tracking-wider transition-colors cursor-pointer active:scale-95 shadow"
            >
                Reset Search
            </button>
        </div>
    </div>

    <!-- Floating checkout button status panel -->
    @if(session('cart') && count(session('cart')) > 0)
        @php
            $totalCount = 0;
            $cartSum = 0;
            foreach(session('cart') as $cartItem) {
                $totalCount += $cartItem['quantity'];
                $cartSum += $cartItem['quantity'] * $cartItem['price'];
            }
        @endphp
        <div class="fixed bottom-6 right-6 z-40 animate-bounce cursor-pointer group">
            <a href="{{ route('cart.index') }}" class="flex items-center gap-4 bg-orange-600 hover:bg-orange-700 text-white pl-4 pr-6 py-4 rounded-3xl shadow-2xl hover:shadow-orange-400/50 hover:scale-105 active:scale-95 transition-all border border-orange-500/30">
                <div class="relative bg-white/20 w-12 h-12 rounded-2xl flex items-center justify-center text-white font-black text-lg">
                    <i class="fas fa-shopping-basket"></i>
                    <span class="absolute -top-1.5 -right-1.5 bg-red-600 border-2 border-orange-600 text-[10px] font-black h-5 w-5 flex items-center justify-center rounded-full">
                        {{ $totalCount }}
                    </span>
                </div>
                <div class="text-left">
                    <p class="text-[9px] font-black uppercase text-orange-200 leading-none">Your Feast Basket</p>
                    <p class="text-sm font-black text-white mt-1">₱{{ number_format($cartSum, 2) }}</p>
                </div>
            </a>
        </div>
    @endif

    <!-- Beautiful Customize & Add Modal -->
    <div
        x-show="showCustomizeModal"
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
        <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-md" @click="closeModal()"></div>

        <!-- Vertically align custom container -->
        <div class="flex min-h-screen items-center justify-center p-4">
            <div
                class="bg-white rounded-[2.5rem] shadow-2xl border border-slate-100 max-w-2xl w-full overflow-hidden relative"
                x-show="showCustomizeModal"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            >
                <!-- Closes handler -->
                <button @click="closeModal()" class="absolute top-4 right-4 z-20 bg-white/90 hover:bg-slate-100 text-slate-800 w-10 h-10 rounded-full flex items-center justify-center shadow-lg hover:rotate-90 transition-transform duration-300 active:scale-95 cursor-pointer border border-slate-200/40">
                    <i class="fas fa-times text-sm"></i>
                </button>

                <div class="grid grid-cols-1 md:grid-cols-2">
                    <!-- Food Image Side -->
                    <div class="relative h-64 md:h-auto min-h-[250px] md:min-h-[420px] bg-slate-50">
                        <img :src="activeItem.image" class="absolute inset-0 w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-slate-950/20 to-transparent"></div>
                        <div class="absolute bottom-6 left-6 right-6 text-white text-left">
                            <span class="bg-orange-600 text-white px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider shadow-md mb-2 inline-block border border-orange-500/20" x-text="activeItem.category"></span>
                            <h4 class="text-2xl font-black text-white leading-tight" x-text="activeItem.name"></h4>
                            <p class="text-sm font-black text-orange-400 mt-1">₱<span x-text="Number(activeItem.price).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></p>
                        </div>
                    </div>

                    <!-- Customizer parameters -->
                    <div class="p-8 flex flex-col justify-between space-y-6 text-left">
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1.5">Description</h3>
                                <p class="text-[11px] font-bold text-slate-500 leading-relaxed" x-text="activeItem.description"></p>
                            </div>

                            <!-- Quantity Selector component -->
                            <div class="pt-4 border-t border-slate-100">
                                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2.5">Set Portion Size</h3>
                                <div class="flex items-center gap-4 bg-slate-50 p-1.5 rounded-2xl w-fit border border-slate-100">
                                    <button type="button" @click="decQty()" class="w-10 h-10 rounded-xl bg-white text-slate-700 hover:text-orange-600 shadow-sm border border-slate-100 active:scale-90 transition-all font-black flex items-center justify-center text-sm cursor-pointer">
                                        <i class="fas fa-minus text-xs"></i>
                                    </button>
                                    <span class="text-sm font-black w-8 text-center text-slate-900 leading-none" x-text="qty"></span>
                                    <button type="button" @click="incQty()" class="w-10 h-10 rounded-xl bg-white text-slate-700 hover:text-orange-600 shadow-sm border border-slate-100 active:scale-90 transition-all font-black flex items-center justify-center text-sm cursor-pointer">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Requests details text -->
                            <div class="pt-4 border-t border-slate-100">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2 block">Special Requests</label>
                                <textarea
                                    x-model="specialInstructions"
                                    rows="2"
                                    class="w-full p-4 rounded-xl bg-slate-50 border border-slate-100 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all text-xs"
                                    placeholder="Examples: No onions, rare doneness, extra wild beast sauce..."></textarea>
                            </div>
                        </div>

                        <!-- Add Button and Dynamic Subtotal summary calculation -->
                        <div class="pt-4 border-t border-slate-100">
                            <form :action="activeItem.add_url" method="POST" class="m-0">
                                @csrf
                                <input type="hidden" name="quantity" :value="qty">
                                <input type="hidden" name="special_instructions" :value="specialInstructions">

                                <button
                                    type="submit"
                                    :disabled="isStoreClosed"
                                    class="w-full font-black py-4 px-5 rounded-xl transition-all flex items-center justify-between gap-4"
                                    :class="isStoreClosed ? 'bg-slate-200 text-slate-400 cursor-not-allowed shadow-none' : 'bg-slate-900 hover:bg-orange-600 text-white cursor-pointer active:scale-[0.98] shadow-xl hover:shadow-orange-200/50'"
                                >
                                    <span class="text-[10px] font-black tracking-wider uppercase">
                                        <template x-if="isStoreClosed">
                                            <span><i class="fas fa-lock mr-2"></i> Store Force Closed</span>
                                        </template>
                                        <template x-if="!isStoreClosed">
                                            <span><i class="fas fa-shopping-basket mr-2"></i> Add to Feast</span>
                                        </template>
                                    </span>
                                    <span class="text-xs font-black px-3.5 py-1 rounded-xl" :class="isStoreClosed ? 'text-slate-400 bg-slate-300/40' : 'text-white bg-white/20'">₱<span x-text="subtotal"></span></span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('customerMenuComponent', () => ({
        activeCategory: '{{ request('category', 'All') }}',
        search: '{{ request('search', '') }}',
        isStoreClosed: window.isStoreForceClosed || false,

        // Load items
        items: window.initialMenuItems || [],

        // Modal triggers
        showCustomizeModal: false,
        activeItem: {},
        qty: 1,
        specialInstructions: '',

        get filteredItems() {
            return this.items.filter(item => {
                // Category Filter
                const matchesCategory = this.activeCategory === 'All' || item.category === this.activeCategory;

                // Search query Filter
                const searchLower = this.search.toLowerCase().trim();
                const matchesSearch = !searchLower ||
                                      (item.name || '').toLowerCase().includes(searchLower) ||
                                      (item.description || '').toLowerCase().includes(searchLower);

                return matchesCategory && matchesSearch;
            });
        },

        get isFiltered() {
            return this.activeCategory !== 'All' || this.search.trim().length > 0;
        },

        resetFilters() {
            this.activeCategory = 'All';
            this.search = '';
        },

        openModal(item) {
            this.activeItem = item;
            this.qty = 1;
            this.specialInstructions = '';
            this.showCustomizeModal = true;
        },

        closeModal() {
            this.showCustomizeModal = false;
        },

        incQty() {
            this.qty++;
        },

        decQty() {
            if (this.qty > 1) {
                this.qty--;
            }
        },

        get subtotal() {
            if (!this.activeItem || !this.activeItem.price) return '0.00';
            const total = this.qty * Number(this.activeItem.price);
            return total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
    }));
});
</script>
@endsection

