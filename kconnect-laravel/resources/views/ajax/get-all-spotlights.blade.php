{{-- ajax/get-all-spotlights.php (spotlights found) --}}

<div class="row g-4">
    @foreach ($spotlights as $spotlight)
    @php
        $isTeam = $spotlight['spotlight_type'] == 'team';
        $rating = floatval($spotlight['rating']);
        $fullStars = floor($rating);
        $halfStar = ($rating - $fullStars) >= 0.5;
        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
     @endphp
    <div class="col-lg-4 col-md-6">
        <div class="card h-100 border-0 shadow-sm spotlight-card" data-bs-toggle="modal" data-bs-target="#spotlightAllModal{!! $spotlight['id'] !!}">
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
                    {!! substr(htmlspecialchars($spotlight['achievement_description']), 0, 100) . '...' !!}
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
    <div class="modal fade" id="spotlightAllModal{!! $spotlight['id'] !!}" tabindex="-1" aria-hidden="true">
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