<x-app-layout>
    <div class="text-center mt-10">
        <h1 class="text-2xl font-bold text-red-600">Data Tidak Ditemukan</h1>
        <!-- Menampilkan pesan error dari abort() -->
        <p class="mt-2 text-gray-700">{{ $exception->getMessage() }}</p>

        <a href="{{ route('guru.exams.index') }}" class="btn btn-primary mt-4 inline-block">
            Kembali ke Daftar Ujian
        </a>
    </div>
</x-app-layout>