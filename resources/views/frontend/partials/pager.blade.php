@if($paginator->hasPages())
    <nav class="inline-flex items-center gap-2 rounded-xl bg-white px-2 py-2 shadow-sm" aria-label="Pagination">
        @if($paginator->onFirstPage())
            <span class="rounded-lg bg-[#ab021c]/10 px-3 py-2 text-xs font-bold text-[#ab021c]/55">Sebelumnya</span>
        @else
            <a class="rounded-lg bg-[#ab021c]/10 px-3 py-2 text-xs font-bold text-[#ab021c] transition hover:bg-[#ab021c] hover:text-white" href="{{ $paginator->previousPageUrl() }}">
                Sebelumnya
            </a>
        @endif

        <span class="rounded-lg bg-[#ab021c]/10 px-3 py-2 text-xs font-bold text-[#ab021c]">
            Halaman {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
        </span>

        @if($paginator->hasMorePages())
            <a class="rounded-lg bg-[#ab021c]/10 px-3 py-2 text-xs font-bold text-[#ab021c] transition hover:bg-[#ab021c] hover:text-white" href="{{ $paginator->nextPageUrl() }}">
                Berikutnya
            </a>
        @else
            <span class="rounded-lg bg-[#ab021c]/10 px-3 py-2 text-xs font-bold text-[#ab021c]/55">Berikutnya</span>
        @endif
    </nav>
@endif
