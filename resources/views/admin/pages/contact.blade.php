<x-admin-layout title="Contact page">

    @include('admin.pages._tabs')
  
    @if (session('success'))
      <div class="adm-alert adm-ok">{{ session('success') }}</div>
    @endif
  
    @if ($errors->any())
      <div class="adm-alert adm-err" role="alert">
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
      </div>
    @endif
  
    <form method="POST" action="{{ route('admin.pages.contact.save') }}">
      @csrf
      @method('PUT')
  
      <section class="db-panel">
        <div class="db-panel-head"><h3><span class="db-p-icon">🏷️</span> Top of the page</h3></div>
        <div class="adm-actions">
          <div>
            <label class="adm-label" for="eyebrow">Small line above the title</label>
            <input type="text" id="eyebrow" name="eyebrow" class="adm-input" maxlength="60" value="{{ old('eyebrow', $c['eyebrow']) }}">
          </div>
          <div>
            <label class="adm-label" for="title">Title</label>
            <input type="text" id="title" name="title" class="adm-input" maxlength="100" required value="{{ old('title', $c['title']) }}">
          </div>
          <div>
            <label class="adm-label" for="intro">Introduction</label>
            <textarea id="intro" name="intro" class="adm-input" maxlength="500">{{ old('intro', $c['intro']) }}</textarea>
          </div>
        </div>
      </section>
  
      <section class="db-panel" style="margin-top:24px;">
        <div class="db-panel-head"><h3><span class="db-p-icon">✉</span> Email addresses</h3></div>
        <div class="adm-actions">
          @include('admin.pages._list', [
              'name'     => 'emails',
              'rows'     => $c['emails'] ?? [],
              'fields'   => [
                  ['key' => 'title',   'label' => 'Title', 'max' => 60],
                  ['key' => 'text',    'label' => 'Short text', 'type' => 'textarea', 'max' => 300],
                  ['key' => 'address', 'label' => 'Email address', 'type' => 'email', 'max' => 120],
              ],
              'addLabel' => 'Add an email card',
              'max'      => 5,
          ])
          <div>
            <label class="adm-label" for="response_title">Response time: title</label>
            <input type="text" id="response_title" name="response_title" class="adm-input" maxlength="60" value="{{ old('response_title', $c['response_title']) }}">
          </div>
          <div>
            <label class="adm-label" for="response_text">Response time: text</label>
            <textarea id="response_text" name="response_text" class="adm-input" maxlength="300">{{ old('response_text', $c['response_text']) }}</textarea>
            <p>Leave both empty to hide this card.</p>
          </div>
        </div>
      </section>
  
      <section class="db-panel" style="margin-top:24px;">
        <div class="db-panel-head"><h3><span class="db-p-icon">🔗</span> Social links</h3></div>
        <div class="adm-actions">
          <div>
            <label class="adm-label" for="social_heading">Heading</label>
            <input type="text" id="social_heading" name="social_heading" class="adm-input" maxlength="60" value="{{ old('social_heading', $c['social_heading']) }}">
          </div>
          <div>
            <label class="adm-label" for="social_text">Text</label>
            <textarea id="social_text" name="social_text" class="adm-input" maxlength="300">{{ old('social_text', $c['social_text']) }}</textarea>
          </div>
          @include('admin.pages._list', [
              'name'     => 'social',
              'rows'     => $c['social'] ?? [],
              'fields'   => [
                  ['key' => 'label', 'label' => 'Name (like GitHub)', 'max' => 30],
                  ['key' => 'url',   'label' => 'Link (https://…)', 'max' => 200, 'placeholder' => 'https://github.com/yourname'],
              ],
              'addLabel' => 'Add a link',
              'max'      => 8,
          ])
          <p>Links with no web address aren't shown, and the whole box disappears if none is filled in.</p>
        </div>
      </section>
  
      <section class="db-panel" style="margin-top:24px;">
        <div class="db-panel-head"><h3><span class="db-p-icon">📨</span> Message form</h3></div>
        <div class="adm-actions">
          <div>
            <label class="adm-label" for="form_enabled">Show the message form</label>
            @php $enabled = (string) old('form_enabled', ($c['form_enabled'] ?? true) ? '1' : '0'); @endphp
            <select id="form_enabled" name="form_enabled" class="adm-input">
              <option value="1" @selected($enabled === '1')>Yes</option>
              <option value="0" @selected($enabled === '0')>No, hide it</option>
            </select>
            <p><b>The form doesn't send messages yet.</b> Visitors who press Send get an error page. Hide it until the message inbox is built.</p>
          </div>
          <div>
            <label class="adm-label" for="form_heading">Form heading</label>
            <input type="text" id="form_heading" name="form_heading" class="adm-input" maxlength="60" value="{{ old('form_heading', $c['form_heading']) }}">
          </div>
          <div>
            <label class="adm-label" for="form_note">Small note under the form</label>
            <input type="text" id="form_note" name="form_note" class="adm-input" maxlength="160" value="{{ old('form_note', $c['form_note']) }}">
          </div>
        </div>
      </section>
  
      <div style="margin-top:20px;">
        <button type="submit" class="adm-btn adm-btn-primary">Save Contact page</button>
      </div>
    </form>
  
    <form method="POST" action="{{ route('admin.pages.reset', 'contact') }}" style="margin-top:14px;"
          onsubmit="return confirm('Put the original Contact text back? Your changes to the text will be lost.')">
      @csrf
      @method('DELETE')
      <button type="submit" class="adm-btn">Put the original text back</button>
    </form>
  
  </x-admin-layout>