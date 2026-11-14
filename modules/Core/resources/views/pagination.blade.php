@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-between gap-4">
        <p class="text-sm text-navy-500">
            Showing <span class="font-medium text-navy-800">{{ $paginator->firstItem() }}</span>–<span class="font-medium text-navy-800">{{ $paginator->lastItem() }}</span>
            of <span class="font-medium text-navy-800">{{ $paginator->total() }}</span>
        </p>
        <ul class="flex items-center gap-1" role="list">
            <li>
                @if ($paginator->onFirstPage())
                    <span class="inline-flex size-9 items-center justify-center rounded-lg text-navy-300" aria-disabled="true"><x-icon name="chevron-left" class="size-4" /><span class="sr-only">Previous</span></span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex size-9 items-center justify-center rounded-lg text-navy-600 ring-1 ring-navy-100 hover:bg-navy-50"><x-icon name="chevron-left" class="size-4" /><span class="sr-only">Previous</span></a>
                @endif
            </li>
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="hidden sm:block"><span class="inline-flex size-9 items-center justify-center text-sm text-navy-400">{{ $element }}</span></li>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li class="hidden sm:block">
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="inline-flex size-9 items-center justify-center rounded-lg bg-navy-900 text-sm font-semibold text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="inline-flex size-9 items-center justify-center rounded-lg text-sm text-navy-600 hover:bg-navy-50">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach
            <li>
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex size-9 items-center justify-center rounded-lg text-navy-600 ring-1 ring-navy-100 hover:bg-navy-50"><x-icon name="chevron-right" class="size-4" /><span class="sr-only">Next</span></a>
                @else
                    <span class="inline-flex size-9 items-center justify-center rounded-lg text-navy-300" aria-disabled="true"><x-icon name="chevron-right" class="size-4" /><span class="sr-only">Next</span></span>
                @endif
            </li>
        </ul>
    </nav>
@endif
