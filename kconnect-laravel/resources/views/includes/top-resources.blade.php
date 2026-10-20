<section class="top-resources-section my-5">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2><i class="fas fa-link me-2"></i>Quick Links</h2>
            <div class="title-line"></div>
            <p class="text-muted mt-3">Quick access to essential forms and templates</p>
        </div>
        
        @php
        // $topResources: SELECT * FROM top_resources WHERE status = 1 ORDER BY category, sort_order ASC (HomeData::topResources)
        $resourcesByCategory = [];
        if (count($topResources) > 0) {
            foreach ($topResources as $resource) {
                $category = $resource['category'] ?: 'Other';
                if (!isset($resourcesByCategory[$category])) {
                    $resourcesByCategory[$category] = [];
                }
                $resourcesByCategory[$category][] = $resource;
            }
        }
        
        $totalResources = array_sum(array_map('count', $resourcesByCategory));
         @endphp
        
        @if (!empty($resourcesByCategory))
            @foreach ($resourcesByCategory as $category => $resources)
            <div class="resources-category mb-4" data-aos="fade-up">
               
                
                <div class="resources-grid">
                    @foreach ($resources as $resource)
                    <a href="{!! !empty($resource['url']) ? htmlspecialchars($resource['url']) : '#' !!}" 
                       class="resource-item" 
                       target="{!! $resource['target'] !!}"
                       title="{{ $resource['description'] ?: $resource['title'] }}"
                       style="--resource-color: {!! $resource['color'] !!}; --resource-bg: {!! $resource['bg_color'] !!};">
                        <i class="fas {!! $resource['icon'] !!}"></i>
                        <span>{{ $resource['title'] }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endforeach
            
            @if ($totalResources > 8)
            <div class="text-center mt-4" data-aos="fade-up">
                <button class="btn btn-outline-gradient" id="viewAllResources" data-bs-toggle="modal" data-bs-target="#allResourcesModal">
                    See additional {!! ($totalResources - 8) !!} files <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </div>
            @endif
            
        @else
        <div class="text-center py-5" data-aos="fade-up">
            <i class="fas fa-link fa-4x text-muted mb-3"></i>
            <h3 class="text-muted">No resources available</h3>
            <p class="text-muted">Check back soon for quick access links</p>
        </div>
        @endif
    </div>
</section>



<style>
    .top-resources-section{
        position: relative;
    padding: 3rem 0;
    background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%);
    border-radius: 30px;
    margin: 2rem 0;
    overflow: hidden;  
    }
</style>