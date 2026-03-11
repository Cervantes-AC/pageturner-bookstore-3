<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-blue-700 hover:to-purple-700 focus:from-blue-700 focus:to-purple-700 active:from-blue-800 active:to-purple-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-800 transition ease-in-out duration-150 shadow-lg']) }}>
    {{ $slot }}
</button>
