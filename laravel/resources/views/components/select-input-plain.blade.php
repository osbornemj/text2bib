@props(['options' => [], 'selected' => []])

<select {!! $attributes->merge(['class' => '']) !!}>
    @foreach($options as $key => $value)
        <option value="{{ $key }}" {{ isset($selected[$key]) ? 'selected' : '' }}>{{ $value }}</option>
    @endforeach
</select>
