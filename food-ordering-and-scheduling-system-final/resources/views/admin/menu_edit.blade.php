@extends('layouts.app')

@section('title', 'Edit Menu Item')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 pb-24">
    <!-- Back Navigation -->
    <div>
        <a href="{{ route('admin.menu.index') }}" class="inline-flex items-center gap-2 text-xs font-black text-slate-500 hover:text-orange-600 transition-colors uppercase tracking-widest bg-white py-2 px-4 rounded-xl border shadow-sm cursor-pointer">
            <i class="fas fa-arrow-left text-orange-500"></i> Back to Catalog
        </a>
    </div>

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 pb-6 border-b border-slate-100">
        <div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                Modify Item
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-orange-50 text-orange-600 border border-orange-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500 mr-2"></span>
                    Update Portal
                </span>
            </h1>
            <p class="text-slate-500 font-bold mt-1">Refine options, pricing indices, and high-fidelity presentation photos for the chosen item</p>
        </div>
        <div class="flex items-center gap-3 bg-white px-5 py-3 rounded-2xl border shadow-sm shrink-0 self-start md:self-auto">
            <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></span>
            <p class="text-xs font-black uppercase tracking-wide text-slate-700">Item: #ID-{{ $item->id }}</p>
        </div>
    </div>

    @if($errors->any())
        <div class="p-6 bg-rose-50 border-2 border-rose-100 text-rose-700 font-bold rounded-[2rem] text-sm leading-relaxed max-w-4xl mx-auto shadow-sm">
            <p class="font-black uppercase tracking-wider text-xs mb-2 flex items-center gap-2 text-rose-800">
                <i class="fas fa-exclamation-triangle"></i> Verification Errors Found
            </p>
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Live Interactive Builder -->
    <div class="bg-white rounded-[3rem] shadow-2xl border border-slate-100 overflow-hidden" x-data="{
        name: '{{ old('name', $item->name) }}',
        category: '{{ old('category', $item->category) }}',
        price: '{{ old('price', $item->price) }}',
        description: `{{ old('description', $item->description) }}`,
        imageType: '{{ $item->image && str_starts_with($item->image, '/uploads/') ? 'file' : 'url' }}',
        imageUrl: '{{ $item->image && !str_starts_with($item->image, '/uploads/') ? $item->image : '' }}',
        filePreview: '{{ $item->image && str_starts_with($item->image, '/uploads/') ? $item->image : null }}',

        get computedImage() {
            if (this.imageType === 'file' && this.filePreview) {
                return this.filePreview;
            }
            if (this.imageType === 'url' && this.imageUrl && this.imageUrl.trim() !== '') {
                return this.imageUrl;
            }
            return 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=80&w=800';
        },

        updateFilePreview(event) {
            const file = event.target.files[0];
            if (file) {
                this.filePreview = URL.createObjectURL(file);
            }
        },

        formatPrice(val) {
            let num = parseFloat(val);
            return isNaN(num) ? '0' : num.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 2 });
        }
    }">
        <form action="{{ route('admin.menu.update', $item->id) }}" method="POST" enctype="multipart/form-data" class="p-8 md:p-12">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

                <!-- Left Column: Settings Configuration -->
                <div class="lg:col-span-7 space-y-10">
                    <div>
                        <h3 class="text-xl font-black text-slate-950">Gourmet Parameter Stream</h3>
                        <p class="text-xs text-slate-400 font-bold mt-1">Amend characteristics of the chosen item below to synchronize instantly</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Name Field -->
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Gourmet Item Name</label>
                            <input type="text" name="name" x-model="name" required
                                class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all placeholder-slate-300 text-slate-800"
                                placeholder="e.g. Garlic Herb Prime Rib">
                        </div>

                        <!-- Category Selection -->
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Main Classification</label>
                            <select name="category" x-model="category" required
                                class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all text-slate-800">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->name }}" {{ old('category', $item->category) == $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Price parameter -->
                        <div class="space-y-1.5 sm:col-span-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Base Value (₱ PHP)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 font-black font-mono">₱</span>
                                <input type="number" step="0.01" min="0" name="price" x-model="price" required
                                    class="w-full pl-10 pr-4 py-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all placeholder-slate-300 text-slate-800"
                                    placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Description -->
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Craft description</label>
                        <textarea name="description" rows="4" x-model="description" required
                            class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all placeholder-slate-300 text-slate-800 leading-relaxed"
                            placeholder="Detail presentation parameters, ingredients, prep timelines..."></textarea>
                    </div>

                    <!-- Image source Options -->
                    <div class="space-y-5">
                        <label class="block text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Plating Photo Sourcing</label>
                        <div class="grid grid-cols-2 gap-3 p-1.5 bg-slate-100 rounded-[1.5rem] border">
                            <button type="button" @click="imageType = 'url'" :class="imageType === 'url' ? 'bg-white text-orange-600 shadow-sm font-extrabold' : 'text-slate-500 font-bold'" class="py-3 rounded-2xl text-xs transition-all cursor-pointer">
                                <i class="fas fa-link mr-1.5"></i> Web asset URL
                            </button>
                            <button type="button" @click="imageType = 'file'" :class="imageType === 'file' ? 'bg-white text-orange-600 shadow-sm font-extrabold' : 'text-slate-500 font-bold'" class="py-3 rounded-2xl text-xs transition-all cursor-pointer">
                                <i class="fas fa-cloud-upload-alt mr-1.5"></i> Custom uploading
                            </button>
                        </div>

                        <!-- Web asset input -->
                        <div x-show="imageType === 'url'" x-cloak class="space-y-1 z-10 animate-in fade-in duration-200">
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Image URL</label>
                            <input type="url" name="image_url" x-model="imageUrl"
                                class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all placeholder-slate-300 text-slate-800"
                                placeholder="https://images.unsplash.com/photo-...">
                        </div>

                        <!-- Custom upload input -->
                        <div x-show="imageType === 'file'" x-cloak class="space-y-1 z-10 animate-in fade-in duration-200">
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Upload Custom image file</label>
                            <div class="relative group">
                                <input type="file" name="image_file" @change="updateFilePreview($event)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <div class="border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center group-hover:border-orange-400 transition-colors bg-slate-50">
                                    <div x-show="filePreview" class="flex items-center justify-center gap-3">
                                        <i class="fas fa-check-circle text-emerald-500 text-2xl animate-bounce"></i>
                                        <span class="text-xs font-black text-slate-800 uppercase tracking-wide">Image Loaded For Upload!</span>
                                    </div>
                                    <div x-show="!filePreview" class="space-y-2">
                                        <i class="fas fa-image text-slate-400 text-4xl mb-1 group-hover:text-orange-500 transition-colors"></i>
                                        <p class="text-xs font-black text-slate-700 uppercase tracking-widest">Click or Drop Picture Here</p>
                                        <p class="text-[10px] text-slate-400 font-bold">JPEG, PNG, WEBP formats up to 5MB are accepted</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submission buttons -->
                    <div class="pt-8 border-t border-slate-100 flex items-center justify-end gap-4">
                        <a href="{{ route('admin.menu.index') }}" class="px-8 py-4 rounded-2xl text-xs font-black uppercase tracking-wider text-slate-500 hover:bg-slate-50 transition-all text-center">
                            Cancel Changes
                        </a>
                        <button type="submit" class="bg-slate-950 hover:bg-orange-600 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-slate-100 transition-all active:scale-95 cursor-pointer">
                            Update Item Properties
                        </button>
                    </div>
                </div>

                <!-- Right Column: Instant Live Display card -->
                <div class="lg:col-span-5 flex flex-col justify-start">
                    <div class="sticky top-24 space-y-6 bg-slate-50/50 p-8 rounded-[3rem] border border-dashed border-slate-200">
                        <div>
                            <h3 class="text-xl font-black text-slate-900">Current Appearance</h3>
                            <p class="text-xs text-slate-400 font-bold mt-1">Real-time simulator of standard catalog layout appearance</p>
                        </div>

                        <!-- Sample Menu item layout -->
                        <div class="bg-white rounded-[2.5rem] overflow-hidden border border-slate-100 shadow-2xl transition-all duration-300 max-w-sm mx-auto w-full flex flex-col hover:shadow-orange-100">
                            <div class="relative h-56 overflow-hidden bg-slate-50">
                                <img :src="computedImage" class="w-full h-full object-cover transition-transform duration-500" alt="Item Preview">
                                <div class="absolute top-4 left-4 bg-white/95 backdrop-blur-md px-4 py-1.5 rounded-xl text-[10px] font-black uppercase text-orange-600 tracking-wider shadow-sm" x-text="category">
                                </div>
                            </div>
                            <div class="p-6 flex flex-col flex-grow space-y-3 bg-white">
                                <div class="flex justify-between items-start gap-2">
                                    <h3 class="text-lg font-black text-slate-950 truncate max-w-[200px]" x-text="name ? name : 'Recipe Item Name'"></h3>
                                    <span class="text-lg font-black text-slate-900 font-mono">₱<span x-text="price ? formatPrice(price) : '0.00'"></span></span>
                                </div>
                                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span> Automatic availability
                                </p>
                                <p class="text-xs text-slate-500 font-semibold leading-relaxed min-h-[48px]" x-text="description ? description : 'Specify full craft details, flavor palettes, and side serving suggestions using the parameter inputs...'"></p>

                                <div class="pt-2">
                                    <button type="button" disabled class="w-full bg-slate-100 text-slate-400 font-black py-3 rounded-xl text-[10px] uppercase tracking-wider flex items-center justify-center gap-2">
                                        <i class="fas fa-lock"></i> Add to Selection
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection
