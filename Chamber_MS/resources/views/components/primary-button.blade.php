<button
    {{ $attributes->merge([
        'type' => 'submit',
        'class' => '
            inline-flex
            items-center
            justify-center
            text-center

            px-4 py-2
            bg-gray-800
            border border-transparent
            rounded-md

            font-semibold text-xs
            text-white
            uppercase tracking-widest

            transition ease-in-out duration-150

            hover:bg-gray-700
            focus:bg-gray-700
            active:bg-gray-900

            focus:outline-none
            focus:ring-2
            focus:ring-indigo-500
            focus:ring-offset-2
        ',
    ]) }}
>
    {{ $slot }}
</button>

{{-- <button
    {{ $attributes->merge([
        'type' => 'submit',
        'class' => '
                inline-flex items-center
                px-4 py-2
                bg-gray-800
                border border-transparent
                rounded-md
                font-semibold text-xs
                text-white
                uppercase tracking-widest
                transition ease-in-out duration-150
    
                hover:bg-gray-700
                focus:bg-gray-700
                active:bg-gray-900
    
                focus:outline-none
                focus:ring-2
                focus:ring-indigo-500
                focus:ring-offset-2
    
                dark:bg-gray-200
                dark:text-gray-800
                dark:hover:bg-white
                dark:focus:bg-white
                dark:active:bg-gray-300
                dark:focus:ring-offset-gray-800
            ',
    ]) }}>
    {{ $slot }}
</button> --}}
