<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('E-Exam') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">{{ __('Ujian dan Kuis') }}</h3>
                <p class="mt-2 text-sm text-gray-600">{{ __('Ujian dan kuis yang tersedia untuk kelas Anda.') }}</p>
            </div>

            @forelse ($exams as $exam)
                @php($session = $sessions->get($exam->exam_id))
                <article class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900">{{ $exam->title }}</h4>
                            <p class="mt-1 text-sm text-gray-600">{{ $exam->classSubjectTeacher->subject->name }}</p>
                        </div>
                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700">{{ strtoupper($exam->exam_type) }}</span>
                    </div>
                    <p class="mt-4 text-sm text-gray-600">
                        {{ $exam->start_time->format('d M Y H:i') }} – {{ $exam->end_time->format('d M Y H:i') }}
                    </p>
                    <p class="mt-2 text-sm text-gray-600">
                        {{ __('Status: :status', ['status' => $session?->status ?? __('Belum dimulai')]) }}
                    </p>
                </article>
            @empty
                <div class="rounded-lg bg-white p-6 text-gray-600 shadow-sm ring-1 ring-gray-200">
                    {{ __('Belum ada ujian atau kuis yang tersedia.') }}
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
