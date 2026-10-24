@php $page_title = $page_title ?? 'Add Course'; @endphp
@include('academy.partials.header')
@include('academy.partials.sidebar')
@verbatim

<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs5.min.css" rel="stylesheet">
<style>
        :root {
            --kiboko-foundation: #4299e1;
            --kiboko-mastery: #48bb78;
            --kiboko-resource: #ed8936;
            --kiboko-compliance: #9f7aea;
        }
        
        .category-card {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            height: 100%;
            background: white;
            position: relative;
            overflow: hidden;
        }
        
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .category-card.selected {
            border-width: 3px;
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        
        .category-card.selected::after {
            content: '✓';
            position: absolute;
            top: 10px;
            right: 10px;
            width: 24px;
            height: 24px;
            background: var(--kiboko-primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
        }
        
        .category-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.8rem;
        }
        
        .category-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .category-description {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 0;
        }
        
        .foundation-bg {
            background: linear-gradient(135deg, #4299e110, #4299e130);
            color: var(--kiboko-foundation);
        }
        
        .mastery-bg {
            background: linear-gradient(135deg, #48bb7810, #48bb7830);
            color: var(--kiboko-mastery);
        }
        
        .resource-bg {
            background: linear-gradient(135deg, #ed893610, #ed893630);
            color: var(--kiboko-resource);
        }
        
        .compliance-bg {
            background: linear-gradient(135deg, #9f7aea10, #9f7aea30);
            color: var(--kiboko-compliance);
        }
        
        .selected-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 0.5rem;
        }
    </style>

            <!-- Sidebar -->
            
            

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <nav class="navbar navbar-expand navbar-light bg-white mb-4 rounded-bottom shadow-sm">
                    <div class="container-fluid">
                        <h5 class="mb-0">Add New Course</h5>
                        <a href="manage_courses" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Courses
                        </a>
                    </div>
                </nav>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <form method="POST" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <!-- Basic Information -->
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Course Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title" name="title" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Course Description</label>
                                        <textarea class="form-control summernote" id="description" name="description" rows="10"></textarea>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <!-- Category Selection -->
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title mb-3">Select Category <span class="text-danger">*</span></h6>
                                            <input type="hidden" name="category_type" id="category_type" value="">
                                            
                                            <div class="row g-3">
                                                <!-- Foundation -->
                                                <div class="col-12">
                                                    <div class="category-card" data-category="foundation" onclick="selectCategory('foundation')">
                                                        <div class="category-icon foundation-bg">
                                                            <i class="bi bi-flag"></i>
                                                        </div>
                                                        <h6 class="category-title">K-Start: The Foundation</h6>
                                                        <p class="category-description">Orientation, culture, and policies</p>
                                                    </div>
                                                </div>
                                                
                                                <!-- Mastery -->
                                                <div class="col-12">
                                                    <div class="category-card" data-category="mastery" onclick="selectCategory('mastery')">
                                                        <div class="category-icon mastery-bg">
                                                            <i class="bi bi-stars"></i>
                                                        </div>
                                                        <h6 class="category-title">K-Expert: The Mastery</h6>
                                                        <p class="category-description">Professional skills and development</p>
                                                    </div>
                                                </div>
                                                
                                                <!-- Resource -->
                                                <div class="col-12">
                                                    <div class="category-card" data-category="resource" onclick="selectCategory('resource')">
                                                        <div class="category-icon resource-bg">
                                                            <i class="bi bi-journal-richtext"></i>
                                                        </div>
                                                        <h6 class="category-title">K-Library: The Resource</h6>
                                                        <p class="category-description">Knowledge, SOPs, and guides</p>
                                                    </div>
                                                </div>
                                                
                                                <!-- Compliance -->
                                                <div class="col-12">
                                                    <div class="category-card" data-category="compliance" onclick="selectCategory('compliance')">
                                                        <div class="category-icon compliance-bg">
                                                            <i class="bi bi-shield-check"></i>
                                                        </div>
                                                        <h6 class="category-title">K-Compliance Center</h6>
                                                        <p class="category-description">Compliance and mandatory training</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Course Settings -->
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title">Course Settings</h6>
                                            
                                            <div class="mb-3">
                                                <label for="level" class="form-label">Level</label>
                                                <select class="form-select" id="level" name="level">
                                                    <option value="beginner">Beginner</option>
                                                    <option value="intermediate">Intermediate</option>
                                                    <option value="advanced">Advanced</option>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="duration" class="form-label">Duration</label>
                                                <input type="text" class="form-control" id="duration" name="duration" 
                                                       placeholder="e.g., 2 hours, 3 weeks, Self-paced">
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status</label>
                                                <select class="form-select" id="status" name="status">
                                                    <option value="draft">Draft</option>
                                                    <option value="published">Published</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Thumbnail Upload -->
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">Course Thumbnail</h6>
                                            
                                            <div class="mb-3">
                                                <label for="thumbnail" class="form-label">Upload Image</label>
                                                <input type="file" class="form-control" id="thumbnail" name="thumbnail" 
                                                       accept="image/jpeg,image/png,image/gif">
                                                <div class="form-text">Recommended size: 800x600px. Max size: 2MB</div>
                                            </div>
                                            
                                            <div id="thumbnailPreview" class="text-center d-none">
                                                <img src="#" alt="Preview" class="img-fluid rounded">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Create Course
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize Summernote
            $('.summernote').summernote({
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
            
            // Thumbnail preview
            $('#thumbnail').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#thumbnailPreview').removeClass('d-none');
                        $('#thumbnailPreview img').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
        
        function selectCategory(category) {
            // Update hidden input
            document.getElementById('category_type').value = category;
            
            // Remove selected class from all cards
            document.querySelectorAll('.category-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selected class to clicked card
            document.querySelector(`[data-category="${category}"]`).classList.add('selected');
            
            // Optional: Show a quick preview of selection
            const categoryNames = {
                'foundation': 'K-Start: The Foundation',
                'mastery': 'K-Expert: The Mastery',
                'resource': 'K-Library: The Resource',
                'compliance': 'K-Compliance Center'
            };
            
            // You could add a small toast or indicator here if needed
        }
    </script>
</body>
</html>
@endverbatim
