@extends('layouts.app')
@section('title', 'Categories')
@section('page-title', 'Categories')

@section('content')
<div class="max-w-4xl">
    <!-- Add Category -->
    <div class="bg-white border border-beige-200/60 rounded-3xl p-6 mb-6">
        <form method="POST" action="{{ route('categories.store') }}" class="flex items-end gap-4">
            @csrf
            <div class="flex-1">
                <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Category Name</label>
                <input type="text" name="name" required placeholder="e.g., Beverages, Snacks, Main Course"
                       class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm font-medium text-mint-900 focus:ring-2 focus:ring-mint-500/20 focus:border-mint-500 transition-all">
            </div>
            <div class="w-48">
                <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Description</label>
                <input type="text" name="description" placeholder="Optional"
                       class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm font-medium text-mint-900 focus:ring-2 focus:ring-mint-500/20 focus:border-mint-500 transition-all">
            </div>
            <div class="w-24">
                <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Color</label>
                <input type="color" name="color" value="#10B981"
                       class="w-full h-[46px] rounded-xl border border-beige-200 cursor-pointer">
            </div>
            <button type="submit" class="px-6 py-3 bg-mint-500 text-white font-bold text-sm rounded-xl hover:bg-mint-600 transition-all shadow-lg shadow-mint-500/30 whitespace-nowrap">
                + Add
            </button>
        </form>
    </div>

    <!-- Category List -->
    <div class="space-y-3">
        @forelse($categories as $category)
        <div class="bg-white border border-beige-200/60 rounded-2xl p-5 flex items-center justify-between group hover:shadow-lg hover:shadow-mint-900/5 transition-all">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-sm" style="background-color: {{ $category->color }}">
                    {{ substr($category->name, 0, 2) }}
                </div>
                <div>
                    <h4 class="text-sm font-bold text-mint-900">{{ $category->name }}</h4>
                    <p class="text-xs text-beige-500">{{ $category->description ?: 'No description' }} · {{ $category->products_count }} products</p>
                </div>
            </div>
            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">
                    @csrf @method('DELETE')
                    <button class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="bg-white border border-beige-200/60 rounded-3xl p-12 text-center">
            <p class="text-sm font-bold text-beige-400">No categories yet. Add your first one above!</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
