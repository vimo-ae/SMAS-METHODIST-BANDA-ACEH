@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto p-4">
    <h1 class="text-xl font-semibold">{{ $student->user->name }}</h1>
    <p class="text-gray-500 mb-4">{{ $student->schoolClass?->name }}</p>

    @forelse ($grades as $list)
        <h2 class="font-medium mt-4">{{ $list->first()->classSubjectTeacher->subject->name }}</h2>
        <table class="w-full border-collapse">
            @foreach ($list as $g)
                <tr>
                    <td class="border p-2">
                        {{ strtoupper($g->grade_type) }}
                        {{ $g->assignment?->title ?? $g->exam?->title }}
                    </td>
                    <td class="border p-2 text-right">{{ number_format($g->score ?? 0, 2) }}</td>
                </tr>
            @endforeach
        </table>
    @empty
        <p class="text-gray-500">Belum ada nilai.</p>
    @endforelse

    <a href="{{ route('cek-nilai.index') }}" class="text-blue-600 block mt-4">Cari siswa lain</a>
</div>
@endsection