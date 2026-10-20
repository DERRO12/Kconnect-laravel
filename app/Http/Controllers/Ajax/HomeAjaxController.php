<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Support\HomeData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** AJAX endpoints of the employee home page (ajax/birthday-handler, contact-handler, get-all-spotlights, get_event, news-handler). */
class HomeAjaxController extends Controller
{
    /** ajax/birthday-handler */
    public function birthdayHandler(Request $request)
    {
        // Log the request for debugging

        $action = $request->query->has('action') ? $request->query('action') : '';

        switch ($action) {
            case 'get_monthly_birthdays':
                $month = $request->query->has('month') ? intval($request->query('month')) : date('n');
                return $this->getMonthlyBirthdays($month);

            case 'get_all_birthdays':
                return $this->getAllBirthdays();

            default:
                return kc_json([
                    'success' => false, 
                    'message' => 'Invalid action',
                    'debug' => [
                        'action_received' => $action,
                        'get_params' => $request->query->all()
                    ]
                ]);
        }
    }

    private function getMonthlyBirthdays($month)
    {
    // Validate month
    if ($month < 1 || $month > 12) {
        $month = date('n');
    }
    
    // Get birthdays for specific month
    try {
        $result = DB::select("SELECT * FROM birthdays 
              WHERE status = 1 
              AND MONTH(birth_date) = ?
              ORDER BY DAY(birth_date) ASC", [$month]);
    } catch (\Illuminate\Database\QueryException $e) {
        return kc_json([
            'success' => false, 
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
    
    $monthName = date('F', mktime(0, 0, 0, $month, 1));
    $currentYear = date('Y');
    $currentMonth = date('n');
    $currentDay = date('j');
    
    if (count($result) > 0) {
        $html = '<div class="monthly-birthdays-header mb-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="fw-bold mb-1"><i class="fas fa-calendar-alt me-2" style="color: var(--kiboko-blue);"></i>' . $monthName . ' ' . $currentYear . '</h4>
                            <p class="text-muted mb-0">Celebrating ' . count($result) . ' team member' . (count($result) > 1 ? 's' : '') . ' this month</p>
                        </div>
                        <div class="month-badge">
                            <span class="badge bg-gradient text-white px-4 py-2">' . $monthName . '</span>
                        </div>
                    </div>
                 </div>';
        
        $html .= '<div class="birthdays-monthly-grid">';
        
        foreach ($result as $bday) {
            $bdayDate = strtotime($bday['birth_date']);
            $bdayDay = date('j', $bdayDate);
            $bdayMonth = date('n', $bdayDate);
            $bdayFormatted = date('F j', $bdayDate);
            
            $isToday = ($bdayMonth == $currentMonth && $bdayDay == $currentDay);
            $isUpcoming = ($bdayMonth == $currentMonth && $bdayDay >= $currentDay && !$isToday);
            
            // Birthday photos are saved by admin/manage-birthdays in admin/uploads/ (older ones in uploads/)
            $photoHtml = '';
            if (!empty($bday['photo'])) {
                $photoPath = '';
                foreach (['admin/uploads/', 'uploads/'] as $photoDir) {
                    if (file_exists(public_path($photoDir . $bday['photo']))) {
                        $photoPath = htmlspecialchars(asset($photoDir . $bday['photo']));
                        break;
                    }
                }
                if ($photoPath !== '') {
                    $photoHtml = '<img src="' . $photoPath . '" class="rounded-circle" width="60" height="60" style="object-fit: cover; border: 3px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">';
                } else {
                    $photoHtml = '<div class="rounded-circle bg-gradient d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: linear-gradient(135deg, #009ada, #76bc21);">
                                    <i class="fas fa-user fa-2x text-white"></i>
                                </div>';
                }
            } else {
                $photoHtml = '<div class="rounded-circle bg-gradient d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: linear-gradient(135deg, #009ada, #76bc21);">
                                <i class="fas fa-user fa-2x text-white"></i>
                            </div>';
            }
            
            $html .= '
            <div class="birthday-monthly-card ' . ($isToday ? 'today-highlight' : '') . '">
                <div class="d-flex">
                    <div class="birthday-day-badge me-3">
                        <span class="day">' . $bdayDay . '</span>
                        <span class="month">' . substr($monthName, 0, 3) . '</span>
                    </div>
                    
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                ' . $photoHtml . '
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fw-bold mb-1">' . htmlspecialchars($bday['full_name']) . '</h5>
                                <p class="small text-muted mb-2">
                                    <i class="fas fa-building me-1" style="color: var(--kiboko-blue);"></i>' . htmlspecialchars($bday['department'] ?: 'Team Member') . '
                                </p>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-light text-dark">
                                        <i class="far fa-calendar-alt me-1"></i>' . $bdayFormatted . '
                                    </span>';
                                    
            if ($isToday) {
                $html .= '<span class="badge bg-danger pulse-animation">🎂 Today!</span>';
            } elseif ($isUpcoming) {
                $daysUntil = $bdayDay - $currentDay;
                $html .= '<span class="badge bg-warning">⏰ In ' . $daysUntil . ' day' . ($daysUntil > 1 ? 's' : '') . '</span>';
            }
            
            $html .= '        </div>
                            </div>
                        </div>';
                        
            if (!empty($bday['message'])) {
                $html .= '
                        <div class="birthday-message-box mt-3">
                            <i class="fas fa-quote-left quote-icon"></i>
                            <p class="mb-0 small">' . htmlspecialchars($bday['message']) . '</p>
                        </div>';
            }
            
            $html .= '
                    </div>
                </div>
            </div>';
        }
        
        $html .= '</div>';
        
        // Month navigation
        $prevMonth = $month == 1 ? 12 : $month - 1;
        $nextMonth = $month == 12 ? 1 : $month + 1;
        $prevMonthName = date('F', mktime(0, 0, 0, $prevMonth, 1));
        $nextMonthName = date('F', mktime(0, 0, 0, $nextMonth, 1));
        
        $html .= '
        <div class="month-navigation mt-4 pt-3 border-top">
            <div class="row align-items-center">
                <div class="col-4">
                    <button class="btn btn-outline-gradient btn-sm w-100 change-month" data-month="' . $prevMonth . '" data-month-name="' . $prevMonthName . '">
                        <i class="fas fa-chevron-left me-2"></i>' . substr($prevMonthName, 0, 3) . '
                    </button>
                </div>
                <div class="col-4 text-center">
                    <span class="fw-bold text-gradient">' . $monthName . ' ' . $currentYear . '</span>
                </div>
                <div class="col-4 text-end">
                    <button class="btn btn-outline-gradient btn-sm w-100 change-month" data-month="' . $nextMonth . '" data-month-name="' . $nextMonthName . '">
                        ' . substr($nextMonthName, 0, 3) . '<i class="fas fa-chevron-right ms-2"></i>
                    </button>
                </div>
            </div>
        </div>';
        
        return kc_json(['success' => true, 'html' => $html, 'count' => count($result)]);
    } else {
        $html = '<div class="text-center py-5">
                    <div class="empty-state-icon mb-4">
                        <i class="fas fa-birthday-cake fa-5x" style="color: var(--kiboko-blue); opacity: 0.3;"></i>
                    </div>
                    <h5 class="text-muted mb-3">No Birthdays in ' . $monthName . '</h5>
                    <p class="text-muted mb-4">Check out other months for celebrations!</p>
                 </div>';
        
        return kc_json(['success' => true, 'html' => $html, 'count' => 0]);
    }
    }

    private function getAllBirthdays()
    {
    // Simple version for now
    return kc_json(['success' => false, 'message' => 'Coming soon']);
    }

    /** ajax/contact-handler */
    public function contactHandler(Request $request)
    {
        $action = $request->query->has('action') ? $request->query('action') : '';

        switch ($action) {
            case 'get_contact':
                $id = $request->query->has('id') ? intval($request->query('id')) : 0;
                if ($id > 0) {
                    return $this->getContactDetails($id);
                }
                return response('', 200, ['Content-Type' => 'application/json']);

            default:
                return kc_json(['success' => false, 'message' => 'Invalid action']);
        }
    }

    private function getContactDetails($id)
    {
    $contact = DB::selectOne("SELECT * FROM key_contacts WHERE id = ? AND status = 1", [$id]);
    
    if ($contact) {
        
        // Format contact details
        $photoHtml = '';
        if (!empty($contact['photo']) && file_exists(kc_public_file('../uploads/contacts/' . $contact['photo'], 'ajax'))) {
            $photoHtml = '<img src="' . htmlspecialchars(asset('uploads/contacts/' . $contact['photo'])) . '" alt="' . htmlspecialchars($contact['name']) . '" class="contact-modal-photo">';
        } else {
            $photoHtml = '<div class="contact-modal-photo-placeholder">
                            <i class="fas fa-user fa-4x"></i>
                        </div>';
        }
        
        $html = '
        <div class="contact-modal-details">
            <div class="text-center mb-4">
                ' . $photoHtml . '
                <h3 class="contact-modal-name mt-3">' . htmlspecialchars($contact['name']) . '</h3>
                <p class="contact-modal-position">' . htmlspecialchars($contact['position']) . '</p>
                ' . (!empty($contact['department']) ? '<span class="badge bg-gradient text-white px-3 py-2">' . htmlspecialchars($contact['department']) . '</span>' : '') . '
            </div>
            
            <div class="contact-info-grid">';
        
        // Email
        if (!empty($contact['email'])) {
            $html .= '
                <div class="contact-info-item">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="info-content">
                        <small class="text-muted">Email</small>
                        <p class="mb-0"><a href="mailto:' . htmlspecialchars($contact['email']) . '">' . htmlspecialchars($contact['email']) . '</a></p>
                    </div>
                </div>';
        }
        
        // Phone
        if (!empty($contact['phone'])) {
            $html .= '
                <div class="contact-info-item">
                    <div class="info-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div class="info-content">
                        <small class="text-muted">Phone</small>
                        <p class="mb-0"><a href="tel:' . htmlspecialchars($contact['phone']) . '">' . htmlspecialchars($contact['phone']) . '</a></p>
                    </div>
                </div>';
        }
        
        // Extension
        if (!empty($contact['extension'])) {
            $html .= '
                <div class="contact-info-item">
                    <div class="info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="info-content">
                        <small class="text-muted">Extension</small>
                        <p class="mb-0"><strong>' . htmlspecialchars($contact['extension']) . '</strong></p>
                    </div>
                </div>';
        }
        
        // Mobile
        if (!empty($contact['mobile'])) {
            $html .= '
                <div class="contact-info-item">
                    <div class="info-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div class="info-content">
                        <small class="text-muted">Mobile</small>
                        <p class="mb-0"><a href="tel:' . htmlspecialchars($contact['mobile']) . '">' . htmlspecialchars($contact['mobile']) . '</a></p>
                    </div>
                </div>';
        }
        
        // Office Location
        if (!empty($contact['office_location'])) {
            $html .= '
                <div class="contact-info-item">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="info-content">
                        <small class="text-muted">Office</small>
                        <p class="mb-0">' . htmlspecialchars($contact['office_location']) . '</p>
                    </div>
                </div>';
        }
        
        // Office Hours
        if (!empty($contact['office_hours'])) {
            $html .= '
                <div class="contact-info-item">
                    <div class="info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="info-content">
                        <small class="text-muted">Hours</small>
                        <p class="mb-0">' . htmlspecialchars($contact['office_hours']) . '</p>
                    </div>
                </div>';
        }
        
        $html .= '</div>';
        
        // Bio
        if (!empty($contact['bio'])) {
            $html .= '
            <div class="contact-bio mt-4 p-3 bg-light rounded">
                <p class="mb-0"><i class="fas fa-quote-left me-2" style="color: var(--kiboko-blue);"></i>' . nl2br(htmlspecialchars($contact['bio'])) . '</p>
            </div>';
        }
        
        $html .= '
            <div class="contact-actions mt-4 d-flex gap-2 justify-content-center">
                <a href="mailto:' . htmlspecialchars($contact['email']) . '" class="btn btn-gradient btn-sm">
                    <i class="fas fa-envelope me-2"></i>Email
                </a>
                ' . (!empty($contact['phone']) ? '
                <a href="tel:' . htmlspecialchars($contact['phone']) . '" class="btn btn-outline-gradient btn-sm">
                    <i class="fas fa-phone me-2"></i>Call
                </a>' : '') . '
            </div>
        </div>';
        
        return kc_json(['success' => true, 'html' => $html, 'data' => $contact]);
    } else {
        return kc_json(['success' => false, 'message' => 'Contact not found']);
    }
    }

    /** ajax/get-all-spotlights */
    public function getAllSpotlights(Request $request)
    {
        // Get all active spotlights
        $spotlights = HomeData::allSpotlights();

        if (count($spotlights) == 0) {
            return response('<div class="text-center py-5">
            <div class="display-1 mb-4">⭐</div>
            <h3 class="text-muted">No spotlights yet</h3>
            <p class="text-muted">Check back soon for employee achievements!</p>
          </div>');
        }

        return view('ajax.get-all-spotlights', compact('spotlights'));
    }

    /** ajax/get_event */
    public function getEvent(Request $request)
    {
        if ($request->query->has('id')) {
            $id = (int) $request->query('id');

            $row = DB::selectOne('SELECT * FROM event_items WHERE id = ? AND status = 1', [$id]);

            if ($row) {
                return view('ajax.get_event', compact('row'));
            }

            return response('<div class="text-center py-5">
                <div class="mb-3"><i class="fas fa-calendar-times fa-4x text-muted"></i></div>
                <h5 class="text-muted">Event not found</h5>
                <p class="text-muted small">The event you\'re looking for doesn\'t exist</p>
              </div>');
        }

        return response('<div class="text-center py-5">
            <div class="mb-3"><i class="fas fa-exclamation-circle fa-4x text-muted"></i></div>
            <h5 class="text-muted">Invalid request</h5>
            <p class="text-muted small">Please try again</p>
          </div>');
    }

    /** ajax/news-handler */
    public function newsHandler(Request $request)
    {
        $action = $request->query->has('action') ? $request->query('action') : '';
        $empty = response('', 200, ['Content-Type' => 'application/json']);

        switch ($action) {
            case 'get_news':
                $newsId = $request->query->has('id') ? intval($request->query('id')) : 0;
                if ($newsId > 0) {
                    return $this->getNewsDetail($newsId);
                }
                return $empty;

            case 'get_all_news':
                return $this->getAllNews();

            case 'get_event':
                $eventId = $request->query->has('id') ? intval($request->query('id')) : 0;
                if ($eventId > 0) {
                    return $this->getEventDetail($eventId);
                }
                return $empty;

            case 'get_all_events':
                return $this->getAllEvents();

            case 'like_news':
                $newsId = $request->request->has('news_id') ? intval($request->post('news_id')) : 0;
                if ($newsId > 0) {
                    return $this->likeNews($newsId);
                }
                return $empty;

            default:
                return kc_json(['success' => false, 'message' => 'Invalid action']);
        }
    }

    private function getNewsDetail($newsId)
    {
    $news = DB::selectOne("SELECT * FROM news_posts WHERE id = ? AND status = 1", [$newsId]);
    
    if ($news) {
        
        // Get category class
        $categoryClass = '';
        switch($news['category']) {
            case 'Announcements': $categoryClass = 'primary'; break;
            case 'Events': $categoryClass = 'success'; break;
            case 'People': $categoryClass = 'warning'; break;
            case 'Milestones': $categoryClass = 'info'; break;
        }
        
        // News images are saved in uploads/news/ (older copies under intranet/uploads/news/, tried on error)
        $imagePath = !empty($news['featured_image']) ? htmlspecialchars(asset('uploads/news/' . $news['featured_image'])) : '';
        $fallbackPath = !empty($news['featured_image']) ? htmlspecialchars(asset('intranet/uploads/news/' . $news['featured_image'])) : '';
        
        $html = '
        <div class="container-fluid p-0">
            <div class="news-detail-header text-center mb-4">';
            
        if (!empty($imagePath)) {
            $html .= '
                <div class="news-detail-image-wrapper mb-4">
                    <img src="' . $imagePath . '" 
                         class="news-detail-image img-fluid rounded" 
                         alt="' . htmlspecialchars($news['title']) . '"
                         onerror="this.onerror=null; this.src=\'' . $fallbackPath . '\'; this.onerror=function(){ this.style.display=\'none\'; this.parentElement.innerHTML=\'<div class=\\\'no-image-placeholder mb-4\\\' style=\\\'height:200px;background:linear-gradient(135deg,#f8fafc 0%,#e9ecef 100%);display:flex;align-items:center;justify-content:center;border-radius:16px;\\\'><i class=\\\'fas fa-newspaper fa-5x text-gradient\\\'></i></div>\'; }"
                         style="max-height: 400px; width: 100%; object-fit: contain; background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%); padding: 10px; border-radius: 16px;">
                </div>';
        } else {
            $html .= '
                <div class="no-image-placeholder mb-4" style="height: 200px; background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%); display: flex; align-items: center; justify-content: center; border-radius: 16px;">
                    <i class="fas fa-newspaper fa-5x text-gradient"></i>
                </div>';
        }
        
        $html .= '
                <h2 class="news-detail-title fw-bold mb-3">' . htmlspecialchars($news['title']) . '</h2>
            </div>
            
            <div class="news-detail-meta mb-4 p-3 bg-light rounded">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="author-info">
                                <h6 class="mb-0 fw-bold"><i class="fas fa-user-circle me-2 text-gradient"></i>' . htmlspecialchars($news['author_name']) . '</h6>
                                ' . (!empty($news['author_role']) ? '<small class="text-muted ms-4">' . htmlspecialchars($news['author_role']) . '</small>' : '') . '
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <span class="badge bg-' . $categoryClass . ' text-white px-3 py-2 mb-2">
                            ' . $news['category'] . '
                        </span>
                        <div class="text-muted small">
                            <i class="far fa-calendar-alt me-1"></i>
                            ' . date('F j, Y', strtotime($news['created_at'])) . '
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="news-detail-content mb-4 p-3">
                ' . nl2br(htmlspecialchars($news['content'])) . '
            </div>
            
            <div class="news-detail-stats d-flex justify-content-between align-items-center p-3 bg-light rounded">
                <div class="d-flex gap-3">
                    <span class="badge bg-white text-dark p-3 shadow-sm">
                        <i class="far fa-comment me-2"></i> ' . $news['comments_count'] . ' Comments
                    </span>
                    <span class="badge bg-white text-dark p-3 shadow-sm like-button-detail" data-news-id="' . $news['id'] . '" style="cursor: pointer;">
                        <i class="far fa-heart me-2"></i> <span class="like-count">' . $news['likes_count'] . '</span> Likes
                    </span>
                </div>
            </div>
        </div>';
        
        return kc_json(['success' => true, 'html' => $html, 'data' => $news]);
    } else {
        return kc_json(['success' => false, 'message' => 'News not found']);
    }
    }

    private function getAllNews()
    {
    $result = DB::select("SELECT * FROM news_posts WHERE status = 1 ORDER BY created_at DESC");
    
    if (count($result) > 0) {
        $html = '<div class="row g-4">';
        
        foreach ($result as $news) {
            $categoryClass = '';
            switch($news['category']) {
                case 'Announcements': $categoryClass = 'primary'; break;
                case 'Events': $categoryClass = 'success'; break;
                case 'People': $categoryClass = 'warning'; break;
                case 'Milestones': $categoryClass = 'info'; break;
            }
            
            // News images are saved in uploads/news/ (older copies under intranet/uploads/news/, tried on error)
            $imagePath = !empty($news['featured_image']) ? htmlspecialchars(asset('uploads/news/' . $news['featured_image'])) : '';
            $fallbackPath = !empty($news['featured_image']) ? htmlspecialchars(asset('intranet/uploads/news/' . $news['featured_image'])) : '';
            
            $html .= '
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm news-card-all" style="cursor: pointer;" data-news-id="' . $news['id'] . '" data-bs-dismiss="modal">';
            
            if (!empty($imagePath)) {
                $html .= '
                    <div class="news-card-image-wrapper" style="height: 200px; overflow: hidden; border-top-left-radius: 16px; border-top-right-radius: 16px;">
                        <img src="' . $imagePath . '" 
                             class="card-img-top news-card-image" 
                             alt="' . htmlspecialchars($news['title']) . '"
                             onerror="this.onerror=null; this.src=\'' . $fallbackPath . '\'; this.onerror=function(){ this.parentElement.innerHTML=\'<div class=\\\'d-flex align-items-center justify-content-center\\\' style=\\\'height:200px;background:linear-gradient(135deg,#f8fafc 0%,#e9ecef 100%)\\\'><i class=\\\'fas fa-newspaper fa-4x text-gradient\\\'></i></div>\'; }"
                             style="height: 200px; width: 100%; object-fit: cover;">
                    </div>';
            } else {
                $html .= '
                    <div class="d-flex align-items-center justify-content-center" style="height: 200px; background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%); border-top-left-radius: 16px; border-top-right-radius: 16px;">
                        <i class="fas fa-newspaper fa-4x text-gradient"></i>
                    </div>';
            }
            
            $html .= '
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-' . $categoryClass . ' text-white px-3 py-2">' . $news['category'] . '</span>
                            <small class="text-muted">' . date('M d, Y', strtotime($news['created_at'])) . '</small>
                        </div>
                        <h5 class="card-title fw-bold mb-2">' . htmlspecialchars($news['title']) . '</h5>
                        <p class="card-text text-muted small">' . substr(htmlspecialchars($news['content']), 0, 100) . '...</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small class="text-muted">
                                <i class="far fa-user me-1"></i> ' . htmlspecialchars($news['author_name']) . '
                            </small>
                            <div class="stats">
                                <span class="me-2" title="Comments">
                                    <i class="far fa-comment"></i> ' . $news['comments_count'] . '
                                </span>
                                <span title="Likes">
                                    <i class="far fa-heart"></i> ' . $news['likes_count'] . '
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
        }
        
        $html .= '</div>';
        return kc_json(['success' => true, 'html' => $html]);
    } else {
        return kc_json(['success' => false, 'html' => '<p class="text-center text-muted py-5">No news available</p>']);
    }
    }

    private function getEventDetail($eventId)
    {
    $event = DB::selectOne("SELECT * FROM event_items WHERE id = ? AND status = 1", [$eventId]);
    
    if ($event) {
        
        $eventDate = strtotime($event['event_date']);
        $formattedDate = date('F j, Y', $eventDate);
        $dayOfWeek = date('l', $eventDate);
        
        $isPast = strtotime($event['event_date']) < strtotime(date('Y-m-d'));
        $isToday = date('Y-m-d') == $event['event_date'];
        $isTomorrow = date('Y-m-d', strtotime('+1 day')) == $event['event_date'];
        
        $dateStatus = '';
        if($isToday) {
            $dateStatus = '<span class="badge bg-danger mb-2">Today!</span>';
        } elseif($isTomorrow) {
            $dateStatus = '<span class="badge bg-warning mb-2">Tomorrow!</span>';
        } elseif($isPast) {
            $dateStatus = '<span class="badge bg-secondary mb-2">Past Event</span>';
        }
        
        $html = '
        <div class="container-fluid p-0">
            <div class="event-detail-header text-center mb-4">
                <div class="event-icon mb-3">
                    <i class="fas fa-calendar-alt fa-4x text-gradient"></i>
                </div>
                
                <h2 class="event-detail-title fw-bold mb-3">' . htmlspecialchars($event['title']) . '</h2>
                ' . $dateStatus . '
            </div>
            
            <div class="event-detail-info p-4 bg-light rounded mb-4">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="info-icon me-3">
                                <i class="fas fa-calendar-day fa-2x text-gradient"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Date</small>
                                <strong>' . $formattedDate . ' (' . $dayOfWeek . ')</strong>
                            </div>
                        </div>
                    </div>
                    
                    ' . (!empty($event['event_time']) ? '
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="info-icon me-3">
                                <i class="fas fa-clock fa-2x text-gradient"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Time</small>
                                <strong>' . date('h:i A', strtotime($event['event_time'])) . '</strong>
                            </div>
                        </div>
                    </div>
                    ' : '') . '
                    
                    ' . (!empty($event['location']) ? '
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="info-icon me-3">
                                <i class="fas fa-map-marker-alt fa-2x text-gradient"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Location</small>
                                <strong>' . htmlspecialchars($event['location']) . '</strong>
                            </div>
                        </div>
                    </div>
                    ' : '') . '
                    
                    ' . (!empty($event['category']) ? '
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="info-icon me-3">
                                <i class="fas fa-tag fa-2x text-gradient"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Category</small>
                                <strong>' . htmlspecialchars($event['category']) . '</strong>
                            </div>
                        </div>
                    </div>
                    ' : '') . '
                </div>
            </div>
            
            ' . (!empty($event['description']) ? '
            <div class="event-detail-description mb-4">
                <h6 class="fw-bold mb-3">Description</h6>
                <p class="text-muted">' . nl2br(htmlspecialchars($event['description'])) . '</p>
            </div>
            ' : '') . '
            
            ' . ($event['rsvp_required'] ? '
            <div class="event-rsvp text-center mt-4">
                <a href="' . (!empty($event['rsvp_url']) ? htmlspecialchars($event['rsvp_url']) : '#') . '" class="btn btn-gradient px-5 py-3" ' . (empty($event['rsvp_url']) ? 'onclick="return false;"' : 'target="_blank"') . '>
                    <i class="fas fa-check-circle me-2"></i>RSVP Now
                </a>
                ' . (empty($event['rsvp_url']) ? '<p class="text-muted small mt-2">No RSVP link provided. Please contact organizer.</p>' : '') . '
            </div>
            ' : '') . '
        </div>';
        
        return kc_json(['success' => true, 'html' => $html, 'data' => $event]);
    } else {
        return kc_json(['success' => false, 'message' => 'Event not found']);
    }
    }

    private function getAllEvents()
    {
    $result = DB::select("SELECT * FROM event_items WHERE status = 1 ORDER BY event_date ASC");
    
    if (count($result) > 0) {
        $html = '<div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Location</th>
                        <th>Category</th>
                        <th>RSVP</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($result as $event) {
            $eventDate = strtotime($event['event_date']);
            $formattedDate = date('M d, Y', $eventDate);
            $isPast = strtotime($event['event_date']) < strtotime(date('Y-m-d'));
            
            $rowClass = $isPast ? 'text-muted' : '';
            
            $html .= '
            <tr class="' . $rowClass . '">
                <td>
                    <strong>' . htmlspecialchars($event['title']) . '</strong>
                    ' . ($isPast ? '<span class="badge bg-secondary ms-2">Past</span>' : '') . '
                </td>
                <td>' . $formattedDate . '</td>
                <td>' . (!empty($event['event_time']) ? date('h:i A', strtotime($event['event_time'])) : '-') . '</td>
                <td>' . (!empty($event['location']) ? htmlspecialchars($event['location']) : '-') . '</td>
                <td><span class="badge bg-light text-dark">' . htmlspecialchars($event['category']) . '</span></td>
                <td>' . ($event['rsvp_required'] ? '<span class="badge bg-success">Required</span>' : '<span class="badge bg-secondary">Optional</span>') . '</td>
                <td>
                    <button class="btn btn-sm btn-outline-gradient view-event-detail" data-event-id="' . $event['id'] . '" data-bs-dismiss="modal">
                        <i class="fas fa-eye"></i> View
                    </button>
                </td>
            </tr>';
        }
        
        $html .= '</tbody></table></div>';
        return kc_json(['success' => true, 'html' => $html]);
    } else {
        return kc_json(['success' => false, 'html' => '<p class="text-center text-muted py-5">No events available</p>']);
    }
    }

    private function likeNews($newsId)
    {
    // Get client IP
    $ip_address = request()->ip();
    
    // Check if already liked
    $checkResult = DB::select("SELECT id FROM news_likes WHERE news_id = ? AND ip_address = ?", [$newsId, $ip_address]);
    
    if (count($checkResult) == 0) {
        // Add like
        DB::insert("INSERT INTO news_likes (news_id, ip_address) VALUES (?, ?)", [$newsId, $ip_address]);
        
        // Update like count in news_posts
        DB::update("UPDATE news_posts SET likes_count = likes_count + 1 WHERE id = ?", [$newsId]);
        
        // Get new count
        $count = DB::selectOne("SELECT likes_count FROM news_posts WHERE id = ?", [$newsId]);
        
        return kc_json(['success' => true, 'likes' => $count['likes_count']]);
    } else {
        return kc_json(['success' => false, 'message' => 'Already liked']);
    }
    }
}
