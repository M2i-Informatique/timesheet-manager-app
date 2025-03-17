<!-- resources/views/livewire/components/data-table.blade.php -->
<div>
    <div class="flex justify-between gap-1 p-5 text-lg text-left rtl:text-right text-blue-600 bg-white">
        @if ($title || $description)
            <div class="flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>

                <p class="text-sm font-normal">
                    {{ $description }}
                </p>
            </div>
        @endif

        @if ($hasSearch)
            <div class="w-64">
                <x-forms.floating-input type="search" label="Rechercher" inputClass="py-1.5"
                    labelClass="bg-white pointer-events-auto" id="search-input-{{ $this->id }}"
                    wire:model.live.debounce.300ms="search" wire:ignore.self>
                    <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>
                </x-forms.floating-input>
            </div>
        @endif
    </div>

    <div class="relative overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    @foreach ($columns as $key => $column)
                        <th scope="col" class="px-6 py-3 cursor-pointer" wire:click="sortBy('{{ $key }}')">
                            {{ $column }}
                            @if ($sortField === $key)
                                <span class="ml-1">
                                    @if ($sortDirection === 'asc')
                                        &#8593;
                                    @else
                                        &#8595;
                                    @endif
                                </span>
                            @endif
                        </th>
                    @endforeach
                    @if (count($actions) > 0)
                        <th scope="col" class="px-6 py-3">
                            Actions
                        </th>
                    @endif
                </tr>
            </thead>

            <tbody>
                @forelse ($data as $item)
                    <tr class="bg-white hover:bg-gray-50">
                        @foreach ($columns as $key => $column)
                            <td
                                class="px-6 py-4 @if ($loop->first) font-medium text-gray-900 whitespace-nowrap @endif">
                                {{ $item->{$key} }}
                            </td>
                        @endforeach

                        @if (count($actions) > 0)
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    @foreach ($actions as $action)
                                        <button wire:click="callAction('{{ $action['method'] }}', {{ $item->id }})"
                                            class="font-medium text-blue-600 hover:text-blue-800 cursor-pointer"
                                            title="{{ $action['title'] ?? '' }}">
                                            {!! $action['icon'] !!}
                                        </button>
                                    @endforeach
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr class="bg-white">
                        <td colspan="{{ count($columns) + (count($actions) > 0 ? 1 : 0) }}"
                            class="px-6 py-4 text-center text-gray-500">
                            Aucune donnée trouvée
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-gray-50">
        {{ $data->links('vendor.livewire.custom-pagination') }}
    </div>
</div>
