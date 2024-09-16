<article>
    <header>
        <address class="flex items-center mb-6 not-italic">
            <div class="inline-flex items-center mr-3 text-sm text-gray-900 dark:text-white">
                <div>
                    @if (Auth::check())
                        <div class="authors">
                            <ul>
                                @foreach ($post->authors as $author)
                                    <li>{{ $author->username }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <p class="text-base text-gray-500 dark:text-gray-400"><time datetime="{{ $post->created_at }}">{{ $post->created_at->diffForHumans() }}</time></p>
                </div>
            </div>
        </address>
        <h1 class="mb-4 text-3xl font-extrabold leading-tight text-gray-900 lg:mb-6 lg:text-4xl dark:text-white inline"><a href="{{ route('show', $post->id) }}">{{ $post->title }}</a>
            @if($post->isAuthor($current_user_id))
                    <form action="{{ route('delete', $post->id) }}" method="POST" class="inline relative -top-2">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="submit delete">Delete</button>
                    </form>
            @endif</h1>
        @if($post->tags)
            <section class="tags">
                <ul>
                    @foreach ($post->tags as $tag)
                        <li>{{ $tag->name }}</li>
                    @endforeach
                </ul>
            </section>
        @endif
    </header>

    <div>
        @if($post->media)
        <section class="media">
            @foreach ($post->media as $media)
                <figure>
                    <img src="{{ asset('storage/media/'.$media->id.'.'.$media->extension) }}" alt=""/>
                </figure>
            @endforeach
        </section>
        @endif
    </div>
</article>
