<x-layout>
    <article class="single-post">
      <a href="{{ route('blog.show', $blog) }}">← {{ $blog->title }}</a>
      <img src="{{ $post->image_url }}" alt="{{ $post->title }}">
      <p class="meta">{{ $post->category->name }} · {{ $post->read_time }} min read · {{ $post->published_at?->format('M j, Y') }}</p>
      <h1>{{ $post->title }}</h1>
      <div class="post-body">{!! $post->body !!}</div>
    </article>
  </x-layout>