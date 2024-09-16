@if (Auth::check())
    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('create') }}" method="POST" enctype="multipart/form-data" class="space-y-8 mb-10">
        @csrf

        <div>
            <label for="title">Titre:</label>
            <input type="text" id="title" name="title" required>
        </div>

        <div>
            <label for="authors_ids">Auteur:</label>
            <select name="authors_ids[]" id="authors_ids" multiple>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @if($user->id == $current_user_id) selected @endif>{{ $user->username }}  @if(isset($user->firstName)) ({{ $user->firstName }}) @endif </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="tags">Tags:</label>
            <select name="tags[]" id="tags" multiple>
                @foreach($tags as $tag)
                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="media_files">Médias:</label>
            <input type="file" multiple name="media_files[]" id="media_files" accept="image/png, image/jpeg, image/heic"/>
        </div>

        <button type="submit" class="submit">Soumettre</button>
    </form>
@endif
