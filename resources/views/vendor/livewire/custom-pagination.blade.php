@if ($paginator->hasPages())
    <div class="flex items-center justify-between mt-4">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-2 bg-gray-200 rounded text-gray-500">←</span>
        @else
            <button wire:click="previousPage" class="px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">←</button>
        @endif

        {{-- Page Number --}}
        <span class="mx-3 text-sm text-gray-700">
            Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}
        </span>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <button wire:click="nextPage" class="px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">→</button>
        @else
            <span class="px-3 py-2 bg-gray-200 rounded text-gray-500">→</span>
        @endif
    </div>
@endif
