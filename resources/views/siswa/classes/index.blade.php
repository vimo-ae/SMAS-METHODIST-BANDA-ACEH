<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('E-Class') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">{{ __('Kelas Saya') }}</h3>
                <p class="mt-2 text-sm text-gray-600">{{ __('Kelas mata pelajaran yang Anda ikuti.') }}</p>
            </div>

            @forelse ($joined as $class)
                <article class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900">{{ $class->subject->name }}</h4>
                    <p class="mt-2 text-sm text-gray-600">{{ $class->schoolClass->name }}</p>
                    <p class="mt-1 text-sm text-gray-600">{{ __('Guru: :name', ['name' => $class->teacher->user->name ?? __('Belum ditentukan')]) }}</p>
                </article>
            @empty
                <div class="rounded-lg bg-white p-6 text-gray-600 shadow-sm ring-1 ring-gray-200">
                    {{ __('Belum ada kelas yang diikuti.') }}
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
