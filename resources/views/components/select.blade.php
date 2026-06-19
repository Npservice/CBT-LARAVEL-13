@props([
    'id' => 'select-' . uniqid(),
    'name' => '',
    'label' => '',
    'placeholder' => 'Pilih...',
    'options' => [],
    'selected' => null,
    'multiple' => false,
    'apiUrl' => null,
    'apiValueField' => 'id',
    'apiTextField' => 'name',
    'required' => false,
    'disabled' => false,
    'class' => '',
    'clearable' => true
])

@php
    $isApi = !empty($apiUrl);
    $selectClass = 'form-select select2-input ' . $class;
@endphp

<!-- Select Field -->
<div class="space-y-2">
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-slate-700">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <select
        id="{{ $id }}"
        name="{{ $name }}"
        class="{{ $selectClass }}"
        @if ($multiple) multiple @endif
        @if ($required) required @endif
        @if ($disabled) disabled @endif
        data-api-url="{{ $apiUrl }}"
        data-api-value="{{ $apiValueField }}"
        data-api-text="{{ $apiTextField }}"
        data-is-api="{{ $isApi ? 'true' : 'false' }}"
    >
        @if (!$isApi && !$multiple)
            <option value="">{{ $placeholder }}</option>
        @endif

        @if (!$isApi && !empty($options))
            @foreach ($options as $value => $text)
                <option value="{{ $value }}" @selected($selected === $value)>
                    {{ $text }}
                </option>
            @endforeach
        @endif
    </select>
</div>

<!-- Initialize Select2 -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const $select = $('#{{ $id }}');
        const isApi = $select.data('is-api');
        const isMultiple = {{ $multiple ? 'true' : 'false' }};
        const clearable = {{ $clearable ? 'true' : 'false' }};

        if (isApi) {
            const apiUrl = $select.data('api-url');
            if (isMultiple) {
                Select2Helper.initMultipleApi('#{{ $id }}', apiUrl, {
                    allowClear: clearable
                });
            } else {
                Select2Helper.initApi('#{{ $id }}', apiUrl, {
                    allowClear: clearable
                });
            }
        } else {
            if (isMultiple) {
                Select2Helper.initMultiple('#{{ $id }}', {
                    allowClear: clearable
                });
            } else {
                Select2Helper.init('#{{ $id }}', {
                    allowClear: clearable
                });
            }
        }
    });
</script>
