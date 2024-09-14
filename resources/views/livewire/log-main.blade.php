<div class="min-h-screen bg-gray-100/10" id="app-container" x-data x-init="setInterval(() => $wire.getLogs(), 2000)">
    <div class="p-6">
        <form class="flex w-full space-x-3" wire:submit.prevent="getLogs">
            <button wire:click="getLogs"
                class="h-10 whitespace-nowrap rounded-md bg-gray-200 px-3 py-2 hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-slate-500">
                <x-icons.refresh class="h-5 w-5"></x-icons.refresh>
            </button>
            {{-- <input type="text" class="h-10 w-full rounded-md bg-gray-300 px-4"
                wire:model.live.debounce.300ms="projectPath" placeholder="Project Path"> --}}
            <x-form.input wire:model.live.debounce.300ms="projectPath" placeholder="Project Path" />
            {{-- <input type="text" class="h-10 w-full rounded-md bg-gray-300 px-4"
                wire:model.live.debounce.300ms="projectPath" placeholder="Project Path"> --}}
        </form>

        <div class="my-3">
            <x-form.input autofocus wire:model.live.debounce.300ms="filterLog" type="text"
                placeholder="Type something to filter logs" />
        </div>

        <div class="mt-6 divide-y divide-gray-700 overflow-hidden rounded-lg">
            @foreach ($logs as $log)
                <div>
                    <div class="overflow-hidden bg-slate-700 text-gray-300" x-data="{
                        open: true
                    }"
                        id="log-{{ $log['timestamp'] }}">
                        {{-- header part of the log --}}
                        <div class="flex cursor-pointer select-none justify-between bg-slate-800 py-1 hover:bg-slate-900"
                            @click="open = !open">
                            <div class="flex items-center">
                                <div class="px-4 py-2 text-sm font-semibold">
                                    {{ $log['timestamp'] }}
                                </div>
                                <div class="px-3 py-2 text-sm">
                                    @php
                                        $typeClass = $log['type'] === 'ERROR' ? 'bg-red-500' : 'bg-blue-500';
                                    @endphp

                                    <span
                                        class="{{ $typeClass }} block rounded-full px-3 py-0.5 font-medium text-white">{{ $log['env'] }}.{{ $log['type'] }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center px-4">
                                <a class="cursor-pointer select-none rounded-lg p-1 hover:bg-slate-700"
                                    x-bind:class="{
                                        'rotate-180': open,
                                    }">
                                    <x-icons.chevron-down class="h-5 w-5"></x-icons.chevron-down>
                                </a>
                            </div>
                        </div>
                        {{-- end of header part of the log --}}
                        <div class="overflow-x-auto p-3 text-sm" x-show="open">
                            <pre x-html="`{{ $log['log'] }}`" class="w-full"></pre>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div x-data="{
        show: false,
        scrollTop() {
            window.scrollTo({ top: 0 })
        }
    }" x-init="window.addEventListener('scroll', () => {
        show = window.scrollY >= 200;
    });" x-cloak x-bind:class="{
        'hidden': !show,
    }"
        class="fixed left-0 right-0 top-0 z-10 m-auto mt-6 grid h-10 w-10 cursor-pointer place-items-center rounded-full bg-white shadow-lg"
        @click="scrollTop">
        <x-icons.chevron-up />
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('main', {
            showSettings: false,
        })
    })
</script>
