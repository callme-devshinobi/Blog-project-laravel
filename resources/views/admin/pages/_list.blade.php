@php $rows = array_values($rows); @endphp

<div class="rp" data-max="{{ $max }}">
  <div class="rp-rows">
    @foreach ($rows as $i => $row)
      @include('admin.pages._row', ['name' => $name, 'i' => $i, 'row' => $row, 'fields' => $fields])
    @endforeach
  </div>

  <template class="rp-template">
    @include('admin.pages._row', ['name' => $name, 'i' => '__i__', 'row' => [], 'fields' => $fields])
  </template>

  <button type="button" class="adm-btn rp-add">＋ {{ $addLabel }}</button>
</div>

@once
  <style>
    .rp-row { display: flex; gap: 10px; align-items: flex-start; border: 1px solid var(--border); border-radius: 10px;
              padding: 12px; margin-bottom: 10px; background: var(--bg-light); }
    .rp-fields { flex: 1; display: flex; flex-direction: column; gap: 8px; min-width: 0; }
    .rp-fields label { display: block; font-size: 12.5px; font-weight: 600; margin-bottom: 4px; }
    .rp-fields .adm-input { margin-bottom: 0; }
    .rp-ctrl { display: flex; flex-direction: column; gap: 6px; }
    .rp-ctrl button { border: 1px solid var(--border); background: var(--card); color: var(--text); border-radius: 8px;
                      padding: 4px 10px; cursor: pointer; font-family: inherit; }
    .rp-ctrl .rp-remove { color: #dc2626; }
    .rp-add[disabled] { opacity: .5; cursor: not-allowed; }
  </style>

  <script>
    document.querySelectorAll('.rp').forEach(function (rp) {
      var rows = rp.querySelector('.rp-rows');
      var tpl  = rp.querySelector('.rp-template');
      var add  = rp.querySelector('.rp-add');
      var max  = parseInt(rp.dataset.max || '10', 10);
      var next = Date.now();

      function sync() { add.disabled = rows.children.length >= max; }

      add.addEventListener('click', function () {
        if (rows.children.length >= max) return;
        rows.insertAdjacentHTML('beforeend', tpl.innerHTML.split('__i__').join(String(next++)));
        sync();
      });

      rp.addEventListener('click', function (e) {
        var row = e.target.closest('.rp-row');
        if (!row) return;

        if (e.target.closest('.rp-remove')) {
          row.remove(); sync();
        } else if (e.target.closest('.rp-up') && row.previousElementSibling) {
          rows.insertBefore(row, row.previousElementSibling);
        } else if (e.target.closest('.rp-down') && row.nextElementSibling) {
          rows.insertBefore(row.nextElementSibling, row);
        }
      });

      sync();
    });
  </script>
@endonce