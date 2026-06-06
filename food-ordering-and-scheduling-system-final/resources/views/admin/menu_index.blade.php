@extends('layouts.app')

@section('content')
<div x-data="{
    search: '',
    categoryFilter: 'all',
    lightboxOpen: false,
    lightboxUrl: '',
    lightboxProductName: ''
}" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12 pb-24">

    <!-- Header & Action Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                Culinary Catalog
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-orange-50 text-orange-600 border border-orange-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500 mr-2"></span>
                    Inventory Control
                </span>
            </h1>
            <p class="text-slate-500 font-bold mt-1">Refine descriptions, manage instantaneous item availability, and audit pricing streams</p>
        </div>
        <a href="{{ route('admin.menu.create') }}" class="bg-slate-950 hover:bg-orange-600 text-white px-8 py-4 rounded-3xl font-black text-xs shadow-lg uppercase tracking-wider flex items-center justify-center gap-3 transition-all active:scale-95 shrink-0 self-start md:self-auto cursor-pointer">
            <i class="fas fa-plus text-sm text-orange-400"></i> Introduce New Item
        </a>
    </div>

    <!-- Live Statistics Overview Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Stat Card 1 -->
        <div class="bg-white rounded-3xl p-6 border-2 border-slate-50 flex items-center gap-5 shadow-sm">
            <div class="w-12 h-12 rounded-2xl bg-slate-50 border flex items-center justify-center text-slate-700 shrink-0">
                <i class="fas fa-utensils text-lg"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Items</p>
                <p class="text-2xl font-black text-slate-950">{{ $items->count() }}</p>
            </div>
        </div>

        <!-- Stat Card 2 -->
        <div class="bg-white rounded-3xl p-6 border-2 border-slate-50 flex items-center gap-5 shadow-sm">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                <i class="fas fa-check-circle text-lg"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Active/Available</p>
                <p class="text-2xl font-black text-slate-950">{{ $items->where('availability_status', true)->count() }}</p>
            </div>
        </div>

        <!-- Stat Card 3 -->
        <div class="bg-white rounded-3xl p-6 border-2 border-slate-50 flex items-center gap-5 shadow-sm">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600 shrink-0">
                <i class="fas fa-times-circle text-lg"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Sold Out / Paused</p>
                <p class="text-2xl font-black text-slate-950">{{ $items->where('availability_status', false)->count() }}</p>
            </div>
        </div>

        <!-- Stat Card 4 -->
        <div class="bg-white rounded-3xl p-6 border-2 border-slate-50 flex items-center gap-5 shadow-sm">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                <i class="fas fa-coins text-lg"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Average Value</p>
                <p class="text-2xl font-black text-slate-950">₱{{ $items->count() > 0 ? number_format($items->avg('price'), 0) : '0' }}</p>
            </div>
        </div>
    </div>

    <!-- Live Client-Side Searching & Real-Time Filtering System -->
    <div class="bg-slate-50/50 rounded-[2.5rem] border p-6 space-y-5">
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <!-- Search field -->
            <div class="relative w-full md:max-w-md">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" x-model="search"
                    placeholder="Search dishes, descriptions, prices..."
                    class="w-full bg-white pl-11 pr-4 py-4 rounded-2xl border border-slate-200 text-sm font-bold placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-orange-100 focus:border-orange-500 transition-all">
                <button x-show="search.length > 0" @click="search = ''" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600">
                    <i class="fas fa-times-circle text-sm"></i>
                </button>
            </div>

            <div class="text-[11px] font-black text-slate-400 uppercase tracking-widest bg-white border py-2 px-4 rounded-xl shadow-sm">
                 Filtered display list
            </div>
        </div>

        <!-- Category tabs filter -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
            <button @click="categoryFilter = 'all'"
                :class="categoryFilter === 'all' ? 'bg-slate-950 text-white font-black shadow' : 'bg-white text-slate-600 hover:text-slate-900 border'"
                class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer">
                All Culinary Categories
            </button>
            @foreach($categories as $category)
                <button @click="categoryFilter = '{{ $category->name }}'"
                    :class="categoryFilter === '{{ $category->name }}' ? 'bg-orange-600 text-white font-black shadow' : 'bg-white text-slate-600 hover:text-slate-900 border'"
                    class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Interactive Culinary Inventory Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        @forelse($items as $item)
            <div x-show="(categoryFilter === 'all' || categoryFilter === '{{ $item->category }}') && ('{{ addslashes(strtolower($item->name)) }}'.includes(search.toLowerCase()) || '{{ addslashes(strtolower($item->description)) }}'.includes(search.toLowerCase()))"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-[2.5rem] border overflow-hidden flex flex-col group transition-all hover:shadow-2xl hover:-translate-y-1 duration-300 relative
                 {{ !$item->availability_status ? 'opacity-80 border-dashed bg-slate-50/50' : 'border-slate-100' }}">

                <!-- Display Image with Recipe Reference capabilities -->
                <div class="relative h-56 bg-slate-100 overflow-hidden shrink-0">
                    <img src="{{ $item->image }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" alt="{{ $item->name }}">

                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex flex-col justify-between p-5">
                        <div class="flex justify-between items-start">
                            <span class="bg-white/95 backdrop-blur-md px-3.5 py-1.5 rounded-xl text-[9px] font-black uppercase text-orange-600 tracking-wider shadow-sm">
                                {{ $item->category }}
                            </span>

                            <!-- Trigger lightbox for a beautiful presentation preview -->
                            <button @click="lightboxUrl = '{{ $item->image }}'; lightboxProductName = '{{ addslashes($item->name) }}'; lightboxOpen = true;"
                                    class="w-8 h-8 rounded-xl bg-white/90 backdrop-blur-md text-slate-700 hover:text-orange-600 transition-colors flex items-center justify-center cursor-pointer shadow-sm">
                                <i class="fas fa-search-plus text-xs"></i>
                            </button>
                        </div>

                        <div>
                            <span class="text-orange-400 font-black text-2xl font-mono">₱{{ number_format($item->price, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Card Info -->
                <div class="p-6 flex-grow flex flex-col justify-between gap-5">
                    <div class="space-y-2">
                        <h3 class="font-black text-slate-900 text-lg leading-snug group-hover:text-orange-600 transition-colors">
                            {{ $item->name }}
                        </h3>
                        <p class="text-xs text-slate-400 font-medium leading-relaxed line-clamp-3">
                            {{ $item->description }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-slate-50 space-y-4 shrink-0">
                        <div class="flex items-center justify-between">
                            <!-- Toggle status layout -->
                            <span class="inline-flex items-center text-[10px] font-black uppercase tracking-wider">
                                <span class="w-2 h-2 rounded-full mr-1.5 {{ $item->availability_status ? 'bg-emerald-500 animate-pulse' : 'bg-rose-400' }}"></span>
                                {{ $item->availability_status ? 'In Stock' : 'Sold Out' }}
                            </span>

                            <!-- Inline toggle form -->
                            <form action="{{ route('admin.menu.toggle', $item->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        title="Click to toggle availability status instantly"
                                        class="px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all cursor-pointer border
                                        {{ $item->availability_status ? 'bg-emerald-50 hover:bg-emerald-100 text-emerald-600 border-emerald-100' : 'bg-rose-50 hover:bg-rose-100 text-rose-600 border-rose-100' }}">
                                    <i class="fas {{ $item->availability_status ? 'fa-check' : 'fa-times' }} mr-1"></i>
                                    Toggle State
                                </button>
                            </form>
                        </div>

                        <!-- Action Parameters (Edit or Delete) -->
                        <div class="grid grid-cols-12 gap-2 mt-2">
                            <a href="{{ route('admin.menu.edit', $item->id) }}"
                               class="col-span-9 bg-slate-50 hover:bg-orange-600 group-hover:border-transparent group-hover:bg-slate-950 border text-slate-800 hover:text-white group-hover:text-white text-xs font-black py-3 rounded-xl transition-all font-mono tracking-wide text-center flex items-center justify-center gap-2">
                                <i class="fas fa-cog"></i> Edit Details
                            </a>
                            <form action="{{ route('admin.menu.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Ensure absolute certainty before deleting &quot;{{ addslashes($item->name) }}&quot; permanently.');" class="col-span-3">
                                @csrf
                                <button type="submit" class="w-full h-11 bg-slate-50 hover:bg-red-50 border hover:border-red-100 text-slate-300 hover:text-red-500 rounded-xl transition-all flex items-center justify-center cursor-pointer">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full py-24 text-center">
                <div class="bg-white w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm border">
                    <i class="fas fa-utensils text-3xl text-slate-200"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-900">Inventory Empty</h3>
                <p class="text-slate-500 font-medium mt-1">Ready to cook standard items? Introduce a new catalog entry above!</p>
            </div>
        @endforelse
    </div>

    <!-- Overlaid Recipe Reference Lightbox -->
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
                    <span class="text-[9px] font-black text-orange-600 uppercase tracking-widest block mb-0.5">Presentation Reference</span>
                    <h4 class="text-sm font-black text-slate-900" x-text="lightboxProductName"></h4>
                </div>
                <button @click="lightboxOpen = false" class="text-slate-400 hover:text-slate-900 transition-colors cursor-pointer w-8 h-8 rounded-xl bg-white border flex items-center justify-center">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-6 flex flex-col items-center">
                <img :src="lightboxUrl" class="max-h-[380px] w-full rounded-2xl object-cover border shadow-md" alt="Dynamic Menu Presentation Image">
                <p class="text-slate-500 text-xs font-bold text-center mt-4">
                    <i class="fas fa-info-circle mr-1 text-slate-400"></i> Plated presentation reference image.
                </p>
            </div>
        </div>
    </div>

</div>
@endsection
