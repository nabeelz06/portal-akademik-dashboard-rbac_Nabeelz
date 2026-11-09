{{-- Pesan sukses atau galat dari redirect (session flash) --}}
@foreach (['success' => 'success', 'error' => 'danger'] as $kunci => $kelas)
    @if (session($kunci))
        <div class="alert alert-{{ $kelas }} alert-dismissible fade show" role="alert">
            <i class="fas {{ $kunci === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' }} mr-1"></i>
            {{ session($kunci) }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
@endforeach
