<div class="p-6">
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}
    {{-- <div class="rounded-lg bg-gray-300 p-2">projectPath: {{ $projectPath }}</div> --}}
    <form class="flex w-full space-x-3" wire:submit.prevent="getLogs">
        <button wire:click="getLogs"
            class="h-10 whitespace-nowrap rounded-md bg-slate-700 px-4 py-2 text-gray-100 focus:outline-none focus:ring-2 focus:ring-slate-500">Get
            Logs</button>
        <input type="text" class="h-10 w-full rounded-md bg-gray-300 px-4" wire:model.live.debounce.300ms="projectPath"
            placeholder="Project Path">
    </form>

    <div class="my-3">
        <input autofocus wire:model.live.debounce.300ms="filterLog" type="text"
            class="h-10 w-full rounded-md bg-gray-300 px-4 focus:outline-none focus:ring-2 focus:ring-slate-500"
            placeholder="Type something to filter logs">
    </div>

    <span wire:loading wire:target="getLogs">loading...</span>

    <div class="mt-6 divide-y divide-gray-700 overflow-hidden rounded-lg">
        @foreach ($logs as $log)
            <div>
                <div class="overflow-hidden bg-slate-700 text-gray-300" x-data="{
                    open: true
                }">
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
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                    width="24" height="24" color="#000000" fill="none">
                                    <path d="M18 9.00005C18 9.00005 13.5811 15 12 15C10.4188 15 6 9 6 9"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    {{-- end of header part of the log --}}
                    <div class="overflow-x-auto p-3 text-base" x-show="open">
                        <pre x-html="`{{ $log['log'] }}`"></pre>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
