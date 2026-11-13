<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">E-Class</h2>
    </x-slot>

    @php
        $now = now();
        $status = function ($e) use ($now) {
            if (! $e->is_published) return ['Draf', 'bg-gray-100 text-gray-600'];
            $start = \Illuminate\Support\Carbon::parse($e->start_time);
            $end = \Illuminate\Support\Carbon::parse($e->end_time);
            if ($now->lt($start)) return ['Belum dibuka', 'bg-blue-50 text-blue-700'];
            if ($now->gt($end)) return ['Ditutup', 'bg-gray-100 text-gray-600'];
            return ['Berlangsung', 'bg-amber-50 text-amber-700'];
        };
    @endphp

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <a href="{{ route('guru.classes.index') }}" class="text-sm text-indigo-600 hover:underline">← Kembali ke daftar kelas</a>

            {{-- Header kelas --}}
            <div class="rounded-2xl p-6 text-white bg-gradient-to-r from-indigo-600 to-violet-600 shadow-sm">
                <p class="text-sm opacity-80">{{ $class->schoolClass->name ?? 'Kelas '.$class->class_id }}</p>
                <h1 class="text-2xl font-bold mt-1">{{ $class->subject->name ?? $class->subject_code }}</h1>
                <div class="mt-4 flex flex-wrap gap-x-8 gap-y-2 text-sm">
                    <span>Tahun ajaran: <strong>{{ $class->academic_year }}</strong></span>
                    <span>Semester: <strong>{{ ucfirst($class->semester) }}</strong></span>
                    <span>Kode gabung: <strong class="font-mono tracking-wider">{{ $class->join_code }}</strong></span>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-5">
                {{-- Ujian --}}
                <section class="lg:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-100">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="font-semibold text-gray-900">Ujian</h2>
                        <span class="text-xs text-gray-500">{{ $exams->count() }} ujian</span>
                    </div>

                    @if ($exams->isEmpty())
                        <p class="p-8 text-center text-sm text-gray-500">Belum ada ujian untuk kelas ini.</p>
                    @else
                        <ul class="divide-y divide-gray-100">
                            @foreach ($exams as $e)
                                @php [$label, $cls] = $status($e); @endphp
                                <li class="p-5">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <h3 class="font-medium text-gray-900">{{ $e->title }}</h3>
                                            <p class="text-xs text-gray-500 mt-0.5 uppercase">{{ $e->exam_type }}</p>
                                        </div>
                                        <span class="shrink-0 text-xs font-semibold px-2.5 py-1 rounded-full {{ $cls }}">{{ $label }}</span>
                                    </div>
                                    <dl class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                                        <div><dt class="text-gray-500">Soal</dt><dd class="font-semibold text-gray-900">{{ $e->questions_count }}</dd></div>
                                        <div><dt class="text-gray-500">Durasi</dt><dd class="font-semibold text-gray-900">{{ $e->duration_minutes }} menit</dd></div>
                                        <div><dt class="text-gray-500">Dibuka</dt><dd class="font-semibold text-gray-900">{{ \Illuminate\Support\Carbon::parse($e->start_time)->format('d M Y, H:i') }}</dd></div>
                                        <div><dt class="text-gray-500">Ditutup</dt><dd class="font-semibold text-gray-900">{{ \Illuminate\Support\Carbon::parse($e->end_time)->format('d M Y, H:i') }}</dd></div>
                                    </dl>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>

                {{-- Siswa --}}
                <section class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="font-semibold text-gray-900">Siswa Bergabung</h2>
                        <span class="text-xs text-gray-500">{{ $students->count() }} siswa</span>
                    </div>

                    @if ($students->isEmpty())
                        <p class="p-8 text-center text-sm text-gray-500">
                            Belum ada siswa yang bergabung. Bagikan kode
                            <span class="font-mono font-semibold">{{ $class->join_code }}</span> ke siswa.
                        </p>
                    @else
                        <ol class="divide-y divide-gray-100 max-h-[32rem] overflow-y-auto">
                            @foreach ($students as $i => $s)
                                <li class="px-5 py-3 flex items-center gap-3 text-sm">
                                    <span class="w-6 text-right text-gray-400">{{ $i + 1 }}</span>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $s->user->name ?? $s->name ?? 'Siswa' }}</div>
                                        <div class="text-xs text-gray-500">NIS {{ $s->nis }}</div>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </section>
            </div>
        </div>
    </div>
</x-app-layout>