<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat {{ $req->type->name }} - {{ $req->ticket_number }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; padding: 2cm; }
        .header { text-align: center; border-bottom: 3px solid #000; margin-bottom: 20px; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16pt; text-transform: uppercase; }
        .header h2 { margin: 0; font-size: 14pt; }
        .header p { margin: 0; font-size: 10pt; }
        .title { text-align: center; margin-bottom: 20px; }
        .title h3 { margin: 0; font-size: 14pt; text-decoration: underline; text-transform: uppercase; }
        .title p { margin: 0; }
        .content { margin-bottom: 30px; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data td { padding: 4px; vertical-align: top; }
        table.data td:first-child { width: 30%; }
        table.data td:nth-child(2) { width: 2%; }
        .signature { width: 100%; margin-top: 50px; }
        .signature table { width: 100%; }
        .signature td { width: 50%; text-align: center; vertical-align: bottom; }
        .tte-stamp { border: 2px solid #000; padding: 10px; display: inline-block; color: #000; font-weight: bold; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>PEMERINTAH KABUPATEN NAMA_KABUPATEN</h2>
        <h2>KECAMATAN NAMA_KECAMATAN</h2>
        <h1>DESA NAMA_DESA</h1>
        <p>Alamat: Jl. Raya Desa No. 1, Kode Pos: 12345</p>
    </div>

    <div class="title">
        <h3>{{ $req->type->name }}</h3>
        <p>Nomor: ..... / ..... / ..... / {{ date('Y') }}</p>
    </div>

    <div class="content">
        <p>Yang bertanda tangan di bawah ini Kepala Desa Nama_Desa, Kecamatan Nama_Kecamatan, Kabupaten Nama_Kabupaten, menerangkan dengan sebenarnya bahwa:</p>
        
        <table class="data">
            <tr>
                <td>Nama Lengkap</td>
                <td>:</td>
                <td>{{ $req->warga->name }}</td>
            </tr>
            <tr>
                <td>NIK</td>
                <td>:</td>
                <td>{{ $req->warga->nik }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>RT {{ $req->warga->rt->nomor_rt ?? '-' }} / RW {{ $req->warga->rw->nomor_rw ?? '-' }}</td>
            </tr>
            @foreach($req->data as $data)
            <tr>
                <td>{{ $req->type->fields->where('id', $data->surat_field_id)->first()->field_label ?? 'Data' }}</td>
                <td>:</td>
                <td>
                    @if(is_array(json_decode($data->field_value, true)))
                        (Data Tabel Terlampir)
                    @else
                        {{ $data->field_value }}
                    @endif
                </td>
            </tr>
            @endforeach
        </table>

        <p>Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    <div class="signature">
        <table>
            <tr>
                <td></td>
                <td>
                    <p>Nama_Desa, {{ date('d F Y') }}</p>
                    <p>Kepala Desa Nama_Desa</p>
                    <br><br>
                    @if($is_tte)
                        <div class="tte-stamp">
                            Ditandatangani secara elektronik (TTE)<br>
                            Oleh: Kepala Desa
                        </div>
                    @else
                        <br><br><br>
                    @endif
                    <p><strong>_____________________</strong></p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
