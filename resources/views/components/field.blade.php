@props([
    'name',
    'id' => null,
    'label' => null,
    'type' => 'text',
    'value' => null,
    'icon' => null,
    'hint' => null,
    'bag' => 'default',
])

@php
    $id = $id ?? $name;
    $galat = $errors->getBag($bag);

    // Nilai non-skalar (mis. ?q[]=x) dianggap kosong supaya tidak error
    $nilai = old($name, $value);
    $nilai = is_scalar($nilai) ? $nilai : '';
@endphp

<div class="form-group">
    @if ($label)
        <label for="{{ $id }}">{{ $label }}@if ($attributes->has('required')) <span class="text-danger">*</span>@endif</label>
    @endif

    <div class="input-group">
        <input type="{{ $type }}" name="{{ $name }}" id="{{ $id }}"
            @unless ($type === 'password') value="{{ $nilai }}" @endunless
            {{ $attributes->class(['form-control', 'is-invalid' => $galat->has($name)]) }}>

        @if ($icon)
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas {{ $icon }}"></span></div>
            </div>
        @endif
    </div>

    @if ($galat->has($name))
        <span class="invalid-feedback d-block" role="alert">{{ $galat->first($name) }}</span>
    @endif

    @if ($hint)
        <small class="form-text text-muted">{{ $hint }}</small>
    @endif
</div>
