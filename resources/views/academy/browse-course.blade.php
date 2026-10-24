@include('academy.partials.header')
@verbatim


<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Browse All Courses</h2>
        <?php if (!empty($type) && isset($type_labels[$type])): ?>
            <span class="badge px-3 py-2" style="background: <?php echo $type_labels[$type][1]; ?>; color: white;">
                <?php echo $type_labels[$type][0]; ?>
            </span>
        <?php endif; ?>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Learning Path</label>
                    <select name="type" class="form-select">
                        <option value="">All Paths</option>
                        <option value="foundation" <?php echo $type == 'foundation' ? 'selected' : ''; ?>>K-Start: Foundation (<?php echo $type_counts['foundation']; ?>)</option>
                        <option value="mastery" <?php echo $type == 'mastery' ? 'selected' : ''; ?>>K-Expert: Mastery (<?php echo $type_counts['mastery']; ?>)</option>
                        <option value="resource" <?php echo $type == 'resource' ? 'selected' : ''; ?>>K-Library: Resource (<?php echo $type_counts['resource']; ?>)</option>
                        <option value="compliance" <?php echo $type == 'compliance' ? 'selected' : ''; ?>>K-Compliance (<?php echo $type_counts['compliance']; ?>)</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['category']; ?>" <?php echo $category == $cat['category'] ? 'selected' : ''; ?>>
                                <?php echo $cat['category']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search courses...">
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Results -->
    <?php if (empty($courses)): ?>
        <div class="text-center py-5">
            <i class="bi bi-journal-x display-1 text-muted"></i>
            <h4 class="mt-3">No courses found</h4>
            <p class="text-secondary">Try adjusting your filters or search terms.</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($courses as $course): 
                $type_color = $type_labels[$course['category_type']][1] ?? '#009ada';
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="course-card h-100">
                    <div class="course-image">
                        <?php 
                        $thumbnail_path = 'assets/uploads/courses/' . ($course['thumbnail'] ?? '');
                        $thumbnail_exists = !empty($course['thumbnail']) && file_exists($thumbnail_path);
                        ?>
                        <?php if ($thumbnail_exists): ?>
                            <img src="<?php echo $thumbnail_path; ?>" alt="<?php echo htmlspecialchars($course['title']); ?>">
                        <?php else: ?>
                            <img src="https://placehold.co/600x400/<?php echo str_replace('#', '', $type_color); ?>/white?text=<?php echo urlencode($course['title']); ?>" 
                                 alt="<?php echo htmlspecialchars($course['title']); ?>">
                        <?php endif; ?>
                        <?php if (!empty($course['category_type'])): ?>
                            <span class="course-badge" style="background: <?php echo $type_color; ?>; color: white;">
                                <?php 
                                echo $course['category_type'] == 'foundation' ? 'Foundation' : 
                                    ($course['category_type'] == 'mastery' ? 'Mastery' : 
                                    ($course['category_type'] == 'resource' ? 'Resource' : 'Compliance')); 
                                ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="course-content">
                        <h5 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                        <p class="text-secondary small">
                            <?php echo htmlspecialchars(substr($course['description'] ?? '', 0, 80)); ?>...
                        </p>
                        
                        <div class="course-meta">
                            <span><i class="bi bi-book"></i> <?php echo $course['lesson_count'] ?? 0; ?> lessons</span>
                            <span><i class="bi bi-clock"></i> <?php echo $course['duration'] ?: 'Self-paced'; ?></span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="enrolled-badge">
                                <i class="bi bi-people"></i> <?php echo $course['enrolled_count'] ?? 0; ?> enrolled
                            </span>
                            <a href="course-overview?id=<?php echo $course['id']; ?>" class="btn btn-outline-primary btn-sm">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<style>
.course-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.03);
    transition: all 0.3s ease;
    border: 1px solid #e2e8f0;
    height: 100%;
}

.course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,154,218,0.1);
    border-color: #009ada;
}

.course-image {
    height: 160px;
    position: relative;
}

.course-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.course-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    padding: 0.25rem 1rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
}

.course-content {
    padding: 1.5rem;
}

.course-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.course-meta {
    display: flex;
    gap: 1rem;
    margin: 1rem 0;
    font-size: 0.9rem;
    color: #64748b;
}

.course-meta i {
    color: #009ada;
    margin-right: 0.3rem;
}

.enrolled-badge {
    background: #f0f9ff;
    color: #009ada;
    padding: 0.3rem 0.8rem;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 500;
}
</style>


@endverbatim
