@extends('cms::backend.layout.app', ['title' => 'Pengaturan Surat Online'])
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <h3 class="tile-title">Pengaturan Plugin: Surat Online</h3>
            <div class="tile-body">
                <form action="{{ route('admin.surat.settings.store') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="custom_domain" class="form-label">Domain Khusus (Custom Domain)</label>
                        <input type="text" class="form-control" id="custom_domain" name="custom_domain" 
                               value="{{ old('custom_domain', $customDomain) }}" 
                               placeholder="Contoh: pelayanan.namadesa.com">
                        <small class="text-muted d-block mt-1">
                            Biarkan kosong jika Anda ingin menggunakan jalur default (<code>{{ url('surat-online') }}</code>). <br>
                            Jika diisi, semua akses portal Surat Online akan dialihkan ke domain ini. <b>Pastikan domain ini sudah diarahkan (DNS A Record) ke server ini.</b>
                        </small>
                    </div>

                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan Pengaturan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
