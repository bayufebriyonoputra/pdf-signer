@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-6 text-sm text-gray-700 dark:text-gray-300">
            {{-- Info --}}
            <div>
                Menampilkan
                <span class="font-semibold">{{ $paginator->firstItem() }}</span>
                sampai
                <span class="font-semibold">{{ $paginator->lastItem() }}</span>
                dari
                <span class="font-semibold">{{ $paginator->total() }}</span>
                entri
            </div>

            {{-- Pagination --}}
            <div class="flex items-center space-x-1 rtl:space-x-reverse">
                {{-- Previous Page --}}
                @if ($paginator->onFirstPage())
                    <button disabled
                        class="px-3 py-1 rounded-md border border-gray-300 text-gray-400 bg-white cursor-not-allowed dark:bg-gray-800 dark:border-gray-600">
                        &laquo;
                    </button>
                @else
                    <button type="button"
                        wire:click="previousPage('{{ $paginator->getPageName() }}')"
                        x-on:click="{{ $scrollIntoViewJsSnippet }}"
                        class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                        &laquo;
                    </button>
                @endif

                {{-- Page Links --}}
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="px-3 py-1 text-gray-500">…</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span
                                    class="px-3 py-1 rounded-md border border-indigo-600 bg-indigo-600 text-white font-medium shadow">
                                    {{ $page }}
                                </span>
                            @else
                                <button type="button"
                                    wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                    x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                    class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                                    {{ $page }}
                                </button>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page --}}
                @if ($paginator->hasMorePages())
                    <button type="button"
                        wire:click="nextPage('{{ $paginator->getPageName() }}')"
                        x-on:click="{{ $scrollIntoViewJsSnippet }}"
                        class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                        &raquo;
                    </button>
                @else
                    <button disabled
                        class="px-3 py-1 rounded-md border border-gray-300 text-gray-400 bg-white cursor-not-allowed dark:bg-gray-800 dark:border-gray-600">
                        &raquo;
                    </button>
                @endif
            </div>
        </div>
    @endif
</div>
