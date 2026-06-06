@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
<div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="space-y-6">
        <!-- Back Link -->
        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center gap-2 text-sm font-black text-slate-500 hover:text-orange-600 transition-colors uppercase tracking-wider">
            <i class="fas fa-arrow-left"></i> Back to Categories
        </a>

        <!-- Header -->
        <div class="pb-4 border-b border-slate-100">
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Edit Category</h1>
            <p class="text-slate-500 font-bold mt-1">Change the descriptor or naming format of the group</p>
        </div>

        @if($errors->any())
            <div class="p-4 bg-red-50 border border-red-100 text-red-600 text-xs font-bold rounded-2xl">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Edit Form Card -->
        <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-100 border border-slate-100 overflow-hidden">
            <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" class="p-10 space-y-8">
                @csrf

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Category Name</label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                        class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all"
                        placeholder="Group Descriptor">
                </div>

                <!-- Guidance Note -->
                <div class="p-4 bg-orange-50/50 rounded-2xl border border-orange-100 text-[11px] text-orange-700 font-bold leading-relaxed space-y-1">
                    <p class="font-extrabold uppercase tracking-widest text-[9px]"><i class="fas fa-info-circle mr-1"></i> Intelligent Sync Active</p>
                    <p>Changing this category name will automatically migrate any menu items currently classified under "{{ $category->name }}" to keep your store completely in sync.</p>
                </div>

                <!-- Action Triggers -->
                <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.categories.index') }}" class="px-6 py-3 rounded-2xl font-black text-slate-500 hover:bg-slate-50 transition-colors text-sm text-center">
                        Cancel
                    </a>
                    <button type="submit" class="bg-slate-900 hover:bg-orange-600 text-white px-8 py-3.5 rounded-xl font-black text-sm shadow-xl shadow-slate-100 transition-all active:scale-95">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
