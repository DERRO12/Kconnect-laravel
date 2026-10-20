{{--
 * Birthdays Section - Clean Centered Layout with Smart Compact Cards
 * Shows: Today's birthdays + Next 4 upcoming birthdays only
 * Now pulls data directly from employees table
--}}

<section class="birthday-section">
    <div class="container">
        <div class="section-title text-center" data-aos="fade-up">
            <h2><i class="fas fa-birthday-cake me-2"></i>Birthday Celebrations</h2>
            <div class="title-line mx-auto"></div>
            <p class="text-muted mt-3">Celebrating our amazing team members</p>
        </div>

        <div id="birthdaysContainer" data-aos="fade-up" data-aos-delay="100">
            @php
            $currentMonth = date('n');
            $todayDay = date('j');
            // $monthBirthdays: all birthdays for this month from employees table (HomeData::employeeBirthdaysForMonth)
            @endphp
            @if (count($monthBirthdays) > 0)
            @php
                // Separate birthdays into arrays
                $todayBirthdays = [];
                $upcomingBirthdays = [];
                
                foreach ($monthBirthdays as $bday) {
                    $bdayDay = $bday['birth_day'];
                    
                    if ($bdayDay == $todayDay) {
                        $todayBirthdays[] = $bday;
                    } elseif ($bdayDay > $todayDay) {
                        $upcomingBirthdays[] = $bday;
                    }
                }
                
                // Sort upcoming birthdays by day
                usort($upcomingBirthdays, function($a, $b) {
                    return $a['birth_day'] - $b['birth_day'];
                });
                
                // SMART LIMIT: Only show next 4 upcoming birthdays
                $displayUpcoming = array_slice($upcomingBirthdays, 0, 4);
                $hiddenUpcomingCount = count($upcomingBirthdays) - count($displayUpcoming);
                
                $totalToday = count($todayBirthdays);
                $totalUpcoming = count($upcomingBirthdays);
                $totalBirthdays = $totalToday + $totalUpcoming;
             @endphp
            
            <!-- Two Column Layout - Centered -->
            <div class="birthday-grid">
                <!-- Today's Birthdays Column -->
                <div class="birthday-col">
                    <div class="col-header">
                        <div class="col-header-icon today-icon">
                            <i class="fas fa-gift"></i>
                        </div>
                        <h3>Today's Birthdays</h3>
                        <span class="count-badge today-count">{!! $totalToday !!}</span>
                    </div>
                    
                    <div class="cards-container">
                        @if ($totalToday > 0)
                            @foreach ($todayBirthdays as $bday)
                            @php
                                $bdayDate = strtotime($bday['birth_date']);
                                $bdayFullDate = date('M j', $bdayDate);
                             @endphp
                            <div class="birthday-card today">
                                <div class="card-avatar">
                                    @if (!empty($bday['photo']) && file_exists(kc_public_file('uploads/employees/' . $bday['photo'])))
                                        <img src="uploads/employees/{{ $bday['photo'] }}" alt="{{ $bday['full_name'] }}">
                                    @else
                                        <div class="avatar-fallback">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                    <div class="avatar-badge">
                                        <i class="fas fa-gift"></i>
                                    </div>
                                </div>
                                <div class="card-content">
                                    <div class="card-row">
                                        <h4 class="card-name">{{ $bday['full_name'] }}</h4>
                                        <div class="day-badge">
                                            {!! $bday['birth_day'] !!}
                                        </div>
                                    </div>
                                    <p class="card-dept">
                                        <i class="fas fa-building"></i>
                                        {!! !empty($bday['department']) ? htmlspecialchars($bday['department']) : 'Team Member' !!}
                                    </p>
                                    <div class="card-date">
                                        <i class="far fa-calendar-alt"></i>
                                        {!! $bdayFullDate !!}
                                        <span class="tag-today">Today!</span>
                                    </div>
                                    <button class="wish-button" onclick="sendWishes({!! $bday['id'] !!})">
                                        <i class="fas fa-gift"></i> Wish
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="empty-col">
                                <i class="fas fa-calendar-day"></i>
                                <p>No birthdays today</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Upcoming Birthdays Column - SMART: Only next 4 -->
                <div class="birthday-col">
                    <div class="col-header">
                        <div class="col-header-icon upcoming-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h3>Upcoming Birthdays</h3>
                        <span class="count-badge upcoming-count">{!! $totalUpcoming !!}</span>
                    </div>
                    
                    <div class="cards-container">
                        @if (!empty($displayUpcoming))
                            @foreach ($displayUpcoming as $bday)
                            @php
                                $bdayDate = strtotime($bday['birth_date']);
                                $bdayFullDate = date('M j', $bdayDate);
                                $daysUntil = $bday['birth_day'] - $todayDay;
                             @endphp
                            <div class="birthday-card upcoming">
                                <div class="card-avatar">
                                    @if (!empty($bday['photo']) && file_exists(kc_public_file('uploads/employees/' . $bday['photo'])))
                                        <img src="uploads/employees/{{ $bday['photo'] }}" alt="{{ $bday['full_name'] }}">
                                    @else
                                        <div class="avatar-fallback">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                    <div class="avatar-badge upcoming-badge">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                </div>
                                <div class="card-content">
                                    <div class="card-row">
                                        <h4 class="card-name">{{ $bday['full_name'] }}</h4>
                                        <div class="day-badge light">
                                            {!! $bday['birth_day'] !!}
                                        </div>
                                    </div>
                                    <p class="card-dept">
                                        <i class="fas fa-building"></i>
                                        {!! !empty($bday['department']) ? htmlspecialchars($bday['department']) : 'Team Member' !!}
                                    </p>
                                    <div class="card-date">
                                        <i class="far fa-calendar-alt"></i>
                                        {!! $bdayFullDate !!}
                                        <span class="tag-upcoming">+{!! $daysUntil !!}d</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            
                            <!-- Show "and more" indicator if there are hidden birthdays -->
                            @if ($hiddenUpcomingCount > 0)
                            <div class="more-indicator">
                                <i class="fas fa-ellipsis-h"></i>
                                <span>and {!! $hiddenUpcomingCount !!} more this month</span>
                            </div>
                            @endif
                            
                        @else
                            <div class="empty-col">
                                <i class="fas fa-calendar-week"></i>
                                <p>No upcoming birthdays</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- View All Button -->
            @if ($totalBirthdays > 0)
            <div class="view-all-wrapper">
                <button class="view-all-button" id="viewMonthBirthdays" 
                        data-month="{!! $currentMonth !!}" 
                        data-month-name="{!! date('F') !!}">
                    <i class="fas fa-calendar-alt"></i>
                    View All {!! date('F') !!} Birthdays
                    <span>{!! $totalBirthdays !!}</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
            @endif
            
            @else
            <!-- No Birthdays This Month -->
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-birthday-cake"></i>
                </div>
                <h3>No Birthdays This Month</h3>
                <p>Check out other months for celebrations!</p>
                
                {{-- $otherBirthdayMonths: other months with birthdays from employees table (HomeData::employeeBirthdayMonths) --}}
                @if (count($otherBirthdayMonths) > 0)
                <div class="month-chips">
                    @foreach ($otherBirthdayMonths as $om)
                    @php
                        $monthName = date('F', mktime(0,0,0,$om['month'],1));
                     @endphp
                    <button class="month-chip view-month-btn" data-month="{!! $om['month'] !!}" data-month-name="{!! $monthName !!}">
                        {!! substr($monthName, 0, 3) !!}
                        <span>{!! $om['count'] !!}</span>
                    </button>
                    @endforeach
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</section>

<style>
/* ========================================
   CLEAN CENTERED BIRTHDAY SECTION - SMART & COMPACT
   ======================================== */

.birthday-section {
    padding: 2rem 0;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 40px;
    margin: 2rem 0;
    position: relative;
}

.birthday-section .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

/* Section Title */
.section-title h2 {
    font-size: 1.8rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 0.75rem;
}

.title-line {
    width: 60px;
    height: 3px;
    background: linear-gradient(90deg, #0082b3, #6aa61d);
    border-radius: 3px;
}

/* Two Column Grid */
.birthday-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-top: 1.5rem;
}

/* Column Styling */
.birthday-col {
    background: transparent;
}

.col-header {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #e2e8f0;
}

.col-header-icon {
    width: 36px;
    height: 36px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}

.today-icon {
    background: linear-gradient(135deg, #ffd700, #f59e0b);
    color: white;
}

.upcoming-icon {
    background: linear-gradient(135deg, #0082b3, #3b82f6);
    color: white;
}

.col-header h3 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
}

.count-badge {
    background: #e2e8f0;
    color: #475569;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.2rem 0.5rem;
    border-radius: 30px;
}

.today-count {
    background: #fef3c7;
    color: #d97706;
}

.upcoming-count {
    background: #e0f2fe;
    color: #0284c7;
}

/* Cards Container */
.cards-container {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

/* Individual Birthday Card - SMALL & SMART */
.birthday-card {
    background: white;
    border-radius: 20px;
    display: flex;
    align-items: center;
    gap: 0.875rem;
    padding: 0.75rem 1rem;
    transition: all 0.25s ease;
    border: 1px solid #eef2ff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
}

.birthday-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    border-color: #e2e8f0;
}

.birthday-card.today {
    background: linear-gradient(135deg, #fffaf5, #ffffff);
    border-left: 3px solid #ffd700;
}

.birthday-card.upcoming {
    border-left: 3px solid transparent;
}

.birthday-card.upcoming:hover {
    border-left-color: #0082b3;
}

/* Avatar - SMALL */
.card-avatar {
    position: relative;
    flex-shrink: 0;
}

.card-avatar img {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid white;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}

.avatar-fallback {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #eef2ff, #e6f0fa);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #0082b3;
    font-size: 1.2rem;
}

.avatar-badge {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid white;
    font-size: 0.55rem;
    background: #ffd700;
    color: white;
}

.avatar-badge.upcoming-badge {
    background: #0082b3;
}

/* Card Content */
.card-content {
    flex: 1;
    min-width: 0;
}

.card-row {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 0.5rem;
    margin-bottom: 0.2rem;
}

.card-name {
    font-size: 0.9rem;
    font-weight: 600;
    color: #0f172a;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.day-badge {
    width: 30px;
    height: 30px;
    background: linear-gradient(135deg, #0082b3, #6aa61d);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 0.8rem;
    flex-shrink: 0;
}

.day-badge.light {
    background: #f1f5f9;
    color: #0082b3;
}

.card-dept {
    font-size: 0.65rem;
    color: #64748b;
    margin: 0 0 0.2rem 0;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.card-dept i {
    font-size: 0.6rem;
    color: #94a3b8;
}

.card-date {
    font-size: 0.65rem;
    color: #475569;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    flex-wrap: wrap;
    margin-bottom: 0.3rem;
}

.card-date i {
    color: #0082b3;
    font-size: 0.6rem;
}

.tag-today {
    background: #fef3c7;
    color: #d97706;
    font-size: 0.55rem;
    font-weight: 600;
    padding: 0.1rem 0.4rem;
    border-radius: 30px;
}

.tag-upcoming {
    background: #e0f2fe;
    color: #0284c7;
    font-size: 0.55rem;
    font-weight: 600;
    padding: 0.1rem 0.4rem;
    border-radius: 30px;
}

/* Wish Button - Small */
.wish-button {
    background: linear-gradient(135deg, #0082b3, #6aa61d);
    border: none;
    border-radius: 30px;
    padding: 0.25rem 0.75rem;
    font-size: 0.6rem;
    font-weight: 500;
    color: white;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    margin-top: 0.4rem;
    transition: all 0.2s;
    cursor: pointer;
}

.wish-button i {
    font-size: 0.55rem;
}

.wish-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(0, 130, 179, 0.2);
}

/* More Indicator - Professional touch for hidden birthdays */
.more-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.6rem;
    background: #f8fafc;
    border-radius: 40px;
    margin-top: 0.25rem;
    border: 1px dashed #cbd5e1;
    color: #64748b;
    font-size: 0.7rem;
}

.more-indicator i {
    color: #0082b3;
    font-size: 0.7rem;
}

.more-indicator span {
    font-weight: 500;
}

/* Empty States */
.empty-col {
    background: #fafcff;
    border-radius: 20px;
    padding: 1.5rem;
    text-align: center;
    border: 1px dashed #e2e8f0;
}

.empty-col i {
    font-size: 1.5rem;
    color: #cbd5e1;
    margin-bottom: 0.5rem;
}

.empty-col p {
    color: #64748b;
    font-size: 0.75rem;
    margin: 0;
}

.empty-state {
    text-align: center;
    padding: 2.5rem;
    background: #fafcff;
    border-radius: 32px;
}

.empty-icon i {
    font-size: 3rem;
    background: linear-gradient(135deg, #0082b3, #6aa61d);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 1rem;
}

.empty-state h3 {
    font-size: 1.1rem;
    color: #1e293b;
    margin-bottom: 0.25rem;
}

.empty-state p {
    color: #64748b;
    font-size: 0.8rem;
    margin-bottom: 1rem;
}

.month-chips {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.month-chip {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 30px;
    padding: 0.3rem 0.8rem;
    font-size: 0.7rem;
    font-weight: 500;
    color: #0082b3;
    transition: all 0.2s;
    cursor: pointer;
}

.month-chip span {
    background: #f1f5f9;
    color: #475569;
    padding: 0.05rem 0.35rem;
    border-radius: 20px;
    margin-left: 0.3rem;
    font-size: 0.6rem;
}

.month-chip:hover {
    background: linear-gradient(135deg, #0082b3, #6aa61d);
    border-color: transparent;
    color: white;
}

.month-chip:hover span {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

/* View All Button */
.view-all-wrapper {
    text-align: center;
    margin-top: 2rem;
}

.view-all-button {
    background: transparent;
    border: 1.5px solid #e2e8f0;
    border-radius: 40px;
    padding: 0.5rem 1.5rem;
    font-size: 0.8rem;
    font-weight: 500;
    color: #334155;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
    cursor: pointer;
}

.view-all-button span {
    background: #f1f5f9;
    color: #0082b3;
    padding: 0.1rem 0.4rem;
    border-radius: 30px;
    font-size: 0.7rem;
}

.view-all-button:hover {
    border-color: #0082b3;
    background: #f8fafc;
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
    .birthday-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .birthday-section {
        padding: 1.5rem 0;
        margin: 1rem 0;
    }
    
    .birthday-section .container {
        padding: 0 1rem;
    }
    
    .section-title h2 {
        font-size: 1.5rem;
    }
    
    .card-avatar img,
    .avatar-fallback {
        width: 42px;
        height: 42px;
    }
    
    .card-name {
        font-size: 0.85rem;
    }
}

@media (max-width: 480px) {
    .birthday-card {
        padding: 0.6rem 0.8rem;
        gap: 0.7rem;
    }
    
    .card-avatar img,
    .avatar-fallback {
        width: 38px;
        height: 38px;
    }
    
    .card-name {
        font-size: 0.8rem;
    }
    
    .day-badge {
        width: 26px;
        height: 26px;
        font-size: 0.7rem;
    }
    
    .col-header h3 {
        font-size: 1rem;
    }
}
</style>

<script>
function sendWishes(birthdayId) {
    alert('🎉 Thank you! Your warm wishes will be shared with the birthday person.');
}

// View all birthdays button handler (you can implement modal or redirect)
document.addEventListener('DOMContentLoaded', function() {
    const viewAllBtn = document.getElementById('viewMonthBirthdays');
    if (viewAllBtn) {
        viewAllBtn.addEventListener('click', function() {
            const month = this.dataset.month;
            const monthName = this.dataset.monthName;
            // Implement your modal or redirect here
            alert('Showing all birthdays for ' + monthName);
        });
    }
    
    // Month chip handlers
    document.querySelectorAll('.view-month-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const month = this.dataset.month;
            const monthName = this.dataset.monthName;
            alert('Showing birthdays for ' + monthName);
        });
    });
});
</script>

<!-- <style>
/* ========================================
   CLEAN CENTERED BIRTHDAY SECTION - SMART & COMPACT
   ======================================== 

.birthday-section {
    padding: 2rem 0;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 40px;
    margin: 2rem 0;
    position: relative;
}

.birthday-section .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

/* Section Title */
.section-title h2 {
    font-size: 1.8rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 0.75rem;
}

.title-line {
    width: 60px;
    height: 3px;
    background: linear-gradient(90deg, #0082b3, #6aa61d);
    border-radius: 3px;
}

/* Two Column Grid */
.birthday-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-top: 1.5rem;
}

/* Column Styling */
.birthday-col {
    background: transparent;
}

.col-header {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #e2e8f0;
}

.col-header-icon {
    width: 36px;
    height: 36px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}

.today-icon {
    background: linear-gradient(135deg, #ffd700, #f59e0b);
    color: white;
}

.upcoming-icon {
    background: linear-gradient(135deg, #0082b3, #3b82f6);
    color: white;
}

.col-header h3 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
}

.count-badge {
    background: #e2e8f0;
    color: #475569;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.2rem 0.5rem;
    border-radius: 30px;
}

.today-count {
    background: #fef3c7;
    color: #d97706;
}

.upcoming-count {
    background: #e0f2fe;
    color: #0284c7;
}

/* Cards Container */
.cards-container {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

/* Individual Birthday Card - SMALL & SMART */
.birthday-card {
    background: white;
    border-radius: 20px;
    display: flex;
    align-items: center;
    gap: 0.875rem;
    padding: 0.75rem 1rem;
    transition: all 0.25s ease;
    border: 1px solid #eef2ff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
}

.birthday-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    border-color: #e2e8f0;
}

.birthday-card.today {
    background: linear-gradient(135deg, #fffaf5, #ffffff);
    border-left: 3px solid #ffd700;
}

.birthday-card.upcoming {
    border-left: 3px solid transparent;
}

.birthday-card.upcoming:hover {
    border-left-color: #0082b3;
}

/* Avatar - SMALL */
.card-avatar {
    position: relative;
    flex-shrink: 0;
}

.card-avatar img {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid white;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}

.avatar-fallback {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #eef2ff, #e6f0fa);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #0082b3;
    font-size: 1.2rem;
}

.avatar-badge {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid white;
    font-size: 0.55rem;
    background: #ffd700;
    color: white;
}

.avatar-badge.upcoming-badge {
    background: #0082b3;
}

/* Card Content */
.card-content {
    flex: 1;
    min-width: 0;
}

.card-row {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 0.5rem;
    margin-bottom: 0.2rem;
}

.card-name {
    font-size: 0.9rem;
    font-weight: 600;
    color: #0f172a;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.day-badge {
    width: 30px;
    height: 30px;
    background: linear-gradient(135deg, #0082b3, #6aa61d);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 0.8rem;
    flex-shrink: 0;
}

.day-badge.light {
    background: #f1f5f9;
    color: #0082b3;
}

.card-dept {
    font-size: 0.65rem;
    color: #64748b;
    margin: 0 0 0.2rem 0;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.card-dept i {
    font-size: 0.6rem;
    color: #94a3b8;
}

.card-date {
    font-size: 0.65rem;
    color: #475569;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    flex-wrap: wrap;
    margin-bottom: 0.3rem;
}

.card-date i {
    color: #0082b3;
    font-size: 0.6rem;
}

.tag-today {
    background: #fef3c7;
    color: #d97706;
    font-size: 0.55rem;
    font-weight: 600;
    padding: 0.1rem 0.4rem;
    border-radius: 30px;
}

.tag-upcoming {
    background: #e0f2fe;
    color: #0284c7;
    font-size: 0.55rem;
    font-weight: 600;
    padding: 0.1rem 0.4rem;
    border-radius: 30px;
}

/* Message - Compact */
.card-message {
    background: #fafcff;
    border-radius: 12px;
    padding: 0.3rem 0.5rem;
    margin-top: 0.3rem;
    display: flex;
    gap: 0.4rem;
    align-items: flex-start;
}

.card-message i {
    color: #94a3b8;
    font-size: 0.55rem;
    margin-top: 0.1rem;
}

.card-message p {
    font-size: 0.6rem;
    color: #475569;
    margin: 0;
    line-height: 1.35;
    font-style: italic;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Wish Button - Small */
.wish-button {
    background: linear-gradient(135deg, #0082b3, #6aa61d);
    border: none;
    border-radius: 30px;
    padding: 0.25rem 0.75rem;
    font-size: 0.6rem;
    font-weight: 500;
    color: white;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    margin-top: 0.4rem;
    transition: all 0.2s;
    cursor: pointer;
}

.wish-button i {
    font-size: 0.55rem;
}

.wish-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(0, 130, 179, 0.2);
}

/* More Indicator - Professional touch for hidden birthdays */
.more-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.6rem;
    background: #f8fafc;
    border-radius: 40px;
    margin-top: 0.25rem;
    border: 1px dashed #cbd5e1;
    color: #64748b;
    font-size: 0.7rem;
}

.more-indicator i {
    color: #0082b3;
    font-size: 0.7rem;
}

.more-indicator span {
    font-weight: 500;
}

/* Empty States */
.empty-col {
    background: #fafcff;
    border-radius: 20px;
    padding: 1.5rem;
    text-align: center;
    border: 1px dashed #e2e8f0;
}

.empty-col i {
    font-size: 1.5rem;
    color: #cbd5e1;
    margin-bottom: 0.5rem;
}

.empty-col p {
    color: #64748b;
    font-size: 0.75rem;
    margin: 0;
}

.empty-state {
    text-align: center;
    padding: 2.5rem;
    background: #fafcff;
    border-radius: 32px;
}

.empty-icon i {
    font-size: 3rem;
    background: linear-gradient(135deg, #0082b3, #6aa61d);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 1rem;
}

.empty-state h3 {
    font-size: 1.1rem;
    color: #1e293b;
    margin-bottom: 0.25rem;
}

.empty-state p {
    color: #64748b;
    font-size: 0.8rem;
    margin-bottom: 1rem;
}

.month-chips {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.month-chip {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 30px;
    padding: 0.3rem 0.8rem;
    font-size: 0.7rem;
    font-weight: 500;
    color: #0082b3;
    transition: all 0.2s;
    cursor: pointer;
}

.month-chip span {
    background: #f1f5f9;
    color: #475569;
    padding: 0.05rem 0.35rem;
    border-radius: 20px;
    margin-left: 0.3rem;
    font-size: 0.6rem;
}

.month-chip:hover {
    background: linear-gradient(135deg, #0082b3, #6aa61d);
    border-color: transparent;
    color: white;
}

.month-chip:hover span {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

/* View All Button */
.view-all-wrapper {
    text-align: center;
    margin-top: 2rem;
}

.view-all-button {
    background: transparent;
    border: 1.5px solid #e2e8f0;
    border-radius: 40px;
    padding: 0.5rem 1.5rem;
    font-size: 0.8rem;
    font-weight: 500;
    color: #334155;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
    cursor: pointer;
}

.view-all-button span {
    background: #f1f5f9;
    color: #0082b3;
    padding: 0.1rem 0.4rem;
    border-radius: 30px;
    font-size: 0.7rem;
}

.view-all-button:hover {
    border-color: #0082b3;
    background: #f8fafc;
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
    .birthday-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .birthday-section {
        padding: 1.5rem 0;
        margin: 1rem 0;
    }
    
    .birthday-section .container {
        padding: 0 1rem;
    }
    
    .section-title h2 {
        font-size: 1.5rem;
    }
    
    .card-avatar img,
    .avatar-fallback {
        width: 42px;
        height: 42px;
    }
    
    .card-name {
        font-size: 0.85rem;
    }
}

@media (max-width: 480px) {
    .birthday-card {
        padding: 0.6rem 0.8rem;
        gap: 0.7rem;
    }
    
    .card-avatar img,
    .avatar-fallback {
        width: 38px;
        height: 38px;
    }
    
    .card-name {
        font-size: 0.8rem;
    }
    
    .day-badge {
        width: 26px;
        height: 26px;
        font-size: 0.7rem;
    }
    
    .col-header h3 {
        font-size: 1rem;
    }
}
</style>

<script>
function sendWishes(birthdayId) {
    alert('🎉 Thank you! Your warm wishes will be shared with the birthday person.');
}

// View all birthdays button handler (you can implement modal or redirect)
document.addEventListener('DOMContentLoaded', function() {
    const viewAllBtn = document.getElementById('viewMonthBirthdays');
    if (viewAllBtn) {
        viewAllBtn.addEventListener('click', function() {
            const month = this.dataset.month;
            const monthName = this.dataset.monthName;
            // Implement your modal or redirect here
            alert('Showing all birthdays for ' + monthName);
        });
    }
    
    // Month chip handlers
    document.querySelectorAll('.view-month-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const month = this.dataset.month;
            const monthName = this.dataset.monthName;
            alert('Showing birthdays for ' + monthName);
        });
    });
});
</script> -->
*/