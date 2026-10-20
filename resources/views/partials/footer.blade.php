@php
    // Footer content from the database: social_links, quick_links, top_resources (App\Support\HomeData::footer)
    $kcFooter = \App\Support\HomeData::footer();
@endphp
<footer class="footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <a class="navbar-brand" href="{{ url('/') }}">{{ SITE_NAME }}</a>
                    <p class="text-white-50 small">Your central hub for communication, collaboration, and celebration.</p>
                    @if (count($kcFooter['socialLinks']) > 0)
                    <div class="social-links">
                        @foreach ($kcFooter['socialLinks'] as $social)
                        <a href="{{ kc_url($social['url']) }}" class="text-white me-2" target="_blank" rel="noopener"
                           title="{{ $social['platform'] }}" aria-label="{{ $social['platform'] }}"><i class="{{ \App\Support\HomeData::iconClass($social['icon'], 'fab') }}"></i></a>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="col-md-2">
                    <h5>Quick Links</h5>
                    <div class="footer-links">
                        @forelse ($kcFooter['quickLinks'] as $link)
                        @php $linkUrl = kc_url($link['url']); @endphp
                        <a href="{{ $linkUrl }}"@if (preg_match('#^https?://#i', $linkUrl) && !str_starts_with($linkUrl, url('/'))) target="_blank" rel="noopener"@endif><i class="fas fa-chevron-right"></i> {{ $link['title'] }}</a>
                        @empty
                        <a href="https://jobs.kibokogroup.com/" target="_blank" rel="noopener"><i class="fas fa-chevron-right"></i> Careers</a>
                        <a href="{{ url('it-support') }}"><i class="fas fa-chevron-right"></i> Support</a>
                        @endforelse
                        @if ($kcFooter['itContact'])
                        @php $itNumber = $kcFooter['itContact']['extension'] ?: $kcFooter['itContact']['phone']; @endphp
                        <a href="{{ $kcFooter['itContact']['phone'] ? 'tel:'.preg_replace('/[^0-9+]/', '', $kcFooter['itContact']['phone']) : url('it-support') }}"><i class="fas fa-chevron-right"></i> Contact IT : {{ $itNumber }}</a>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <h5>Resources</h5>
                    <div class="footer-links">
                        @forelse ($kcFooter['resources'] as $resource)
                        <a href="{{ kc_url($resource['url']) }}"@if (($resource['target'] ?? '') === '_blank') target="_blank" rel="noopener"@endif><i class="fas fa-chevron-right"></i> {{ $resource['title'] }}</a>
                        @empty
                        <a href="{{ url('it-support') }}"><i class="fas fa-chevron-right"></i> IT Support</a>
                        @endforelse
                    </div>
                </div>
                <div class="col-md-3">
                    <a href="{{ url('admin/login') }}" class="btn-login ms-3">
                        <i class="fas fa-user me-2"></i>Admin Login
                    </a>
                </div>
            </div>
            <hr class="border-secondary mt-4">
            <div class="text-center text-white-50 small">
                &copy; {{ date('Y') }} KIBOKO GROUP
            </div>
        </div>
    </footer>
