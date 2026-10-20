<section class="spotlights-section container my-5">
    <div class="section-title" data-aos="fade-up">
        <h2><i class="fas fa-star me-2"></i>Employee & Team Spotlights</h2>
        <div class="title-line"></div>
        <p class="text-muted mt-3">Celebrating excellence and outstanding achievements</p>
    </div>

    {{-- $spotlights: HomeData::spotlights() (6 active, ORDER BY sort_order, created_at DESC) --}}
    @if (count($spotlights) > 0)
    <div class="row g-4">
        @php $loopIndex = 0; /* old $loop (renamed: Blade owns $loop) */ @endphp
        @foreach ($spotlights as $spotlight)
        @php
            $isTeam = $spotlight['spotlight_type'] == 'team';
            $rating = floatval($spotlight['rating']);
            $fullStars = floor($rating);
            $halfStar = ($rating - $fullStars) >= 0.5;
            $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
         @endphp
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{!! $loopIndex * 100 !!}">
            <div class="card h-100 border-0 shadow-sm spotlight-card" data-bs-toggle="modal" data-bs-target="#spotlightModal{!! $spotlight['id'] !!}">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="spotlight-avatar me-3">
                            @if (!empty($spotlight['employee_photo']))
                                <img src="{{ asset('uploads') }}/{{ $spotlight['employee_photo'] }}" 
                                     class="rounded-circle" width="60" height="60" style="object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-gradient-light d-flex align-items-center justify-content-center" 
                                     style="width: 60px; height: 60px;">
                                    <i class="fas fa-{!! $isTeam ? 'users' : 'user' !!} fa-2x text-gradient"></i>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">{{ $spotlight['employee_name'] }}</h5>
                            <span class="badge bg-{!! $spotlight['badge_color'] ?: 'primary' !!} mt-1">
                                {{ $spotlight['badge_text'] ?: ($isTeam ? 'Team of the Month' : 'Employee of the Month') }}
                            </span>
                        </div>
                    </div>
                    
                    <h6 class="fw-bold text-gradient mb-2">{{ $spotlight['achievement_title'] }}</h6>
                    <p class="text-muted small mb-3">
                        {!! substr(htmlspecialchars($spotlight['achievement_description']), 0, 100) !!}...
                    </p>
                    
                    @if ($rating > 0)
                    <div class="mb-3">
                        <div class="rating-stars">
                            @for ($i = 0; $i < $fullStars; $i++)
                                <i class="fas fa-star text-warning"></i>
                            @endfor
                            @if ($halfStar)
                                <i class="fas fa-star-half-alt text-warning"></i>
                            @endif
                            @for ($i = 0; $i < $emptyStars; $i++)
                                <i class="far fa-star text-warning"></i>
                            @endfor
                            <span class="ms-2 small fw-bold">{!! $rating !!}/5.0</span>
                        </div>
                    </div>
                    @endif
                    
                    @if (!empty($spotlight['featured_quote']))
                    <div class="quote-section mt-3 p-3 bg-light rounded">
                        <i class="fas fa-quote-left text-gradient me-2"></i>
                        <em class="small">{{ $spotlight['featured_quote'] }}</em>
                    </div>
                    @endif
                    
                    <div class="mt-3 text-end">
                        <span class="badge bg-light text-dark">
                            <i class="far fa-calendar me-1"></i>{!! date('M Y', strtotime($spotlight['created_at'])) !!}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Spotlight Detail Modal -->
        <div class="modal fade" id="spotlightModal{!! $spotlight['id'] !!}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-star me-2 text-gradient"></i>Spotlight Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            @if (!empty($spotlight['employee_photo']))
                                <img src="{{ asset('uploads') }}/{{ $spotlight['employee_photo'] }}" 
                                     class="rounded-circle mb-3" width="100" height="100" style="object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-gradient-light d-flex align-items-center justify-content-center mx-auto mb-3" 
                                     style="width: 100px; height: 100px;">
                                    <i class="fas fa-{!! $isTeam ? 'users' : 'user' !!} fa-3x text-gradient"></i>
                                </div>
                            @endif
                            
                            <h4 class="fw-bold mb-1">{{ $spotlight['employee_name'] }}</h4>
                            @if ($spotlight['department'])
                                <p class="text-muted mb-2">{{ $spotlight['department'] }}</p>
                            @endif
                            
                            <span class="badge bg-{!! $spotlight['badge_color'] ?: 'primary' !!} px-3 py-2">
                                {{ $spotlight['badge_text'] ?: 'Achievement' }}
                            </span>
                        </div>
                        
                        <div class="achievement-section mb-4">
                            <h6 class="fw-bold text-gradient mb-2">🏆 Achievement</h6>
                            <h5 class="fw-bold">{{ $spotlight['achievement_title'] }}</h5>
                            <p class="text-muted">{!! nl2br(htmlspecialchars($spotlight['achievement_description'])) !!}</p>
                        </div>
                        
                        @if ($rating > 0)
                        <div class="rating-section mb-4">
                            <h6 class="fw-bold text-gradient mb-2">⭐ Rating</h6>
                            <div class="d-flex align-items-center">
                                <div class="rating-stars fs-4">
                                    @for ($i = 0; $i < $fullStars; $i++)
                                        <i class="fas fa-star text-warning"></i>
                                    @endfor
                                    @if ($halfStar)
                                        <i class="fas fa-star-half-alt text-warning"></i>
                                    @endif
                                    @for ($i = 0; $i < $emptyStars; $i++)
                                        <i class="far fa-star text-warning"></i>
                                    @endfor
                                </div>
                                <span class="ms-3 fs-4 fw-bold text-gradient">{!! $rating !!}</span>
                                <span class="ms-1 text-muted">/5.0</span>
                            </div>
                        </div>
                        @endif
                        
                        @if (!empty($spotlight['featured_quote']))
                        <div class="quote-section mb-4">
                            <h6 class="fw-bold text-gradient mb-2">💬 Featured Quote</h6>
                            <div class="p-3 bg-light rounded">
                                <i class="fas fa-quote-left text-gradient me-2"></i>
                                <em>{{ $spotlight['featured_quote'] }}</em>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        @php $loopIndex++; @endphp
        @endforeach
    </div>
    
    <div class="text-center mt-4" data-aos="fade-up">
        <button class="btn btn-gradient px-4" data-bs-toggle="modal" data-bs-target="#allSpotlightsModal">
            View All Spotlights <i class="fas fa-arrow-right ms-2"></i>
        </button>
    </div>
    @else
    <div class="text-center py-5">
        <div class="display-1 mb-4">⭐</div>
        <h3 class="text-muted">No spotlights yet</h3>
        <p class="text-muted">Check back soon for employee achievements!</p>
    </div>
    @endif
    
</section>
<style>
    .spotlights-section {
    margin: 2rem auto;
}

/* Card Styles */
.spotlight-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border-radius: 24px !important;
    overflow: hidden;
    background: white;
}

.spotlight-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,130,179,0.15) !important;
}

/* BIG AVATAR IMAGES - Card View */
.spotlight-avatar {
    position: relative;
    flex-shrink: 0;
}

.spotlight-avatar img,
.spotlight-avatar .rounded-circle {
    width: 90px !important;
    height: 100px !important;
    border-radius: 24px !important;
    object-fit: cover;
    
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    transition: all 0.3s ease;
}

.spotlight-card:hover .spotlight-avatar img {
    transform: scale(1.02);
    border-color: var(--kiboko-green);
}

/* Avatar placeholder without image */
.spotlight-avatar .bg-gradient-light {
    width: 90px;
    height: 90px;
    border-radius: 24px;
    background: linear-gradient(135deg, rgba(0,154,218,0.15) 0%, rgba(118,188,33,0.15) 100%);
    border: 2px dashed var(--kiboko-blue);
}

.spotlight-avatar .bg-gradient-light i {
    font-size: 3rem;
    color: var(--kiboko-blue);
}

/* BIG IMAGES - Modal/Dialog View */
.modal .rounded-circle.mb-3 {
    width: 200px !important;
    height: 200px !important;
   
    object-fit: cover;

    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
  
}

.modal .bg-gradient-light.mx-auto {
    width: 200px !important;
    height: 200px !important;
   
}

.modal .bg-gradient-light.mx-auto i {
    font-size: 4rem;
}

/* Optional: Full width hero image at top of card */
.spotlight-hero-image {
    width: 200px;
    height: 240px;
    object-fit: cover;
    border-bottom: 3px solid var(--kiboko-blue);
}

/* Card content spacing */
.spotlight-card .card-body {
    padding: 1.5rem !important;
}

/* Rating stars */
.rating-stars {
    display: inline-flex;
    align-items: center;
    gap: 2px;
}

.rating-stars .fas.fa-star,
.rating-stars .fas.fa-star-half-alt {
    color: #fbbf24;
    font-size: 16px;
}

.rating-stars .far.fa-star {
    color: #cbd5e1;
    font-size: 16px;
}

/* Quote section */
.quote-section {
    border-left: 4px solid var(--kiboko-blue);
    font-style: italic;
    background: #f8fafc !important;
    border-radius: 12px;
    padding: 1rem !important;
    margin-top: 1rem;
}

.quote-section i {
    opacity: 0.7;
}

/* Badge styling */
.badge {
    padding: 6px 12px;
    font-weight: 600;
    border-radius: 30px;
}

/* Text gradient */
.text-gradient {
    background: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Section title */
.section-title {
    text-align: center;
    margin-bottom: 2.5rem;
}

.section-title h2 {
    font-size: 2rem;
    font-weight: 700;
}

.title-line {
    width: 60px;
    height: 3px;
    background: linear-gradient(90deg, #009ada, #76bc21);
    margin: 0.5rem auto 0;
    border-radius: 3px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .spotlight-avatar img,
    .spotlight-avatar .rounded-circle {
        width: 70px !important;
        height: 70px !important;
        border-radius: 18px !important;
    }
    
    .spotlight-avatar .bg-gradient-light {
        width: 70px;
        height: 70px;
    }
    
    .spotlight-avatar .bg-gradient-light i {
        font-size: 2.2rem;
    }
    
    .modal .rounded-circle.mb-3 {
        width: 110px !important;
        height: 110px !important;
        border-radius: 30px !important;
    }
    
    .modal .bg-gradient-light.mx-auto {
        width: 110px !important;
        height: 110px !important;
    }
    
    .modal .bg-gradient-light.mx-auto i {
        font-size: 3rem;
    }
    
    .section-title h2 {
        font-size: 1.6rem;
    }
    
    .spotlight-card .card-body {
        padding: 1.2rem !important;
    }
}

@media (max-width: 576px) {
    .spotlight-avatar img,
    .spotlight-avatar .rounded-circle {
        width: 60px !important;
        height: 60px !important;
        border-radius: 16px !important;
    }
    
    .spotlight-avatar .bg-gradient-light {
        width: 60px;
        height: 60px;
    }
    
    .spotlight-avatar .bg-gradient-light i {
        font-size: 1.8rem;
    }
    
    .modal .rounded-circle.mb-3 {
        width: 90px !important;
        height: 90px !important;
        border-radius: 25px !important;
    }
}

/* Animation for image load */
@keyframes imagePop {
    0% {
        opacity: 0;
        transform: scale(0.9);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

.spotlight-avatar img {
    animation: imagePop 0.3s ease-out;
}

/* Card hover effects */
.spotlight-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.spotlight-card:hover {
    box-shadow: 0 25px 45px rgba(0,0,0,0.1) !important;
}

/* Ensure images don't get distorted */
.spotlight-avatar img {
    object-fit: cover;
    object-position: center;
}

/* Optional: Gradient overlay on hover for cards */
.spotlight-card {
    position: relative;
}

.spotlight-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #009ada, #76bc21);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.spotlight-card:hover::after {
    opacity: 1;
}
</style>
