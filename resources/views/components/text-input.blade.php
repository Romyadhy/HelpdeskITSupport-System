@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-teal-400 focus:ring-teal-400 rounded-md shadow-sm']) }}>
