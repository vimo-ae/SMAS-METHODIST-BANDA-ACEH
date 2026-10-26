<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                <a href="{{ route('guru.classes.index') }}" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200 transition hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('E-Class') }}</h3>
                    <p class="mt-2 text-sm text-gray-600">{{ __('Buka kelas untuk mengelola pembelajaran.') }}</p>
                </a>

                <a href="{{ route('guru.exams.index') }}" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200 transition hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('E-Exam') }}</h3>
                    <p class="mt-2 text-sm text-gray-600">{{ __('Kelola ujian dan kuis kelas Anda.') }}</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
