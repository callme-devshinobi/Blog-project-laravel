@php $fieldName = fn ($f) => $name . '[' . $i . '][' . $f['key'] . ']'; @endphp

<div class="rp-row">
  <div class="rp-fields">
    @foreach ($fields as $f)
      @php
        $type = $f['type'] ?? 'text';
        $val  = old($name . '.' . $i . '.' . $f['key'], $row[$f['key']] ?? '');
      @endphp
      <div>
        <label>{{ $f['label'] }}</label>
        @if ($type === 'textarea')
          <textarea name="{{ $fieldName($f) }}" class="adm-input" rows="{{ $f['rows'] ?? 3 }}" maxlength="{{ $f['max'] ?? 500 }}">{{ $val }}</textarea>
        @else
          <input type="{{ $type }}" name="{{ $fieldName($f) }}" class="adm-input" maxlength="{{ $f['max'] ?? 200 }}"
                 value="{{ $val }}" placeholder="{{ $f['placeholder'] ?? '' }}">
        @endif
      </div>
    @endforeach
  </div>

  <div class="rp-ctrl">
    <button type="button" class="rp-up" aria-label="Move up">↑</button>
    <button type="button" class="rp-down" aria-label="Move down">↓</button>
    <button type="button" class="rp-remove" aria-label="Remove">✕</button>
  </div>
</div>