<div class="p-6">
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}
    {{-- <div class="rounded-lg bg-gray-300 p-2">projectPath: {{ $projectPath }}</div> --}}
    <form class="flex w-full space-x-3" wire:submit.prevent="getLogs">
        <button wire:click="getLogs" class="h-10 whitespace-nowrap rounded-md bg-slate-700 px-4 py-2 text-gray-100">Get
            Logs</button>
        <input type="text" class="h-10 w-full rounded-md bg-gray-300 px-4" wire:model.live.debounce.300ms="projectPath"
            placeholder="Project Path">
    </form>

    <span wire:loading wire:target="getLogs">loading...</span>

    <div class="mt-6 space-y-2">
        @foreach ($logs as $log)
            <div>
                {{-- <div class="mb-1 inline-flex overflow-hidden rounded-lg">
                    <div class="bg-slate-600 px-2 py-1 text-gray-300">{{ $log['env'] }}</div>
                    <div class="bg-slate-600 px-2 py-1 text-gray-300">{{ $log['type'] }}</div>
                </div> --}}
                <div class="overflow-hidden rounded-lg bg-slate-700 text-gray-300" x-data>
                    <div class="flex items-center bg-slate-800 py-1">
                        <div class="px-4 py-2">
                            {{ $log['timestamp'] }}
                        </div>
                        <div class="px-3 py-2">
                            @php
                                $typeClass = $log['type'] === 'ERROR' ? 'bg-red-500' : 'bg-blue-500';
                            @endphp

                            <span
                                class="{{ $typeClass }} block rounded-full px-3 py-0.5 font-medium">{{ $log['env'] }}.{{ $log['type'] }}
                            </span>
                        </div>
                    </div>
                    <div class="overflow-x-auto p-3 text-base">
                        <pre x-html="`{{ $log['log'] }}`"></pre>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
