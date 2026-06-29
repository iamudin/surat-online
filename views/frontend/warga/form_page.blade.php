<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Form Pengajuan - {{ $type->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f3f4f6;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            padding-bottom: 80px;
        }
    </style>
</head>

<body class="antialiased text-gray-900">
    <!-- Top App Bar -->
    <div
        class="fixed top-0 left-0 right-0 bg-blue-600 text-white shadow-md z-40 px-4 py-4 flex justify-between items-center safe-top">
        <div class="text-lg font-semibold truncate">
            <a href="{{ route('portal.dashboard') }}" class="mr-2 hover:text-blue-200"><i
                    class="fa fa-arrow-left"></i></a>
            Pengajuan: {{ $type->name }}
        </div>
        <div class="flex items-center space-x-3">
            <form action="{{ route('portal.logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-white hover:text-gray-200 focus:outline-none">
                    <i class="fa fa-sign-out-alt text-xl"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="pt-20 px-4 max-w-lg mx-auto sm:max-w-2xl lg:max-w-4xl pb-10">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 mb-8 relative overflow-hidden">
            @include('surat-online::frontend.warga.form_ajax')
        </div>
    </div>

    <script>
        // Handle dynamic array rows via event delegation
        document.addEventListener('click', function (e) {
            let addBtn = e.target.closest('.btn-add-row');
            if (addBtn) {
                let fieldId = addBtn.getAttribute('data-field-id');
                let columnsStr = addBtn.getAttribute('data-columns');
                let isRequired = addBtn.getAttribute('data-required') === '1' ? 'required' : '';

                let columns = [];
                if (columnsStr) {
                    try { columns = JSON.parse(columnsStr); } catch (e) { }
                }

                let tbody = document.getElementById('tbody_array_' + fieldId);
                let newIndex = Date.now(); // unique index

                let divRow = document.createElement('div');
                divRow.className = 'bg-white p-3 rounded-lg border border-gray-200 relative array-row mt-4';

                let html = '<button type="button" class="absolute top-2 right-2 text-red-400 hover:text-red-600 p-1 btn-remove-row" title="Hapus"><i class="fa fa-times"></i></button>';
                html += '<div class="space-y-3 sm:space-y-0 sm:flex sm:gap-3 pr-6">';
                columns.forEach(function (col) {
                    html += `<div class="flex-1">
                                <label class="block text-xs text-gray-500 mb-1">${col}</label>
                                <input type="text" name="field_${fieldId}[${newIndex}][${col}]" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" ${isRequired}>
                             </div>`;
                });
                html += '</div>';

                divRow.innerHTML = html;
                tbody.appendChild(divRow);
            }

            let removeBtn = e.target.closest('.btn-remove-row');
            if (removeBtn) {
                removeBtn.closest('.array-row').remove();
            }
        });

        // Handle AJAX Submit inside the new page
        document.addEventListener('submit', function (e) {
            if (e.target && e.target.id === 'dynamicSuratForm') {
                e.preventDefault();
                let form = e.target;
                let submitBtn = form.querySelector('button[type="submit"]');
                let originalBtnText = submitBtn.innerHTML;

                // Cek file size (Max 1MB)
                let files = form.querySelectorAll('input[type="file"]');
                let isFileSizeValid = true;
                files.forEach(function (fileInput) {
                    if (fileInput.files.length > 0) {
                        let sizeMB = fileInput.files[0].size / 1024 / 1024;
                        if (sizeMB > 1) {
                            isFileSizeValid = false;
                        }
                    }
                });

                if (!isFileSizeValid) {
                    Swal.fire('Peringatan', 'Ukuran file maksimal 1MB per file.', 'warning');
                    return;
                }

                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i> Memproses...';
                submitBtn.disabled = true;

                let formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json().then(data => ({ status: response.status, body: data })))
                    .then(res => {
                        if (res.status === 200 && res.body.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                html: 'Permohonan berhasil dikirim.<br><br><span class="text-sm text-gray-500">Nomor Tiket:</span><br><b class="text-xl text-blue-600 tracking-wider">' + res.body.ticket + '</b>',
                                icon: 'success',
                                confirmButtonText: 'Tutup',
                                customClass: {
                                    confirmButton: 'bg-blue-600 text-white rounded-xl px-6 py-2'
                                }
                            }).then(() => {
                                window.location.href = "{{ route('portal.dashboard') }}";
                            });
                        } else if (res.status === 422) {
                            let errors = res.body.errors;
                            let errorMsg = '<ul class="text-left text-sm space-y-1">';
                            for (let key in errors) {
                                errorMsg += '<li><i class="fa fa-times-circle text-red-500 mr-1"></i> ' + errors[key][0] + '</li>';
                            }
                            errorMsg += '</ul>';
                            Swal.fire({ title: 'Validasi Gagal', html: errorMsg, icon: 'error' });
                            submitBtn.innerHTML = originalBtnText;
                            submitBtn.disabled = false;
                        } else {
                            Swal.fire('Error', res.body.message || 'Terjadi kesalahan sistem.', 'error');
                            submitBtn.innerHTML = originalBtnText;
                            submitBtn.disabled = false;
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'Gagal terhubung ke server.', 'error');
                        submitBtn.innerHTML = originalBtnText;
                        submitBtn.disabled = false;
                    });
            }
        });
    </script>
</body>

</html>