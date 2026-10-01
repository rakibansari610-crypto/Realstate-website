@if ($paginator->hasPages())

<nav>
<ul class="pagination custom-pagination">

    {{-- Prev Button (hide on first page) --}}
    @if (!$paginator->onFirstPage())
        <li class="page-item">
            <a class="page-link prev-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                Prev
            </a>
        </li>
    @endif


    {{-- Page Numbers --}}
    @foreach ($elements as $element)

        {{-- "..." separator --}}
        @if (is_string($element))
            <li class="page-item disabled">
                <span class="page-link dots">{{ $element }}</span>
            </li>
        @endif

        {{-- Page Links --}}
        @if (is_array($element))
            @foreach ($element as $page => $url)

                @if ($page == $paginator->currentPage())
                    <li class="page-item active">
                        <span class="page-link">{{ $page }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endif

            @endforeach
        @endif

    @endforeach


    {{-- Next Button (hide on last page) --}}
    @if ($paginator->hasMorePages())
        <li class="page-item">
            <a class="page-link next-btn" href="{{ $paginator->nextPageUrl() }}" rel="next">
                Next
            </a>
        </li>
    @endif

</ul>
</nav>

@endif