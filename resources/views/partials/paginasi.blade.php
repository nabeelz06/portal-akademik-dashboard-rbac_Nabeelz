{{-- Ringkasan jumlah data dan tautan halaman. Variabel: $data (paginator) --}}
<div class="card-footer d-flex flex-column flex-md-row justify-content-between align-items-center">
    <small class="text-muted mb-2 mb-md-0">
        @if ($data->total() > 0)
            Menampilkan {{ $data->firstItem() }} sampai {{ $data->lastItem() }} dari {{ $data->total() }} data
        @else
            Tidak ada data
        @endif
    </small>

    {{ $data->links() }}
</div>
