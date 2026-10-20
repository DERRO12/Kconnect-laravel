<div class="row g-4 align-items-stretch">
    <!-- Left Column - Carousel Info Card -->
    <div class="col-lg-4" data-aos="fade-right">
        <div class="carousel-info-card h-100">
            {{-- $carouselInfo: SELECT * FROM homepage_carousel WHERE status = 1 ORDER BY sort_order ASC LIMIT 1 (HomeData::carouselInfo) --}}
            @if ($carouselInfo)
            @php
                $infoSlide = $carouselInfo;
                $infoTitle = htmlspecialchars($infoSlide['title']);
                $infoDescription = $infoSlide['description']; // Don't escape here - allow HTML
                $infoMediaType = $infoSlide['media_type'];
                $infoCreatedAt = $infoSlide['created_at'];
            @endphp
            <div class="card border-0 shadow-lg h-100">
                <div class="card-body p-4 d-flex flex-column info-card-content">
                    <div class="mb-4">
                        <span class="badge bg-gradient text-white px-3 py-2 mb-3 rounded-pill">
                            <i class="fas fa-{!! $infoMediaType == 'youtube' ? 'play-circle' : 'image' !!} me-2"></i>
                            Featured {!! ucfirst($infoMediaType) !!}
                        </span>
                        
                        @if (!empty($infoTitle))
                            <h3 class="fw-bold mb-3 text-dark">{!! $infoTitle !!}</h3>
                        @endif
                        
                        @if (!empty($infoDescription))
                            <div class="text-muted description-text rich-text-content">
                                @php  
                                // Allow specific HTML tags for formatting
                                $allowed_tags = '<b><strong><i><em><u><ul><ol><li><p><br><span><div><h1><h2><h3><h4><h5><h6>';
                                echo strip_tags($infoDescription, $allowed_tags); 
                                 @endphp
                            </div>
                        @else
                            <p class="text-muted fst-italic description-text">No description available for this media.</p>
                        @endif
                    </div>
                    
                    
                </div>
            </div>
            @else
            <div class="card border-0 shadow-lg h-100">
                <div class="card-body p-4 d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <div class="display-1 mb-3 text-gradient">📷</div>
                        <p class="text-muted fw-medium">No featured content available</p>
                        <small class="text-muted">Check back soon for updates</small>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Right Column - Carousel Media Gallery -->
    <div class="col-lg-8" data-aos="fade-left">
        <div class="carousel-card h-100">
            <div id="mediaCarousel" class="carousel slide h-100" data-bs-ride="carousel" data-bs-interval="5000">
                {{-- $carouselItems: SELECT * FROM homepage_carousel WHERE status = 1 ORDER BY sort_order ASC (HomeData::carouselItems) --}}
                @if (count($carouselItems) > 0)
                @php $slideCount = count($carouselItems); @endphp
                <div class="carousel-indicators">
                    @for ($i = 0; $i < $slideCount; $i++)
                    <button type="button" data-bs-target="#mediaCarousel" data-bs-slide-to="{!! $i !!}" 
                            {!! $i == 0 ? 'class="active"' : '' !!}></button>
                    @endfor
                </div>
                
                <div class="carousel-inner h-100">
                    @php $active = true; @endphp
                    @foreach ($carouselItems as $slide)
                    @php
                        $mediaUrl = htmlspecialchars($slide['media_url']);
                        $title = htmlspecialchars($slide['title']);
                        $mediaType = $slide['media_type'];
                     @endphp
                    <div class="carousel-item {!! $active ? 'active' : '' !!} h-100">
                        @if ($mediaType == 'image')
                            <div class="media-container">
                                <img src="uploads/{!! $mediaUrl !!}" class="media-image" alt="{!! $title !!}" loading="lazy">
                            </div>
                        @elseif ($mediaType == 'youtube')
                            <div class="media-container youtube-container">
                                <iframe src="https://www.youtube.com/embed/{!! $mediaUrl !!}?autoplay=0&mute=1&controls=1&rel=0&modestbranding=1&showinfo=0" 
                                        class="media-iframe" allowfullscreen loading="lazy" title="{!! $title !!}"></iframe>
                            </div>
                        @elseif ($mediaType == 'vimeo')
                            <div class="media-container vimeo-container">
                                <iframe src="https://player.vimeo.com/video/{!! $mediaUrl !!}?autoplay=0&muted=1&controls=1" 
                                        class="media-iframe" allowfullscreen loading="lazy" title="{!! $title !!}"></iframe>
                            </div>
                        @endif
                        
                        <!-- Optional caption overlay -->
                        @if (!empty($title))
                        <div class="carousel-caption d-none d-md-block">
                            <h5>{!! $title !!}</h5>
                        </div>
                        @endif
                    </div>
                    @php $active = false; @endphp
                    @endforeach
                </div>
                
                <button class="carousel-control-prev" type="button" data-bs-target="#mediaCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#mediaCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
                
                @else
                <div class="carousel-inner h-100">
                    <div class="carousel-item active h-100">
                        <div class="media-container no-media">
                            <div class="text-center">
                                <i class="fas fa-images fa-4x text-muted mb-3"></i>
                                <p class="text-muted fw-medium">No media available</p>
                                <small class="text-muted">Media gallery will appear here</small>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add these styles to your main CSS file or in a style tag -->
<style>
/* Rich text content styling */
.rich-text-content {
    line-height: 1.6;
    max-height: 300px;
    overflow-y: auto;
    padding-right: 10px;
}

.rich-text-content h1 {
    font-size: 1.8rem;
    margin-bottom: 0.5rem;
    color: #333;
}

.rich-text-content h2 {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    color: #333;
}

.rich-text-content h3 {
    font-size: 1.3rem;
    margin-bottom: 0.5rem;
    color: #333;
}

.rich-text-content h4 {
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
    color: #333;
}

.rich-text-content h5 {
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
    color: #333;
}

.rich-text-content h6 {
    font-size: 1rem;
    margin-bottom: 0.5rem;
    color: #333;
}

.rich-text-content p {
    margin-bottom: 1rem;
}

.rich-text-content ul, 
.rich-text-content ol {
    padding-left: 1.5rem;
    margin-bottom: 1rem;
}

.rich-text-content li {
    margin-bottom: 0.25rem;
}

.rich-text-content b, 
.rich-text-content strong {
    font-weight: 700;
    color: #009ada;
}

.rich-text-content i, 
.rich-text-content em {
    font-style: italic;
}

.rich-text-content u {
    text-decoration: underline;
}

/* Media container improvements */
.media-container {
    position: relative;
    width: 100%;
    height: 500px; /* Increased height */
    overflow: hidden;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.media-image {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Ensures image covers container properly */
    object-position: center;
    transition: transform 0.5s ease;
}

.media-image:hover {
    transform: scale(1.05);
}

.youtube-container,
.vimeo-container {
    position: relative;
    padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
    height: 0;
    overflow: hidden;
    border-radius: 12px;
}

.media-iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: 0;
}

/* Custom scrollbar for rich text content */
.rich-text-content::-webkit-scrollbar {
    width: 6px;
}

.rich-text-content::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.rich-text-content::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
    border-radius: 10px;
}

.rich-text-content::-webkit-scrollbar-thumb:hover {
    background: #009ada;
}

/* Carousel caption styling */
.carousel-caption {
    background: rgba(0, 0, 0, 0.6);
    border-radius: 8px;
    padding: 10px 20px;
    bottom: 20px;
}

.carousel-caption h5 {
    color: white;
    font-weight: 600;
    margin: 0;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .media-container {
        height: 350px;
    }
    
    .rich-text-content {
        max-height: 200px;
    }
}

@media (max-width: 576px) {
    .media-container {
        height: 250px;
    }
}
</style>