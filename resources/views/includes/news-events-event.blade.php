{{-- includes/news-events.php ?action=get_event&id=N, event found ($event = SELECT * FROM event_items WHERE id = N) --}}
            <div class="event-detail-modal">
                <h4 class="fw-bold mb-3">{{ $event['title'] }}</h4>
                <div class="mb-3">
                    <span class="badge bg-primary px-3 py-2">{{ $event['category'] }}</span>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><i class="far fa-calendar-alt me-2" style="color: #009ada;"></i><strong>Date:</strong> {!! date('F j, Y', strtotime($event['event_date'])) !!}</p>
                    </div>
                    <div class="col-md-6">
                        @if (!empty($event['event_time']))
                        <p><i class="far fa-clock me-2" style="color: #009ada;"></i><strong>Time:</strong> {!! date('h:i A', strtotime($event['event_time'])) !!}</p>
                        @endif
                    </div>
                </div>
                @if (!empty($event['location']))
                <p><i class="fas fa-map-marker-alt me-2" style="color: #76bc21;"></i><strong>Location:</strong> {{ $event['location'] }}</p>
                @endif
                @if (!empty($event['description']))
                <hr><p>{!! nl2br(htmlspecialchars($event['description'])) !!}</p>
                @endif
                @if ($event['rsvp_required'] && !empty($event['rsvp_url']))
                <hr><a href="{{ $event['rsvp_url'] }}" target="_blank" class="btn btn-gradient"><i class="fas fa-check-circle me-2"></i>RSVP Now</a>
                @endif
            </div>
