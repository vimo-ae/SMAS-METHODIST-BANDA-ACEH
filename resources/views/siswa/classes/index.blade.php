
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="bg-sky-100 p-2 rounded-xl">
                <svg class="w-6 h-6 text-sky-600" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 6v12m6-6H6"/>
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-xl text-sky-900">
                    E-Class
                </h2>
                <p class="text-xs text-sky-600">
                    Teacher Dashboard
                </p>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-sky-50 py-10">

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- HEADER --}}
            <div class="mb-8">
                <div class="inline-flex items-center gap-2
                            bg-sky-100 text-sky-700
                            px-4 py-2 rounded-full text-sm
                            font-semibold mb-4">
                    <span class="w-2 h-2 bg-sky-500 rounded-full"></span>
                    Area Guru
                </div>

                <h1 class="text-3xl font-bold text-sky-950">
                    Kelas yang Kamu Ampu
                </h1>

                <p class="text-slate-500 mt-2">
                    Kelola kelas, pantau siswa, dan lihat ujian
                    yang kamu buat dengan mudah.
                </p>

                <div class="mt-5 flex items-center gap-3">
                    <div class="bg-white border border-sky-100
                                shadow-sm rounded-xl px-5 py-3">
                        <p class="text-xs text-slate-500">
                            Total Kelas
                        </p>
                        <p class="text-2xl font-bold text-sky-700">
                            {{ $classes->count() }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- DAFTAR KELAS --}}
            @if ($classes->isEmpty())

                <div class="bg-white border border-sky-100
                            rounded-2xl p-12 text-center shadow-sm">

                    <div class="bg-sky-100 w-16 h-16 rounded-2xl
                                flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-sky-500"
                             fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M12 6v12m6-6H6"/>
                        </svg>
                    </div>

                    <h3 class="text-lg font-bold text-sky-950">
                        Belum Ada Kelas
                    </h3>

                    <p class="text-slate-500 text-sm mt-2">
                        Kamu belum mengampu kelas mata pelajaran apa pun.
                    </p>
                </div>

            @else

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-2">

                    @foreach ($classes as $c)

                        <a href="{{ route('guru.classes.show', $c->cst_id) }}"
                           class="group block bg-white rounded-2xl
                                  border border-sky-100 shadow-sm
                                  hover:shadow-xl hover:shadow-sky-100
                                  hover:-translate-y-1
                                  transition duration-300 overflow-hidden">

                            {{-- CARD TOP --}}
                            <div class="bg-gradient-to-r
                                        from-sky-100 to-blue-50
                                        p-5 border-b border-sky-100">

                                <div class="flex items-start
                                            justify-between gap-3">

                                    <div class="flex items-center gap-3">

                                        <div class="w-12 h-12
                                                    rounded-xl bg-white
                                                    shadow-sm flex
                                                    items-center justify-center">

                                            <svg class="w-6 h-6 text-sky-600"
                                                 fill="none"
                                                 stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M12 14l9-5-9-5-9 5
                                                         9 5zm0 0v7"/>
                                            </svg>
                                        </div>

                                        <div>
                                            <h3 class="text-lg font-bold
                                                       text-sky-950
                                                       group-hover:text-sky-600
                                                       transition">
                                                {{ $c->subject->name ?? $c->subject_code }}
                                            </h3>

                                            <p class="text-sm text-sky-700">
                                                {{ $c->schoolClass->name ?? 'Kelas '.$c->class_id }}
                                            </p>
                                        </div>
                                    </div>

                                    <span class="text-xs font-semibold
                                                 px-3 py-1.5 rounded-full
                                                 bg-white text-sky-700
                                                 border border-sky-200">
                                        {{ ucfirst($c->semester) }}
                                        {{ $c->academic_year }}
                                    </span>

                                </div>
                            </div>

                            {{-- CARD BODY --}}
                            <div class="p-5">

                                <div class="grid grid-cols-2 gap-4">

                                    {{-- JUMLAH SISWA --}}
                                    <div class="bg-sky-50 rounded-xl p-4
                                                border border-sky-100">

                                        <div class="flex items-center
                                                    justify-between">

                                            <span class="text-xs
                                                         text-slate-500">
                                                Siswa Bergabung
                                            </span>

                                            <svg class="w-5 h-5 text-sky-500"
                                                 fill="none"
                                                 stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M17 20h5v-2a3 3
                                                         0 00-5.356-1.857
                                                         M17 20H7m10 0v-2
                                                         c0-.656-.126-1.283
                                                         -.356-1.857
                                                         M7 20H2v-2a3 3
                                                         0 015.356-1.857
                                                         M7 20v-2
                                                         c0-.656.126-1.283
                                                         .356-1.857
                                                         m0 0a5.002 5.002
                                                         0 019.288 0
                                                         M15 7a3 3 0
                                                         11-6 0 3 3 0
                                                         016 0zm6 3a2 2
                                                         0 11-4 0 2 2
                                                         0 014 0z"/>
                                            </svg>
                                        </div>

                                        <p class="text-2xl font-bold
                                                  text-sky-800 mt-2">
                                            {{ $studentCounts[$c->cst_id] ?? 0 }}
                                        </p>
                                    </div>

                                    {{-- JUMLAH UJIAN --}}
                                    <div class="bg-blue-50 rounded-xl p-4
                                                border border-blue-100">

                                        <div class="flex items-center
                                                    justify-between">

                                            <span class="text-xs
                                                         text-slate-500">
                                                Total Ujian
                                            </span>

                                            <svg class="w-5 h-5 text-blue-500"
                                                 fill="none"
                                                 stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M9 5H7a2 2 0
                                                         00-2 2v12a2 2
                                                         0 002 2h10a2 2
                                                         0 002-2V7a2 2
                                                         0 00-2-2h-2
                                                         M9 5a2 2 0
                                                         002 2h2a2 2
                                                         0 002-2
                                                         M9 12h6m-6 4h6"/>
                                            </svg>
                                        </div>

                                        <p class="text-2xl font-bold
                                                  text-blue-800 mt-2">
                                            {{ $examCounts[$c->cst_id] ?? 0 }}
                                        </p>
                                    </div>

                                </div>

                                {{-- JOIN CODE --}}
                                <div class="mt-5 flex items-center
                                            justify-between gap-2">

                                    <div>
                                        <p class="text-xs text-slate-400">
                                            Kode Gabung Kelas
                                        </p>

                                        <p class="font-mono font-bold
                                                  text-slate-700 mt-1">
                                            {{ $c->join_code }}
                                        </p>
                                    </div>

                                    <span class="inline-flex items-center
                                                 gap-2 bg-sky-600
                                                 text-white text-sm
                                                 font-semibold px-4 py-2
                                                 rounded-xl
                                                 group-hover:bg-sky-700
                                                 transition">

                                        Buka Kelas

                                        <svg class="w-4 h-4"
                                             fill="none"
                                             stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </span>

                                </div>

                            </div>

                        </a>

                    @endforeach

                </div>

            @endif

        </div>
    </div>
</x-app-layout>
