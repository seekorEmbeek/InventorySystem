<div class="form-group">
    <select class="form-control select2" name="{{ $name }}" id="{{ $id }}" required>
        <option value="" disabled selected>--Pilih Satuan--</option> <!-- Placeholder -->
        @foreach($uoms as $uom)
        <option value="{{ $uom }}" {{ $selected == $uom ? 'selected' : '' }}>
            {{ strtoupper($uom) }}
        </option>
        @endforeach
    </select>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/select2/css/select2.min.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('vendor/adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#{{ $id }}').select2({
            theme: 'bootstrap4',
            placeholder: "-- Pilih Satuan --", // Placeholder
            allowClear: true // Allow clearing selection
        }).val("{{ $selected }}").trigger('change'); // Set selected value
    });
</script>
@endpush