@props([
    'checked' => false,
])

<label class="inline-flex items-center pt-2 cursor-pointer">
    <input
        type="checkbox"
        @checked($checked)
        {{ $attributes->merge(['class' => 'sr-only peer']) }}
    >
    <div class="relative
                w-[60px]
                h-[34px]
                bg-gray-300
                rounded-full
                transition-colors
                duration-300

                peer-focus:ring-2
                peer-focus:ring-indigo-300
                peer-checked:bg-indigo-500

                after:content-['']
                after:absolute
                after:top-1
                after:left-1
                after:w-[26px]
                after:h-[26px]
                after:bg-white
                after:rounded-full
                after:transition-transform
                after:duration-300

                peer-checked:after:translate-x-[26px]">
    </div>
</label>