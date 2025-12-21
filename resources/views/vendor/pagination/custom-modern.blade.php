@if ($paginator->hasPages())
    <nav>
        <ul class="pagination mb-0" style="gap: 6px;">

            {{-- Tombol PREV --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link" style="border-radius: 8px;">Prev</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" style="border-radius: 8px;" href="{{ $paginator->previousPageUrl() }}">Prev</a>
                </li>
            @endif

            {{-- Nomor Halaman --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled">
                        <span class="page-link" style="border-radius: 8px;">{{ $element }}</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active">
                                <span class="page-link" style="border-radius: 8px; background:#2563eb; border-color:#2563eb;">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" style="border-radius: 8px;" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol NEXT --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" style="border-radius: 8px;" href="{{ $paginator->nextPageUrl() }}">Next</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link" style="border-radius: 8px;">Next</span>
                </li>
            @endif

        </ul>
    </nav>
@endif
