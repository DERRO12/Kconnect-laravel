{{-- The ?action= AJAX part of the old include is handled in HomeController::index() --}}

<section class="news-events-section my-5">
    <div class="container">
        <div class="section-title">
            <h2><i class="fas fa-newspaper me-2"></i>News & Events</h2>
            <div class="title-line"></div>
        </div>
        
        <div class="row g-4">
            <!-- Left Column - Recent News -->
            <div class="col-lg-8">
                <div class="row" id="recentNewsContainer">
                    {{-- $recentNews: HomeData::recentNews() (latest 2 published posts) --}}
                    @if (count($recentNews) > 0)
                        @foreach ($recentNews as $news)
                    @php
                            $categoryClass = '';
                            switch($news['category']) {
                                case 'Announcements': $categoryClass = 'primary'; break;
                                case 'Events': $categoryClass = 'success'; break;
                                case 'People': $categoryClass = 'warning'; break;
                                case 'Milestones': $categoryClass = 'info'; break;
                                default: $categoryClass = 'secondary';
                            }
                            $hasImage = !empty($news['featured_image']) && file_exists(kc_public_file('uploads/news/' . $news['featured_image']));
                     @endphp
                    <div class="col-md-6 mb-4">
                        <div class="news-card h-100">
                            @if ($hasImage)
                            <div class="news-card-image-container">
                                <img src="{{ asset('uploads') }}/news/{{ $news['featured_image'] }}" 
                                     class="news-card-img" alt="{{ $news['title'] }}"
                                     loading="lazy" width="100%" height="200"
                                     onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'news-card-image-container no-image\'><i class=\'fas fa-newspaper fa-4x text-gradient\'></i></div>';">
                            </div>
                            @else
                            <div class="news-card-image-container no-image">
                                <i class="fas fa-newspaper fa-4x text-gradient"></i>
                            </div>
                            @endif
                            
                            <div class="news-card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-{!! $categoryClass !!} text-white px-3 py-2">
                                        {{ $news['category'] }}
                                    </span>
                                    <small class="text-muted">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        {!! date('M d, Y', strtotime($news['created_at'])) !!}
                                    </small>
                                </div>
                                
                                <h5 class="news-card-title fw-bold mb-2">
                                    {{ $news['title'] }}
                                </h5>
                                
                                <p class="news-card-excerpt text-muted mb-3">
                                    {{ substr(strip_tags($news['content']), 0, 120) }}...
                                </p>
                                
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="news-card-author">
                                        <i class="fas fa-user-circle me-1 text-gradient"></i>
                                        <span>{{ $news['author_name'] }}</span>
                                    </div>
                                    <div class="news-card-stats">
                                        <span class="me-2" title="Comments">
                                            <i class="far fa-comment"></i> {!! (int)$news['comments_count'] !!}
                                        </span>
                                        <span class="like-button" data-news-id="{!! $news['id'] !!}" title="Like" onclick="event.preventDefault();event.stopPropagation();">
                                            <i class="far fa-heart"></i> 
                                            <span class="like-count">{!! (int)$news['likes_count'] !!}</span>
                                        </span>
                                    </div>
                                </div>
                                
                                <a href="news-details?id={!! $news['id'] !!}" class="btn btn-sm btn-view-details w-100">
                                    <i class="fas fa-eye me-1"></i> View Details
                                </a>
                            </div>
                        </div>
                    </div>
                        @endforeach
                    @else
                    <div class="col-12 text-center py-5">
                        <i class="far fa-newspaper fa-4x text-muted mb-3"></i>
                        <p class="text-muted">No news available</p>
                    </div>
                    @endif
                </div>
                
                <div class="text-center mt-3">
                   <a class="nav-link" href="{{ url('all-news') }}">
    <i class="fas fa-newspaper me-2"></i>
    <span>All News</span>
</a>
                </div>
            </div>
            
            <!-- Right Column - Events -->
            <div class="col-lg-4">
                <div class="events-sidebar">
                    <h5 class="mb-3 fw-bold">
                        <i class="far fa-calendar-alt me-2 text-gradient"></i>Upcoming Events
                    </h5>
                    
                    <div class="events-list" id="eventsList">
                        {{-- $upcomingEvents: HomeData::upcomingEvents() (next 5) --}}
                        @if (count($upcomingEvents) > 0)
                        @php
                            $today = date('Y-m-d');
                            $tomorrow = date('Y-m-d', strtotime('+1 day'));
                        @endphp
                            @foreach ($upcomingEvents as $event)
                        @php
                                $eventDate = $event['event_date'];
                                $month = date('M', strtotime($eventDate));
                                $day = date('d', strtotime($eventDate));
                                $isToday = $today == $eventDate;
                                $isTomorrow = $tomorrow == $eventDate;
                         @endphp
                        <div class="event-item" onclick="showEventDetails({!! $event['id'] !!})" data-bs-toggle="modal" data-bs-target="#eventDetailModal" role="button" tabindex="0">
                            <div class="d-flex">
                                <div class="event-date-box text-center me-3">
                                    <span class="month d-block fw-bold text-uppercase small">{!! $month !!}</span>
                                    <span class="day d-block fw-bold h4 mb-0">{!! $day !!}</span>
                                </div>
                                <div class="event-details flex-grow-1">
                                    <h6 class="event-title mb-1 fw-bold">{{ $event['title'] }}</h6>
                                    @if ($isToday)<span class="badge bg-danger mb-2">Today!</span>@elseif ($isTomorrow)<span class="badge bg-warning mb-2">Tomorrow!</span>@endif
                                    @if (!empty($event['location']))<p class="event-location small text-muted mb-1"><i class="fas fa-map-marker-alt me-1 text-gradient"></i>{{ $event['location'] }}</p>@endif
                                    @if (!empty($event['event_time']))<p class="event-time small text-muted mb-1"><i class="far fa-clock me-1 text-gradient"></i>{!! date('h:i A', strtotime($event['event_time'])) !!}</p>@endif
                                    @if (!empty($event['category']))<span class="badge bg-light text-dark mt-1">{{ $event['category'] }}</span>@endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <div class="text-center py-4">
                            <i class="far fa-calendar-times fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No upcoming events</p>
                        </div>
                        @endif
                    </div>
                    
                    {{-- $upcomingEventsCount: HomeData::upcomingEventsCount() --}}
                    @php $countRow = $upcomingEventsCount; @endphp
                    @if ($countRow && $countRow['total'] > 5)
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-outline-gradient btn-sm" id="viewAllEventsBtn">
                            View All Events <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ALL NEWS MODAL -->
<div class="modal fade" id="allNewsModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #009ada, #76bc21); color: white; border: none;">
                <h5 class="modal-title fw-bold"><i class="fas fa-newspaper me-2"></i>All News</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="allNewsModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div>
                    <p class="mt-3 text-muted">Loading news...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- EVENT DETAIL MODAL -->
<div class="modal fade" id="eventDetailModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #009ada, #76bc21); color: white; border: none;">
                <h5 class="modal-title fw-bold"><i class="fas fa-calendar-alt me-2"></i>Event Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="eventDetailModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-3 text-muted">Loading event details...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ========================================
// FIXED SELF-REFERENCING AJAX
// ========================================
var SELF_URL = window.location.href.split('?')[0];

// ========================================
// VIEW ALL NEWS BUTTON
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    var viewAllNewsBtn = document.getElementById('viewAllNewsBtn');
    var allNewsModal = document.getElementById('allNewsModal');
    
    if (viewAllNewsBtn && allNewsModal) {
        viewAllNewsBtn.addEventListener('click', function() {
            var modal = new bootstrap.Modal(allNewsModal);
            modal.show();
            loadAllNews();
        });
        
        allNewsModal.addEventListener('hidden.bs.modal', function() {
            var modalBody = document.getElementById('allNewsModalBody');
            if (modalBody) {
                modalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" style="width:3rem;height:3rem;"></div><p class="mt-3 text-muted">Loading news...</p></div>';
            }
            document.body.classList.remove('modal-open');
            var backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(function(b) { b.remove(); });
        });
    }
});

function loadAllNews() {
    var modalBody = document.getElementById('allNewsModalBody');
    if (!modalBody) return;
    
    modalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" style="width:3rem;height:3rem;"></div><p class="mt-3 text-muted">Loading all news...</p></div>';
    
    var url = SELF_URL + '?action=get_all_news';
    
    fetch(url)
        .then(function(r) { 
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json(); 
        })
        .then(function(data) {
            if (data.success && data.html) {
                modalBody.innerHTML = data.html;
            } else {
                modalBody.innerHTML = '<div class="text-center py-5"><i class="far fa-newspaper fa-4x text-muted mb-3"></i><h5 class="text-muted">No news available</h5></div>';
            }
        })
        .catch(function(err) {
            console.error('Error loading news:', err);
            modalBody.innerHTML = '<div class="alert alert-danger text-center py-4"><i class="fas fa-exclamation-triangle fa-3x mb-3"></i><h5>Error Loading News</h5><p class="mb-3">URL: ' + url + '</p><button class="btn btn-outline-primary btn-sm" onclick="loadAllNews()"><i class="fas fa-sync-alt me-2"></i>Retry</button></div>';
        });
}

// ========================================
// VIEW ALL EVENTS BUTTON
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    var btn = document.getElementById('viewAllEventsBtn');
    if (btn) btn.addEventListener('click', loadAllEvents);
});

function loadAllEvents() {
    var eventsList = document.getElementById('eventsList');
    var viewAllBtn = document.getElementById('viewAllEventsBtn');
    if (!eventsList) return;
    
    eventsList.innerHTML = '<div class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div><small class="text-muted ms-2">Loading events...</small></div>';
    if (viewAllBtn) viewAllBtn.style.display = 'none';
    
    fetch(SELF_URL + '?action=get_all_events')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success && data.events && data.events.length > 0) {
                var html = '';
                var today = new Date().toISOString().split('T')[0];
                var tomorrow = new Date(Date.now()+86400000).toISOString().split('T')[0];
                
                data.events.forEach(function(event) {
                    var d = new Date(event.event_date);
                    var month = d.toLocaleString('en-US',{month:'short'}).toUpperCase();
                    var day = d.getDate();
                    html += '<div class="event-item" onclick="showEventDetails('+event.id+')" data-bs-toggle="modal" data-bs-target="#eventDetailModal" role="button" tabindex="0">'+
                        '<div class="d-flex">'+
                            '<div class="event-date-box text-center me-3"><span class="month d-block fw-bold text-uppercase small">'+month+'</span><span class="day d-block fw-bold h4 mb-0">'+day+'</span></div>'+
                            '<div class="event-details flex-grow-1">'+
                                '<h6 class="event-title mb-1 fw-bold">'+escHtml(event.title)+'</h6>'+
                                (today===event.event_date?'<span class="badge bg-danger mb-2">Today!</span>':'')+
                                (tomorrow===event.event_date?'<span class="badge bg-warning mb-2">Tomorrow!</span>':'')+
                                (event.location?'<p class="event-location small text-muted mb-1"><i class="fas fa-map-marker-alt me-1 text-gradient"></i>'+escHtml(event.location)+'</p>':'')+
                                (event.event_time?'<p class="event-time small text-muted mb-1"><i class="far fa-clock me-1 text-gradient"></i>'+fmtTime(event.event_time)+'</p>':'')+
                                (event.category?'<span class="badge bg-light text-dark mt-1">'+escHtml(event.category)+'</span>':'')+
                            '</div></div></div>';
                });
                eventsList.innerHTML = html;
            } else {
                eventsList.innerHTML = '<div class="text-center py-4"><i class="far fa-calendar-times fa-3x text-muted mb-3"></i><p class="text-muted">No upcoming events found</p></div>';
            }
        })
        .catch(function() {
            eventsList.innerHTML = '<div class="text-center py-4"><small class="text-danger">Failed to load events.</small></div>';
            if (viewAllBtn) viewAllBtn.style.display = 'inline-block';
        });
}

// ========================================
// EVENT DETAILS
// ========================================
(function() {
    var cache = new Map();
    var pending = null;
    
    document.addEventListener('DOMContentLoaded', function() {
        var eventModal = document.getElementById('eventDetailModal');
        if (eventModal) {
            eventModal.addEventListener('hidden.bs.modal', function() {
                var body = document.getElementById('eventDetailModalBody');
                if (body) {
                    body.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-3 text-muted">Loading event details...</p></div>';
                }
                document.body.classList.remove('modal-open');
                var backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(function(b) { b.remove(); });
            });
        }
    });
    
    window.showEventDetails = function(eventId) {
        var body = document.getElementById('eventDetailModalBody');
        if (!body) return;
        if (cache.has(eventId)) { body.innerHTML = cache.get(eventId); return; }
        
        body.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary mb-3"></div><p class="text-muted">Loading...</p></div>';
        if (pending) pending.abort();
        
        var ctrl = new AbortController();
        var tid = setTimeout(function() { ctrl.abort(); }, 8000);
        pending = ctrl;
        
        fetch(SELF_URL + '?action=get_event&id=' + eventId, {signal: ctrl.signal})
            .then(function(r) { return r.text(); })
            .then(function(data) {
                clearTimeout(tid);
                if (body) { body.innerHTML = data; cache.set(eventId, data); if (cache.size > 20) cache.delete(cache.keys().next().value); }
                pending = null;
            })
            .catch(function(err) {
                clearTimeout(tid); pending = null;
                if (err.name === 'AbortError') return;
                if (body) body.innerHTML = '<div class="text-center py-5"><i class="fas fa-exclamation-circle fa-4x text-muted mb-3"></i><h5 class="text-muted">Unable to load event</h5><button class="btn btn-outline-primary btn-sm mt-3" onclick="showEventDetails('+eventId+')"><i class="fas fa-sync-alt me-2"></i>Retry</button></div>';
            });
    };
})();

function escHtml(t) { var d = document.createElement('div'); d.textContent = t; return d.innerHTML; }
function fmtTime(t) { if(!t) return ''; var parts = t.split(':'); var hr = parseInt(parts[0]); return (hr%12||12)+':'+parts[1]+' '+(hr>=12?'PM':'AM'); }
</script>

<style>
.news-events-section { margin: 2rem 0; }
.news-card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.02); height: 100%; display: flex; flex-direction: column; transition: transform 0.2s ease, box-shadow 0.2s ease; }
.news-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,130,179,0.1); }
.news-card-image-container { height: 200px; overflow: hidden; background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%); display: flex; align-items: center; justify-content: center; }
.news-card-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease; }
.news-card-body { padding: 1.25rem; flex: 1; display: flex; flex-direction: column; }
.news-card-title { font-size: 1rem; font-weight: 700; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; color: #1e293b; }
.news-card-excerpt { font-size: 0.85rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; color: #64748b; margin-top: 0.5rem; margin-bottom: 1rem; }
.btn-view-details { color: #009ada; border: 1px solid #009ada; background: transparent; border-radius: 8px; font-weight: 600; font-size: 0.85rem; padding: 8px 16px; transition: all 0.25s ease; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 6px; margin-top: auto; }
.btn-view-details:hover { background: linear-gradient(135deg, #009ada 0%, #76bc21 100%); color: white; border-color: transparent; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,154,218,0.2); }
.btn-gradient { background: linear-gradient(135deg, #009ada 0%, #76bc21 100%); color: white; border: none; padding: 10px 24px; border-radius: 10px; font-weight: 600; transition: all 0.3s ease; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; }
.btn-gradient:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,154,218,0.25); color: white; }
.btn-outline-gradient { background: transparent; border: 1px solid #009ada; color: #009ada; padding: 10px 24px; border-radius: 10px; font-weight: 600; transition: all 0.3s ease; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; }
.btn-outline-gradient:hover { background: linear-gradient(135deg, #009ada 0%, #76bc21 100%); border-color: transparent; color: white; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,154,218,0.2); }
.all-news-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
.all-news-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,154,218,0.15) !important; }
.events-sidebar { background: #ffffff; border-radius: 24px; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); position: relative; height: fit-content; min-height: 450px; border: 1px solid rgba(0,130,179,0.15); }
.events-list { display: flex; flex-direction: column; gap: 0.75rem; max-height: 550px; overflow-y: auto; padding-right: 5px; }
.event-item { background: #f8fafc; border-radius: 16px; padding: 1rem; transition: all 0.2s ease; border: 1px solid #e9edf2; cursor: pointer; }
.event-item:hover { background: #ffffff; transform: translateX(4px); border-color: #009ada; }
.event-date-box { min-width: 60px; height: 70px; background: white; border-radius: 16px; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 0.5rem; border: 1px solid #e9edf2; }
.event-date-box .month { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; color: #009ada; }
.event-date-box .day { font-size: 1.5rem; line-height: 1; font-weight: 700; color: #1e293b; }
.event-title { font-size: 0.9rem; font-weight: 600; color: #1e293b; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.text-gradient { background: linear-gradient(135deg, #009ada 0%, #76bc21 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
@media (max-width: 768px) { .news-card-image-container { height: 180px; } .events-sidebar { margin-top: 1.5rem; } }
</style>