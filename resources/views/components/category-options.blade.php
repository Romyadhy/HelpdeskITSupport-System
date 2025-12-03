@props(['categories'])

@foreach ($categories as $cat)
    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
@endforeach
