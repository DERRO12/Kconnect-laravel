        
{{-- includes/news-events.php ?action=get_all_news: the html put into the JSON. $allNews from HomeData::allNewsForModal() --}}
        @if (count($allNews) > 0)
            <div class="row g-3">
                @foreach ($allNews as $news)
                @php
                    $catClass = '';
                    switch($news['category']) {
                        case 'Announcements': $catClass = 'primary'; break;
                        case 'Events': $catClass = 'success'; break;
                        case 'People': $catClass = 'warning'; break;
                        case 'Milestones': $catClass = 'info'; break;
                        default: $catClass = 'secondary';
                    }
                    $hasImg = !empty($news['featured_image']) && file_exists(kc_public_file('uploads/news/' . $news['featured_image']));
                @endphp
                <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm all-news-card" style="border-radius: 12px; overflow: hidden; cursor: pointer;" 
                         onclick="window.location.href='{{ url('news-details') }}?id={!! $news['id'] !!}'">
                        @if ($hasImg)
                        <img src="{{ asset('uploads') }}/news/{{ $news['featured_image'] }}" 
                             class="card-img-top" alt="{{ $news['title'] }}"
                             style="height: 180px; object-fit: cover;" onerror="this.style.display='none';">
                        @else
                        <div style="height: 180px; background: linear-gradient(135deg, #f8fafc, #e9ecef); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-newspaper fa-3x" style="color: #009ada; opacity: 0.3;"></i>
                        </div>
                        @endif
                        <div class="card-body d-flex flex-column">
                            <span class="badge bg-{!! $catClass !!} mb-2 align-self-start">{{ $news['category'] }}</span>
                            <h6 class="card-title fw-bold">{{ $news['title'] }}</h6>
                            <p class="card-text small text-muted flex-grow-1">{{ substr(strip_tags($news['content']), 0, 150) }}...</p>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted"><i class="far fa-calendar-alt me-1"></i>{!! date('M d, Y', strtotime($news['created_at'])) !!}</small>
                                <span class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); window.location.href='{{ url('news-details') }}?id={!! $news['id'] !!}';">Read More</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="far fa-newspaper fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">No news available</h5>
            </div>
        @endif
