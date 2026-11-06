<x-layout>
    <div class="blogs-page">
      <h1>Explore blogs</h1>
  
      @forelse ($blogs as $blog)
        <a href="{{ route('blog.show', $blog) }}" class="blog-card">
          <h2>{{ $blog->title }}</h2>
          <p>by {{ $blog->user->name }} · {{ $blog->published_count }} {{ Str::plural('post', $blog->published_count) }}</p>
          <span>@ {{ $blog->subdomain }}</span>
        </a>
      @empty
        <p>No blogs yet.</p>
      @endforelse
  
      {{ $blogs->links() }}
    </div>
  </x-layout>