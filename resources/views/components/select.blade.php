@props([
    'name',
    'options' => [],
    'id' => null,
    'label' => null,
    'value' => null,
    'placeholder' => null,
    'hint' => null,
])

@php
    $id = $id ?? $name;
    $mentah = old($name, $value);
    $dipilih = is_scalar($mentah) ? (string) $mentah : '';
@endphp

<div class="form-group">
    @if ($label)
        <label for="{{ $id }}">{{ $label }}@if ($attributes->has('required')) <span class="text-danger">*</span>@endif</label>
    @endif

    <select name="{{ $name }}" id="{{ $id }}"
        {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($name)]) }}>
        @if ($placeholder !== null)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($options as $nilai => $teks)
            <option value="{{ $nilai }}" @selected($dipilih !== '' && $dipilih === (string) $nilai)>{{ $teks }}</option>
        @endforeach
    </select>

    @error($name)
        <span class="invalid-feedback d-block" role="alert">{{ $message }}</span>
    @enderror

    @if ($hint)
        <small class="form-text text-muted">{{ $hint }}</small>
    @endif
</div>
