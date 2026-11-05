<a href="{{ route('announcements.show', $announcement->slug) }}"
   class="group overflow-hidden rounded-xl border border-softblue/30 bg-white transition hover:-translate-y-1 hover:shadow-lg">

    @if ($announcement->image_url)
        <img src="{{ $announcement->image_url }}"
             alt="{{ $announcement->title }}"
             class="h-48 w-full object-cover">
    @else
        <div class="flex h-48 items-center justify-center bg-softblue/20">
            <span class="text-sm text-navy/50">SMAS Methodist Banda Aceh</span>
        </div>
    @endif

    <div class="p-5">
        <div class="flex items-center justify-between gap-2">
            <span class="text-xs font-semibold text-softblue">
                {{ $announcement->category_label }}
            </span>

            <time class="text-xs text-navy/50">
                {{ $announcement->published_at->translatedFormat('d M Y') }}
            </time>
        </div>

        <h3 class="mt-3 font-serif text-xl font-bold text-navy group-hover:text-softblue">
            {{ $announcement->title }}
        </h3>

        <p class="mt-2 text-sm leading-6 text-navy/70">
            {{ $announcement->excerpt }}
        </p>

        <span class="mt-4 inline-block text-sm font-semibold text-navy">
            Baca selengkapnya →
        </span>
    </div>
</a>
