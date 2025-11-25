<x-plain-app-layout>
    <x-slot name="header">
        {{ $header ?? '' }}
    </x-slot>

    {{ $slot }}
</x-plain-app-layout>
