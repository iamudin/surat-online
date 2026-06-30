<form id="dynamicSuratForm" action="{{ plugin_route('warga.form.store', $type->slug) }}" method="POST"
    enctype="multipart/form-data" class="space-y-4">
    @csrf

    <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl text-sm mb-4">
        <strong>Informasi:</strong> Form ini dibuat secara dinamis berdasarkan jenis surat
        <strong>{{ $type->name }}</strong>.
    </div>

    @foreach($type->fields as $field)

        @if($field->field_type == 'break')
            <div class="pt-4 pb-2 border-b border-gray-200 mt-6">
                <h4 class="text-md font-bold text-blue-800"><i class="fa fa-folder-open mr-2"></i>{{ $field->field_name }}</h4>
                @if($field->description)
                    <p class="text-xs text-gray-500 mt-1">{{ $field->description }}</p>
                @endif
            </div>
            @continue
        @endif

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                {{ $field->field_name }}
                @if($field->is_required) <span class="text-red-500">*</span> @endif
            </label>

            @if($field->description)
                <p class="text-xs text-gray-500 mb-2">{{ $field->description }}</p>
            @endif

            @if($field->field_type == 'textarea')
                <textarea name="field_{{ $field->id }}"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    rows="3" {{ $field->is_required ? 'required' : '' }}></textarea>

            @elseif($field->field_type == 'select')
                <select name="field_{{ $field->id }}"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all appearance-none"
                    {{ $field->is_required ? 'required' : '' }}>
                    <option value="">-- Pilih --</option>
                    @if($field->field_options)
                        @foreach($field->field_options as $opt)
                            <option value="{{ trim($opt) }}">{{ trim($opt) }}</option>
                        @endforeach
                    @endif
                </select>

            @elseif($field->field_type == 'file')
                <div class="mt-1">
                    <input type="file" name="field_{{ $field->id }}"
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all cursor-pointer"
                        {{ $field->is_required ? 'required' : '' }} accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <p class="text-xs text-gray-500 mt-2">Maksimal 1MB (PDF/JPG/PNG)</p>

            @elseif($field->field_type == 'array')
                <div class="border rounded-xl p-4 bg-gray-50 shadow-inner">
                    <div id="tbody_array_{{ $field->id }}" class="space-y-4">
                        <div class="bg-white p-3 rounded-lg border border-gray-200 relative array-row">
                            <button type="button"
                                class="absolute top-2 right-2 text-red-400 hover:text-red-600 p-1 btn-remove-row"
                                title="Hapus"><i class="fa fa-times"></i></button>
                            <div class="space-y-3 sm:space-y-0 sm:flex sm:gap-3 pr-6">
                                @if($field->field_options)
                                    @foreach($field->field_options as $col)
                                        <div class="flex-1">
                                            <label class="block text-xs text-gray-500 mb-1">{{ trim($col) }}</label>
                                            <input type="text" name="field_{{ $field->id }}[0][{{ trim($col) }}]"
                                                class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                {{ $field->is_required ? 'required' : '' }}>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    <button type="button"
                        class="mt-4 w-full px-4 py-2 text-sm bg-blue-50 text-blue-600 rounded-lg font-semibold flex items-center justify-center hover:bg-blue-100 transition-colors btn-add-row"
                        data-field-id="{{ $field->id }}"
                        data-columns="{{ json_encode(array_map('trim', $field->field_options ?? [])) }}"
                        data-required="{{ $field->is_required ? '1' : '0' }}">
                        <i class="fa fa-plus mr-1.5"></i> Tambah Data
                    </button>
                </div>
            @else
                <!-- text, number, date -->
                <input type="{{ $field->field_type }}" name="field_{{ $field->id }}"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    {{ $field->is_required ? 'required' : '' }}>
            @endif
        </div>
    @endforeach

    <div class="pt-4 border-t border-gray-100 mt-6 flex justify-end gap-3">
        <a href="{{ plugin_route('portal.dashboard') }}"
            class="px-5 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-700 text-sm font-medium hover:bg-gray-50 transition-colors">Batal</a>
        <button type="submit"
            class="px-5 py-2.5 bg-blue-600 rounded-xl text-white text-sm font-medium hover:bg-blue-700 transition-colors flex items-center shadow-sm">
            <i class="fa fa-paper-plane mr-2"></i> Ajukan Permohonan
        </button>
    </div>
</form>