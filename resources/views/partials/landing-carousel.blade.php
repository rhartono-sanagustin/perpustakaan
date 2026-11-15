@if ($carouselSlides->isNotEmpty())
    <section class="container-xl pt-4" aria-label="Sorotan perpustakaan">
        <div id="landingCarousel" class="carousel slide landing-carousel" data-carousel data-interval="6000">
            @if ($carouselSlides->count() > 1)
                <div class="carousel-indicators">
                    @foreach ($carouselSlides as $slide)
                        <button
                            type="button"
                            data-bs-target="#landingCarousel"
                            data-bs-slide-to="{{ $loop->index }}"
                            class="{{ $loop->first ? 'active' : '' }}"
                            aria-current="{{ $loop->first ? 'true' : 'false' }}"
                            aria-label="Slide {{ $loop->iteration }}"
                        ></button>
                    @endforeach
                </div>
            @endif

            <div class="carousel-inner">
                @foreach ($carouselSlides as $slide)
                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                        @if ($slide->link_url)
                            <a
                                href="{{ $slide->link_url }}"
                                @if ($slide->isExternalLink()) target="_blank" rel="noopener noreferrer" @endif
                            >
                        @endif

                        <img
                            src="{{ $slide->imageUrl() }}"
                            class="d-block w-100"
                            alt="{{ $slide->alt_text ?: ($slide->title ?: 'Slide carousel') }}"
                            @if (! $loop->first) loading="lazy" @endif
                        >

                        @if ($slide->title || $slide->caption)
                            <div class="carousel-caption">
                                @if ($slide->title)
                                    <h2 class="h5 mb-1">{{ $slide->title }}</h2>
                                @endif
                                @if ($slide->caption)
                                    <p class="mb-0 small">{{ $slide->caption }}</p>
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
                <button class="carousel-control-prev" type="button" data-bs-target="#landingCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Slide sebelumnya</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#landingCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Slide berikutnya</span>
                </button>
                <button type="button" class="carousel-pause" data-carousel-toggle aria-label="Jeda carousel" hidden>
                    <i class="bi bi-pause-fill" aria-hidden="true"></i>
                </button>
            @endif
        </div>
    </section>
@endif
