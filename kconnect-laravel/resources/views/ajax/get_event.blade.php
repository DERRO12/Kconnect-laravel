{{-- ajax/get_event.php (event found) --}}
@php
        $eventDate = strtotime($row['event_date']);
        $month = date('F', $eventDate);
        $day = date('d', $eventDate);
        $year = date('Y', $eventDate);
        $isToday = date('Y-m-d') == $row['event_date'];
        $isTomorrow = date('Y-m-d', strtotime('+1 day')) == $row['event_date'];
        
        // Get category color
        $categoryColor = '';
        switch($row['category']) {
            case 'Get together': $categoryColor = 'primary'; break;
            case 'Orientation': $categoryColor = 'info'; break;
            case 'Training': $categoryColor = 'success'; break;
            case 'Meeting': $categoryColor = 'warning'; break;
            case 'Social': $categoryColor = 'danger'; break;
            default: $categoryColor = 'secondary';
        }
         @endphp
        <div class="event-detail-wrapper">
            <!-- Event Header with Date -->
            <div class="event-header text-center mb-4">
                <div class="event-date-large mx-auto mb-3">
                    <span class="event-month">{!! $month !!}</span>
                    <span class="event-day">{!! $day !!}</span>
                    <span class="event-year">{!! $year !!}</span>
                </div>
                
                <h3 class="fw-bold mb-2">{{ $row['title'] }}</h3>
                
                @if ($isToday)
                    <span class="badge bg-danger mb-2 px-3 py-2">Happening Today!</span>
                @elseif ($isTomorrow)
                    <span class="badge bg-warning mb-2 px-3 py-2">Tomorrow!</span>
                @endif
                
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge bg-{!! $categoryColor !!} px-3 py-2">
                        {{ $row['category'] }}
                    </span>
                    @if ($row['rsvp_required'])
                        <span class="badge bg-info px-3 py-2">
                            <i class="fas fa-check-circle me-1"></i>RSVP Required
                        </span>
                    @endif
                </div>
            </div>
            
            <!-- Event Details -->
            <div class="event-details-list">
                @if (!empty($row['event_time']))
                <div class="detail-item">
                    <div class="detail-icon"><i class="fas fa-clock"></i></div>
                    <div class="detail-content">
                        <span class="detail-label">Time</span>
                        <span class="detail-value">{!! date('h:i A', strtotime($row['event_time'])) !!}</span>
                    </div>
                </div>
                @endif
                
                @if (!empty($row['location']))
                <div class="detail-item">
                    <div class="detail-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="detail-content">
                        <span class="detail-label">Location</span>
                        <span class="detail-value">{{ $row['location'] }}</span>
                    </div>
                </div>
                @endif
                
                @if (!empty($row['description']))
                <div class="detail-item description-item">
                    <div class="detail-icon"><i class="fas fa-align-left"></i></div>
                    <div class="detail-content">
                        <span class="detail-label">Description</span>
                        <p class="detail-value description-text">{!! nl2br(htmlspecialchars($row['description'])) !!}</p>
                    </div>
                </div>
                @endif
                
                @if ($row['rsvp_required'] && !empty($row['rsvp_url']))
                <div class="text-center mt-4">
                    <a href="{{ $row['rsvp_url'] }}" target="_blank" class="btn btn-gradient px-4 py-2">
                        <i class="fas fa-check-circle me-2"></i>RSVP Now
                    </a>
                </div>
                @endif
            </div>
        </div>
        
        <style>
        .event-detail-wrapper {
            padding: 10px;
        }
        
        .event-date-large {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 10px 25px rgba(0,154,218,0.3);
        }
        
        .event-month {
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.9;
        }
        
        .event-day {
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1;
            margin: 5px 0;
        }
        
        .event-year {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .event-details-list {
            max-width: 450px;
            margin: 0 auto;
        }
        
        .detail-item {
            display: flex;
            align-items: flex-start;
            padding: 15px 20px;
            background: #f8f9fa;
            border-radius: 15px;
            margin-bottom: 12px;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .detail-item:hover {
            background: #ffffff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border-left-color: #009ada;
            transform: translateX(5px);
        }
        
        .description-item:hover {
            border-left-color: #76bc21;
        }
        
        .detail-icon {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: #009ada;
            box-shadow: 0 3px 8px rgba(0,0,0,0.05);
        }
        
        .detail-content {
            flex: 1;
        }
        
        .detail-label {
            display: block;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            margin-bottom: 4px;
            font-weight: 600;
        }
        
        .detail-value {
            font-weight: 500;
            color: #212529;
            font-size: 1rem;
            word-break: break-word;
        }
        
        .description-text {
            line-height: 1.6;
            color: #495057;
            margin-top: 5px;
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,154,218,0.3);
            color: white;
        }
        
        .badge.bg-primary { background: #009ada !important; }
        .badge.bg-info { background: #3b82f6 !important; }
        .badge.bg-success { background: #76bc21 !important; }
        .badge.bg-warning { background: #f59e0b !important; color: #000 !important; }
        .badge.bg-danger { background: #ef4444 !important; }
        
        @media (max-width: 576px) {
            .event-date-large {
                width: 100px;
                height: 100px;
            }
            
            .event-day {
                font-size: 2rem;
            }
            
            .detail-item {
                padding: 12px 15px;
            }
            
            .detail-icon {
                width: 35px;
                height: 35px;
                margin-right: 12px;
            }
        }
        </style>
