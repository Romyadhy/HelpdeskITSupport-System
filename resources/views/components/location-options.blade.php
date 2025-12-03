@props(['locations'])

@foreach ($locations as $loc)
    <option value="{{ $loc->id }}">{{ $loc->name }}</option>
@endforeach
