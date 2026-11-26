<x-admin-layout title="FAQ">

    @include('admin.pages._tabs')
  
    @if (session('success'))
      <div class="adm-alert adm-ok">{{ session('success') }}</div>
    @endif
  
    @if ($errors->any())
      <div class="adm-alert adm-err" role="alert">
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
      </div>
    @endif
  
    <form method="POST" action="{{ route('admin.pages.faq.save') }}">
      @csrf
      @method('PUT')
  
      <section class="db-panel">
        <div class="db-panel-head"><h3><span class="db-p-icon">❓</span> Frequently asked questions</h3></div>
        <div class="adm-actions">
          <div>
            <label class="adm-label" for="title">Section title</label>
            <input type="text" id="title" name="title" class="adm-input" maxlength="100" value="{{ old('title', $c['title']) }}">
          </div>
          <div>
            <label class="adm-label" for="subtitle">Line under the title</label>
            <input type="text" id="subtitle" name="subtitle" class="adm-input" maxlength="200" value="{{ old('subtitle', $c['subtitle']) }}">
          </div>
  
          @include('admin.pages._list', [
              'name'     => 'items',
              'rows'     => $c['items'] ?? [],
              'fields'   => [
                  ['key' => 'q', 'label' => 'Question', 'max' => 160],
                  ['key' => 'a', 'label' => 'Answer', 'type' => 'textarea', 'rows' => 4, 'max' => 1000],
              ],
              'addLabel' => 'Add a question',
              'max'      => 20,
          ])
  
          <p>Use the arrows to change the order. This section appears at the bottom of the Contact page. If you remove every question, the section is hidden.</p>
        </div>
      </section>
  
      <div style="margin-top:20px;">
        <button type="submit" class="adm-btn adm-btn-primary">Save FAQ</button>
      </div>
    </form>
  
    <form method="POST" action="{{ route('admin.pages.reset', 'faq') }}" style="margin-top:14px;"
          onsubmit="return confirm('Put the original questions back? Your changes will be lost.')">
      @csrf
      @method('DELETE')
      <button type="submit" class="adm-btn">Put the original questions back</button>
    </form>
  
  </x-admin-layout>