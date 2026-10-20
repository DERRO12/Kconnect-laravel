<section class="key-contacts-section my-5">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2><i class="fas fa-address-book me-2"></i>Emergency Contacts</h2>
            <div class="title-line"></div>
            <p class="text-muted mt-3">Emergency contacts and important people you need to reach</p>
        </div>

        {{-- $keyContacts: HomeData::keyContacts() (SELECT * FROM key_contacts WHERE status = 1 ORDER BY category, sort_order ASC) --}}
        @if (count($keyContacts) > 0)
        <div class="contacts-horizontal-scroll" data-aos="fade-up">
            <div class="contacts-wrapper">
                @foreach ($keyContacts as $contact)
                <div class="contact-card" data-category="{{ $contact['category'] }}">
                    <div class="contact-card-inner">
                        <div class="contact-role-badge">
                            {{ $contact['category'] }}
                        </div>
                        
                        <div class="contact-info-main">
                            <h5 class="contact-name">{{ $contact['name'] }}</h5>
                            <p class="contact-position">{{ $contact['position'] }}</p>
                            
                            @if (!empty($contact['department']))
                            <p class="contact-dept">
                                <i class="fas fa-building"></i> {{ $contact['department'] }}
                            </p>
                            @endif
                        </div>
                        
                        <div class="contact-emergency-details">
                            <!-- 1. PHONE NUMBER (Always First) -->
                            @if (!empty($contact['phone']))
                            <div class="emergency-item">
                                <i class="fas fa-phone-alt"></i>
                                <div class="emergency-content">
                                    <span class="emergency-label">Phone</span>
                                    <a href="tel:{{ $contact['phone'] }}" class="emergency-value phone-number">
                                        {{ $contact['phone'] }}
                                    </a>
                                </div>
                            </div>
                            @endif
                            
                            <!-- 2. EXTENSION (Always Second if exists) -->
                            @if (!empty($contact['extension']))
                            <div class="emergency-item">
                                <i class="fas fa-phone-square"></i>
                                <div class="emergency-content">
                                    <span class="emergency-label">Extension</span>
                                    <span class="emergency-value extension">
                                        {{ $contact['extension'] }}
                                    </span>
                                </div>
                            </div>
                            @endif
                            
                            <!-- 3. EMAIL (Always Third if exists) -->
                            @if (!empty($contact['email']))
                            <div class="emergency-item">
                                <i class="fas fa-envelope"></i>
                                <div class="emergency-content">
                                    <span class="emergency-label">Email</span>
                                    <a href="mailto:{{ $contact['email'] }}" class="emergency-value">
                                        {{ $contact['email'] }}
                                    </a>
                                </div>
                            </div>
                            @endif
                            
                            <!-- 4. MOBILE (Always Fourth - only shown if no phone exists) -->
                            @if (!empty($contact['mobile']) && empty($contact['phone']))
                            <div class="emergency-item">
                                <i class="fas fa-mobile-alt"></i>
                                <div class="emergency-content">
                                    <span class="emergency-label">Mobile</span>
                                    <a href="tel:{{ $contact['mobile'] }}" class="emergency-value">
                                        {{ $contact['mobile'] }}
                                    </a>
                                </div>
                            </div>
                            @endif
                            
                            <!-- 5. OFFICE LOCATION (Always Last) -->
                            @if (!empty($contact['office_location']))
                            <div class="emergency-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <div class="emergency-content">
                                    <span class="emergency-label">Office</span>
                                    <span class="emergency-value">
                                        {{ $contact['office_location'] }}
                                    </span>
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        @if (!empty($contact['office_hours']) || !empty($contact['bio']))
                        <div class="contact-additional-info">
                            @if (!empty($contact['office_hours']))
                            <div class="info-note">
                                <i class="far fa-clock"></i>
                                <span>{{ $contact['office_hours'] }}</span>
                            </div>
                            @endif
                            
                            @if (!empty($contact['bio']))
                            <div class="info-note bio-note">
                                <i class="fas fa-info-circle"></i>
                                <span>{{ $contact['bio'] }}</span>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="text-center py-5" data-aos="fade-up">
            <div class="display-1 mb-4">📞</div>
            <h3 class="text-muted">No contacts available</h3>
            <p class="text-muted">Emergency contact list will appear here</p>
        </div>
        @endif
    </div>
</section>

<style>
/* Key Contacts Section Styles */
.key-contacts-section {
    position: relative;
    padding: 3rem 0;
    background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%);
    border-radius: 30px;
    margin: 2rem 0;
    overflow: hidden;
}

.key-contacts-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #009ada, #76bc21, #009ada);
    background-size: 200% 100%;
    animation: gradientMove 3s ease infinite;
}

@keyframes gradientMove {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.section-title {
    text-align: center;
    margin-bottom: 2rem;
}

.section-title h2 {
    font-size: 2.2rem;
    font-weight: 800;
    background: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    display: inline-block;
    margin-bottom: 0.75rem;
}

.section-title .title-line {
    width: 70px;
    height: 4px;
    background: linear-gradient(90deg, #009ada, #76bc21);
    margin: 0 auto;
    border-radius: 2px;
}

/* Horizontal Scroll Container */
.contacts-horizontal-scroll {
    position: relative;
    width: 100%;
    overflow-x: auto;
    overflow-y: visible;
    padding: 10px 0 20px;
    cursor: grab;
    scroll-behavior: smooth;
    scrollbar-width: thin;
    scrollbar-color: #009ada #e9ecef;
}

.contacts-horizontal-scroll:active {
    cursor: grabbing;
}

.contacts-horizontal-scroll::-webkit-scrollbar {
    height: 6px;
}

.contacts-horizontal-scroll::-webkit-scrollbar-track {
    background: #e9ecef;
    border-radius: 10px;
}

.contacts-horizontal-scroll::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #009ada, #76bc21);
    border-radius: 10px;
}

/* Contacts Wrapper */
.contacts-wrapper {
    display: flex;
    gap: 15px;
    padding: 5px 5px 15px;
}

/* Contact Card - Smaller Size */
.contact-card {
    flex: 0 0 280px;
    min-width: 280px;
    max-width: 280px;
    transition: all 0.3s ease;
    animation: fadeInUp 0.6s ease backwards;
}

.contact-card:hover {
    transform: translateY(-3px);
}

.contact-card-inner {
    background: white;
    border-radius: 16px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    padding: 16px;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid rgba(0,154,218,0.1);
    border-left: 3px solid #009ada;
    height: 100%;
    display: flex;
    flex-direction: column;
}

/* Emergency accent */
.contact-card-inner::after {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 0;
    height: 0;
    border-style: solid;
    border-width: 0 40px 40px 0;
    border-color: transparent #76bc21 transparent transparent;
    opacity: 0.1;
    transition: opacity 0.3s ease;
}

.contact-card:hover .contact-card-inner::after {
    opacity: 0.2;
}

/* Role/Category Badge */
.contact-role-badge {
    display: inline-block;
    background: rgba(0,154,218,0.1);
    color: #009ada;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 20px;
    margin-bottom: 12px;
    border: 1px solid rgba(0,154,218,0.2);
    align-self: flex-start;
}

/* Contact Main Info */
.contact-info-main {
    margin-bottom: 12px;
}

.contact-name {
    font-size: 1.2rem;
    font-weight: 700;
    color: #1a2634;
    margin-bottom: 4px;
    line-height: 1.3;
}

.contact-position {
    font-size: 0.85rem;
    color: #009ada;
    font-weight: 500;
    margin-bottom: 6px;
    text-transform: none;
}

.contact-dept {
    font-size: 0.8rem;
    color: #5a6a7a;
    display: flex;
    align-items: center;
    gap: 5px;
}

.contact-dept i {
    color: #76bc21;
    font-size: 0.8rem;
}

/* Emergency Details Section - Compact with uniform order */
.contact-emergency-details {
    background: #f8fafc;
    border-radius: 12px;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #e9edf2;
    display: flex;
    flex-direction: column;
}

.emergency-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 4px 0;
    border-bottom: 1px solid #e9edf2;
}

.emergency-item:last-child {
    border-bottom: none;
}

.emergency-item i {
    width: 24px;
    height: 24px;
    background: white;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #009ada;
    font-size: 0.85rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    flex-shrink: 0;
}

.emergency-content {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
}

.emergency-label {
    font-size: 0.6rem;
    color: #6c757d;
    font-weight: 600;
    margin-bottom: 1px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.emergency-value {
    font-weight: 600;
    color: #1a2634;
    font-size: 0.85rem;
    text-decoration: none;
    word-break: break-word;
    line-height: 1.3;
}

/* Special styling for phone number */
.emergency-value.phone-number {
    color: #d32f2f;
    font-weight: 700;
}

/* Special styling for extension */
.emergency-value.extension {
    color: #009ada;
    font-weight: 700;
}

.emergency-value a {
    color: #1a2634;
    text-decoration: none;
    transition: color 0.2s ease;
}

.emergency-value a:hover {
    color: #009ada;
    text-decoration: underline;
}

/* Phone icon color */
.emergency-item .fa-phone-alt {
    color: #d32f2f;
}

/* Extension icon color */
.emergency-item .fa-phone-square {
    color: #76bc21;
}

/* Email icon color */
.emergency-item .fa-envelope {
    color: #009ada;
}

/* Mobile icon color */
.emergency-item .fa-mobile-alt {
    color: #76bc21;
}

/* Location icon color */
.emergency-item .fa-map-marker-alt {
    color: #009ada;
}

/* Additional Info Section - Compact */
.contact-additional-info {
    border-top: 1px dashed #e0e7ed;
    padding-top: 8px;
    font-size: 0.8rem;
}

.info-note {
    display: flex;
    align-items: flex-start;
    gap: 6px;
    color: #5a6a7a;
    margin-bottom: 4px;
    line-height: 1.3;
}

.info-note:last-child {
    margin-bottom: 0;
}

.info-note i {
    color: #009ada;
    font-size: 0.8rem;
    margin-top: 2px;
    flex-shrink: 0;
}

.info-note span {
    word-break: break-word;
}

.bio-note {
    font-style: italic;
    background: rgba(118,188,33,0.05);
    padding: 4px 8px;
    border-radius: 6px;
    border-left: 2px solid #76bc21;
}

/* Animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Stagger animation for cards */
.contact-card:nth-child(1) { animation-delay: 0.1s; }
.contact-card:nth-child(2) { animation-delay: 0.15s; }
.contact-card:nth-child(3) { animation-delay: 0.2s; }
.contact-card:nth-child(4) { animation-delay: 0.25s; }
.contact-card:nth-child(5) { animation-delay: 0.3s; }
.contact-card:nth-child(6) { animation-delay: 0.35s; }
.contact-card:nth-child(7) { animation-delay: 0.4s; }
.contact-card:nth-child(8) { animation-delay: 0.45s; }

/* Responsive Design */
@media (max-width: 768px) {
    .key-contacts-section {
        padding: 2rem 0;
        border-radius: 20px;
        margin: 1.5rem 0;
    }
    
    .section-title h2 {
        font-size: 1.8rem;
    }
    
    .contact-card {
        flex: 0 0 260px;
        min-width: 260px;
        max-width: 260px;
    }
    
    .contact-card-inner {
        padding: 14px;
    }
    
    .contact-name {
        font-size: 1.1rem;
    }
    
    .contact-position {
        font-size: 0.8rem;
    }
    
    .contact-dept {
        font-size: 0.75rem;
    }
    
    .emergency-value {
        font-size: 0.8rem;
    }
    
    .contact-emergency-details {
        padding: 8px;
    }
}

@media (max-width: 576px) {
    .contact-card {
        flex: 0 0 240px;
        min-width: 240px;
        max-width: 240px;
    }
    
    .contact-card-inner {
        padding: 12px;
    }
    
    .contact-name {
        font-size: 1rem;
    }
    
    .contact-position {
        font-size: 0.75rem;
    }
    
    .contact-role-badge {
        font-size: 0.65rem;
        padding: 2px 8px;
    }
    
    .emergency-item i {
        width: 20px;
        height: 20px;
        font-size: 0.75rem;
    }
    
    .emergency-value {
        font-size: 0.75rem;
    }
    
    .info-note {
        font-size: 0.75rem;
    }
}

/* Optional: Add a subtle background pattern */
.key-contacts-section::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: radial-gradient(circle at 30% 50%, rgba(0,154,218,0.02) 0%, transparent 50%);
    pointer-events: none;
}

/* Emergency indicator for urgent contacts */
.contact-card[data-category="Emergency"] .contact-card-inner {
    border-left-color: #d32f2f;
}

.contact-card[data-category="Emergency"] .contact-role-badge {
    background: rgba(211,47,47,0.1);
    color: #d32f2f;
    border-color: rgba(211,47,47,0.2);
}
</style>

<script>
// Drag scrolling functionality
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainer = document.querySelector('.contacts-horizontal-scroll');
    if (scrollContainer) {
        let isDown = false;
        let startX;
        let scrollLeft;

        scrollContainer.addEventListener('mousedown', (e) => {
            isDown = true;
            scrollContainer.style.cursor = 'grabbing';
            startX = e.pageX - scrollContainer.offsetLeft;
            scrollLeft = scrollContainer.scrollLeft;
        });

        scrollContainer.addEventListener('mouseleave', () => {
            isDown = false;
            scrollContainer.style.cursor = 'grab';
        });

        scrollContainer.addEventListener('mouseup', () => {
            isDown = false;
            scrollContainer.style.cursor = 'grab';
        });

        scrollContainer.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - scrollContainer.offsetLeft;
            const walk = (x - startX) * 1.5;
            scrollContainer.scrollLeft = scrollLeft - walk;
        });
    }
});
</script>