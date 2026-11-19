@if ($carouselSlides->isNotEmpty())
    <section id="landingCarousel" class="mx-auto w-full max-w-7xl px-4 pt-4 lg:px-8" aria-label="Sorotan perpustakaan">
        <div
            x-data="{
                active: 0,
                count: {{ $carouselSlides->count() }},
                paused: false,
                timer: null,
                init() {
                    if (this.count > 1 && ! window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                        this.start();
                    } else if (this.count > 1) {
                        this.paused = true;
                    }
                },
                start() {
                    this.clear();
                    this.timer = window.setInterval(() => {
                        if (! this.paused) this.next();
                    }, 6000);
                },
                clear() {
                    if (this.timer) window.clearInterval(this.timer);
                },
                next() {
                    this.active = (this.active + 1) % this.count;
                },
                previous() {
                    this.active = (this.active - 1 + this.count) % this.count;
                },
                go(index) {
                    this.active = index;
                },
                toggle() {
                    this.paused = ! this.paused;

                    if (! this.paused) this.start();
                }
            }"
            x-on:mouseenter="paused = true"
            x-on:mouseleave="paused = false"
            x-on:focusin="paused = true"
            class="relative overflow-hidden rounded-xl border border-zinc-200 bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800"
        >
            <div class="relative">
                @foreach ($carouselSlides as $slide)
                    <div
                        x-show="active === {{ $loop->index }}"
                        x-transition.opacity.duration.400ms
                        @if (! $loop->first) x-cloak @endif
                    >
                        @if ($slide->link_url)
                            <a
                                href="{{ $slide->link_url }}"
                                @if ($slide->isExternalLink()) target="_blank" rel="noopener noreferrer" @endif
                                class="block"
                            >
                        @endif

                        <img
                            src="{{ $slide->imageUrl() }}"
                            class="h-64 w-full object-cover sm:h-80 lg:h-96"
                            alt="{{ $slide->alt_text ?: ($slide->title ?: 'Slide carousel') }}"
                            @if (! $loop->first) loading="lazy" @endif
                        >

                        @if ($slide->title || $slide->caption)
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-zinc-950/80 to-transparent px-5 pt-16 pb-5 text-white">
                                @if ($slide->title)
                                    <h2 class="text-lg font-semibold">{{ $slide->title }}</h2>
                                @endif
                                @if ($slide->caption)
                                    <p class="mt-1 text-sm text-white/85">{{ $slide->caption }}</p>
                                @endif
                            </div>
                        @endif

                        @if ($slide->link_url)
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>

            @if ($carouselSlides->count() > 1)
                <div class="absolute inset-x-0 bottom-3 flex justify-center gap-2">
                    @foreach ($carouselSlides as $slide)
                        <button
                            type="button"
                            x-on:click="go({{ $loop->index }})"
                            :class="active === {{ $loop->index }} ? 'bg-white' : 'bg-white/50 hover:bg-white/80'"
                            class="h-1 w-7 rounded-full transition"
                            aria-label="Slide {{ $loop->iteration }}"
                        ></button>
                    @endforeach
                </div>

                <button
                    type="button"
                    x-on:click="previous()"
                    class="absolute start-3 top-1/2 inline-flex size-10 -translate-y-1/2 items-center justify-center rounded-full bg-zinc-950/45 text-white transition hover:bg-zinc-950/70 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                    aria-label="Slide sebelumnya"
                >
                    <flux:icon.chevron-left class="size-5" />
                </button>

                <button
                    type="button"
                    x-on:click="next()"
                    class="absolute end-3 top-1/2 inline-flex size-10 -translate-y-1/2 items-center justify-center rounded-full bg-zinc-950/45 text-white transition hover:bg-zinc-950/70 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                    aria-label="Slide berikutnya"
                >
                    <flux:icon.chevron-right class="size-5" />
                </button>

                <button
                    type="button"
                    x-on:click="toggle()"
                    class="absolute end-3 top-3 inline-flex size-9 items-center justify-center rounded-md border border-white/60 bg-zinc-950/35 text-white transition hover:bg-zinc-950/60 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                    :aria-label="paused ? 'Putar carousel' : 'Jeda carousel'"
                >
                    <flux:icon.play x-show="paused" class="size-4" />
                    <flux:icon.pause x-show="! paused" class="size-4" />
                </button>
            @endif
        </div>
    </section>
@endif
