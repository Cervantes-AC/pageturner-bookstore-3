@php
    $showCategory = $showCategory ?? true;
    $action = $action ?? route('books.index');
    $clearUrl = $clearUrl ?? route('books.index');
    $activeFilters = $showCategory
        ? ['search', 'category', 'year', 'min_price', 'max_price', 'stock_status', 'sort']
        : ['search', 'year', 'min_price', 'max_price', 'stock_status', 'sort'];
@endphp

<div class="mb-8" x-data="{ filtersOpen: localStorage.getItem('bookFiltersOpen') !== 'false' }" x-init="$watch('filtersOpen', value => localStorage.setItem('bookFiltersOpen', value))">
    <div class="bg-white border border-parchment-200 rounded-lg shadow-sm overflow-hidden">
        <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-parchment-200">
            <div>
                <h2 class="font-heading text-xl font-bold text-ink-900">Find Books</h2>
                <p class="text-sm text-ink-400 mt-1">{{ $books->total() }} {{ Str::plural('result', $books->total()) }} available</p>
            </div>
            <button type="button"
                    @click="filtersOpen = !filtersOpen"
                    class="btn-secondary inline-flex items-center justify-center gap-2">
                <svg x-show="!filtersOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707L15 13v5l-6 3v-8L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <svg x-show="filtersOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span x-text="filtersOpen ? 'Hide Filters' : 'Show Filters'"></span>
            </button>
        </div>

        <form x-show="filtersOpen" x-transition action="{{ $action }}" method="GET" class="p-5 space-y-5">
            <div>
                <label class="block text-ink-700 text-sm font-semibold mb-2">Search</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Title, author, or ISBN"
                           class="input-field pl-11">
                    <svg class="w-5 h-5 text-parchment-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            @if($showCategory)
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('books.index') }}"
                       class="px-3 py-1.5 rounded-lg text-sm font-medium border {{ request('category') ? 'bg-white text-ink-600 border-parchment-300 hover:border-gold-400' : 'bg-ink-700 text-white border-ink-700' }}">
                        All
                    </a>
                    @foreach($categories as $categoryOption)
                        <a href="{{ route('books.index', array_merge(request()->except('page'), ['category' => $categoryOption->id])) }}"
                           class="px-3 py-1.5 rounded-lg text-sm font-medium border {{ request('category') == $categoryOption->id ? 'bg-gold-600 text-white border-gold-600' : 'bg-white text-ink-600 border-parchment-300 hover:border-gold-400' }}">
                            {{ $categoryOption->name }}
                        </a>
                    @endforeach
                </div>
            @endif

            <div class="grid grid-cols-1 {{ $showCategory ? 'md:grid-cols-4' : 'md:grid-cols-3' }} gap-4">
                @if($showCategory)
                    <div>
                        <label class="block text-ink-700 text-sm font-semibold mb-2">Category</label>
                        <select name="category" class="input-field">
                            <option value="">All Categories</option>
                            @foreach($categories as $categoryOption)
                                <option value="{{ $categoryOption->id }}" {{ request('category') == $categoryOption->id ? 'selected' : '' }}>
                                    {{ $categoryOption->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label class="block text-ink-700 text-sm font-semibold mb-2">Sort</label>
                    <select name="sort" class="input-field">
                        <option value="">Newest First</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Title: A-Z</option>
                        <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Title: Z-A</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                </div>

                <div>
                    <label class="block text-ink-700 text-sm font-semibold mb-2">Publication Year</label>
                    <select name="year" class="input-field">
                        <option value="">All Years</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-ink-700 text-sm font-semibold mb-2">Stock</label>
                    <select name="stock_status" class="input-field">
                        <option value="">Any Stock</option>
                        <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-ink-700 text-sm font-semibold mb-2">Price Range</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <input type="number" name="min_price" value="{{ request('min_price') }}" step="0.01" min="0" placeholder="Minimum price" class="input-field">
                    <input type="number" name="max_price" value="{{ request('max_price') }}" step="0.01" min="0" placeholder="Maximum price" class="input-field">
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <button type="submit" class="btn-primary inline-flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Apply Filters
                </button>
                @if(request()->hasAny($activeFilters))
                    <a href="{{ $clearUrl }}" class="btn-secondary inline-flex items-center justify-center">Clear All</a>
                @endif
            </div>
        </form>
    </div>
</div>
