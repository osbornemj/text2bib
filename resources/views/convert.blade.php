<x-app-layout>

    <div class="sm:px-4 lg:px-4 space-y-6">
        <div class="sm:p-0 pt-0 sm:pt-0">
            <livewire:convert-file />
        </div>
    </div>

    @section('scripts')
        <script>
            document.body.scrollIntoView({behavior: "smooth"});
        </script>
    @endsection

</x-app-layout>

