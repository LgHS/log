<html>
<body>
@foreach ($posts as $post)
    <article>
        <header>
            <h1><a href="{{ route('show', $post->id) }}">{{ $post->title }}</a></h1>
            <p>Écrit par <span class="author">{{ $post->author_id }}</span></p>
        </header>

        <footer>
            @if($post->tags)
            <section class="tags">
                <h2>Tags</h2>
                <ul>
                    @foreach ($post->tags as $tag)
                        <li><a href="/tag/tag1">{{ $tag->name }}</a></li>
                    @endforeach
                </ul>
            </section>
            @endif

            @if($post->media)
            <section class="media">
                <h2>Galerie de médias</h2>

                @foreach ($post->media as $media)
                    <figure>
                        <img src="{{ $media->id }}.{{ $media->extension }}" alt=""/>
                    </figure>
                @endforeach
            </section>
            @endif
        </footer>
    </article>
@endforeach
</body>
</html>
