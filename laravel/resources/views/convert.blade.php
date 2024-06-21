<x-app-layout>

    <div class="px-4 space-y-6 pb-6">
        <div class="sm:p-0 pt-0">
            <livewire:convert-file>
        </div>
    </div>

    @section('scripts')
        <script>
            /*
            history.scrollRestoration = "manual";
            window.onbeforeunload = function () {
                window.scrollTo(0, 0);
            }
            */
            /*document.body.scrollIntoView({behavior: "smooth"});*/
            /*
            document.addEventListener('livewire:navigated', () => {
                window.scrollTo(0,0);
            })
            */
        </script>
    @endsection

</x-app-layout>

