@extends('layouts.app')

@section('title', 'Category Inventory')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-4 border-b">
        <div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">Manage Categories</h1>
            <p class="text-slate-500 font-bold mt-1">Add, update, or remove parent food and drink groups dynamically</p>
        </div>
        <div class="flex items-center gap-3 shrink-0 self-start md:self-auto">
            <a href="{{ route('admin.menu.index') }}" class="bg-white text-slate-700 px-6 py-3 rounded-2xl font-black shadow-sm border border-slate-200 hover:bg-slate-50 transition-colors flex items-center gap-2 text-sm">
                <i class="fas fa-boxes"></i> Go to Inventory
            </a>
        </div>
    </div>

    <!-- Main Workspace Split -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

        <!-- Left: Category Table -->
        <div class="lg:col-span-8 bg-white rounded-[3rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-8 border-b border-slate-50 flex items-center justify-between">
                <h3 class="text-lg font-black text-slate-900 uppercase tracking-widest">Active Categories</h3>
                <span class="bg-orange-100 text-orange-600 px-4 py-1.5 rounded-full text-xs font-black uppercase">{{ count($categories) }} Categories</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-8 py-4 text-[10px] font-black uppercase text-slate-400">ID</th>
                            <th class="px-8 py-4 text-[10px] font-black uppercase text-slate-400">Category Name</th>
                            <th class="px-8 py-4 text-[10px] font-black uppercase text-slate-400 text-center">Assigned Items</th>
                            <th class="px-8 py-4 text-[10px] font-black uppercase text-slate-400 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($categories as $category)
                        <tr class="hover:bg-slate-50/50 transition-all">
                            <td class="px-8 py-6">
                                <span class="font-mono text-xs text-slate-400 font-bold">#{{ $category->id }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-sm font-black text-slate-900">{{ $category->name }}</p>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <span class="inline-flex items-center justify-center bg-slate-100 font-black text-xs text-slate-700 h-7 px-3 rounded-xl">
                                    {{ $category->menu_items_count }} items
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="p-2.5 text-slate-400 hover:text-orange-500 hover:bg-orange-50 rounded-xl transition-all" title="Edit Category">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>

                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category? Any associated items will be fallback-reassigned safely.');">
                                        @csrf
                                        <button type="submit" class="p-2.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all" title="Delete Category">
                                            <i class="fas fa-trash-alt text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Inline Add Form -->
        <div class="lg:col-span-4">
            <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-slate-100 space-y-6 sticky top-24">
                <div class="space-y-1">
                    <h3 class="text-xl font-black text-slate-900">Add New Category</h3>
                    <p class="text-xs text-slate-400 font-bold">Instantly include a new tab or filter group on the menu</p>
                </div>

                @if($errors->any())
                    <div class="p-4 bg-red-50 border border-red-100 text-red-600 text-xs font-bold rounded-2xl">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Category Name</label>
                        <input type="text" name="name" required
                            class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all placeholder-slate-300"
                            placeholder="e.g. Desserts, Burgers...">
                    </div>

                    <button type="submit" class="w-full bg-slate-900 hover:bg-orange-600 text-white font-black py-4 rounded-2xl text-sm shadow-xl shadow-slate-100 transition-all active:scale-95">
                        <i class="fas fa-plus mr-1"></i> Save Category
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
