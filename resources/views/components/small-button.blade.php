<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-1 py-0 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest focus:outline-none transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
