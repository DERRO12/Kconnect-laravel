{{-- index.php (formatEventDate() helper of the old page was never used) --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kconnect</title>
      <link rel="icon" type="image/png" href="favi.png">
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        /* Additional styles for news images */
        .news-detail-image {
            max-height: 400px;
            width: 100%;
            object-fit: contain;
            background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%);
            border-radius: 16px;
            padding: 10px;
        }
        
        .news-card-image {
            height: 200px;
            width: 100%;
            object-fit: cover;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
            background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%);
        }
        
        .news-image img {
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }
        
        .spotlight-card {
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .spotlight-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
        }
    </style>
</head>
<body>
    <!-- Background Animation -->
    <div class="bg-animation">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>
@include('partials.navbar')
    <!-- Hero Section with Carousel -->
    <section class="hero">
        <div class="container">
            <!-- Welcome Text -->
            <div class="row mb-5" data-aos="fade-down">
                <div class="col-12 text-center">
                    <h1 class="display-3 fw-bold mb-3">Welcome to <span class="text-gradient">K-Connect</span></h1>
                    <p class="lead text-muted mx-auto" style="max-width: 700px;">The heart of our internal Communications and resource sharing. Stay informed and connected as we work together to drive excellence across our companies</p>
                </div>
            </div>
            
            <!-- Carousel Section -->
            @include('includes.carousel')
        </div>
    </section>

    <!-- Top Resources Section -->
    @include('includes.top-resources')

    <!-- News & Events Section -->
    @if ($newsAction !== null)
{{-- includes/news-events.php handled ?action= here: its output, then exit --}}
{!! $newsActionOutput !!}
@else
    @include('includes.news-events')

    <!-- Birthdays & Social Section -->
    @include('includes.birthdays-social')

    <!-- Employee & Team Spotlights Section -->
    @include('includes.spotlights')

    <!-- Key Contacts Section -->
    @include('includes.key-contacts')

    <!-- Footer -->
    
    @include('partials.footer')
    <!-- Back to Top Button -->
    <button class="back-to-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Modals -->
    <!-- News Modals -->
    <div class="modal fade" id="newsDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title"><i class="fas fa-newspaper me-2 text-gradient"></i>News Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="newsDetailModalBody">
                    <div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="allNewsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title"><i class="fas fa-newspaper me-2 text-gradient"></i>All News</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="allNewsModalBody">
                    <div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Modals -->
   <!-- Event Detail Modal -->
<div class="modal fade" id="eventDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-alt me-2 text-gradient"></i>Event Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="eventDetailModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted">Select an event to view details</p>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

    <div class="modal fade" id="allEventsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title"><i class="fas fa-calendar-alt me-2 text-gradient"></i>All Events</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="allEventsModalBody">
                    <div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Resources Modal -->
    <div class="modal fade" id="allResourcesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title"><i class="fas fa-link me-2 text-gradient"></i>All Resources</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="allResourcesModalBody"></div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Birthday Modals -->
    <div class="modal fade" id="monthlyBirthdaysModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="monthlyBirthdaysModalTitle">
                        <i class="fas fa-birthday-cake me-2 text-gradient"></i>
                        <span id="modalMonthTitle">Monthly Birthdays</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="monthlyBirthdaysBody">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-3 text-muted">Loading birthday celebrations...</p>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-gradient" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- All Spotlights Modal - Fixed Version -->
    <div class="modal fade" id="allSpotlightsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-star me-2 text-gradient"></i>All Employee & Team Spotlights</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Direct query to load all spotlights: $allSpotlights = HomeData::allSpotlights() --}}
                    @if (count($allSpotlights) > 0)
                        <div class="row g-4">
                        @foreach ($allSpotlights as $spotlight)
                        @php
                            $isTeam = $spotlight['spotlight_type'] == 'team';
                            $rating = floatval($spotlight['rating']);
                            $fullStars = floor($rating);
                            $halfStar = ($rating - $fullStars) >= 0.5;
                            $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                             @endphp
                            <div class="col-lg-4 col-md-6">
                                <div class="card h-100 border-0 shadow-sm spotlight-card" data-bs-toggle="modal" data-bs-target="#spotlightModalAll{!! $spotlight['id'] !!}">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="spotlight-avatar me-3">
                                                @if (!empty($spotlight['employee_photo']))
                                                    <img src="uploads/{{ $spotlight['employee_photo'] }}" 
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
                            
                            <!-- Individual Spotlight Detail Modal -->
                            <div class="modal fade" id="spotlightModalAll{!! $spotlight['id'] !!}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"><i class="fas fa-star me-2 text-gradient"></i>Spotlight Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="text-center mb-4">
                                                @if (!empty($spotlight['employee_photo']))
                                                    <img src="uploads/{{ $spotlight['employee_photo'] }}" 
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
                        @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                                <div class="display-1 mb-4">⭐</div>
                                <h3 class="text-muted">No spotlights yet</h3>
                                <p class="text-muted">Check back soon for employee achievements!</p>
                              </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

<!-- Contact Modal -->
<div class="modal fade" id="contactModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="fas fa-address-card me-2 text-gradient"></i>Contact Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="contactModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted">Select a contact to view details</p>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

    <!-- Toast Notifications -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="wishesToast" class="toast" role="alert">
            <div class="toast-header bg-gradient text-white">
                <i class="fas fa-gift me-2"></i>
                <strong class="me-auto">Birthday Wishes</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">Wish sent successfully! 🎂</div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1"></script>
    <script src="js/main.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true
        });
        
        // Back to top button visibility
        window.addEventListener('scroll', function() {
            var backToTop = document.querySelector('.back-to-top');
            if (window.pageYOffset > 300) {
                backToTop.style.display = 'block';
            } else {
                backToTop.style.display = 'none';
            }
        });
    </script>
</body>
</html>
@endif
