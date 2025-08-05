<x-app-layout>

    <div class="px-4 space-y-6 pb-6">
        <div class="sm:p-0 pt-0">
            <livewire:convert-file>
        </div>
    </div>

    @push('scripts')
        <script>
            window.addEventListener('livewire:initialized', () => {
                Livewire.on('scroll-to-next', ({ id }) => {
                    const current = document.getElementById(id);
                    if (!current) {
                        return;
                    }

                    const next = current.nextElementSibling;
                    if (next) {
                        next.scrollIntoView({ behavior: 'smooth', block: 'start' });

                        // Optional: Highlight effect
                        //next.classList.add('bg-yellow-100');
                        //setTimeout(() => next.classList.remove('bg-yellow-100'), 1500);
                    }
                });
            });
        </script>
    @endpush

</x-app-layout>

