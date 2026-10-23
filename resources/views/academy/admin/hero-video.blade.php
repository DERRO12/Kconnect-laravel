@php $page_title = $page_title ?? 'Manage Hero Video'; @endphp
@include('academy.partials.header')
@include('academy.partials.sidebar')
@verbatim




            
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Manage Hero Video</h1>
                </div>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php echo ka_flash_html(); ?>
                
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">YouTube Video URL</label>
                                <input type="url" name="video_url" class="form-control" 
                                       value="<?php echo htmlspecialchars($hero['video_url'] ?? ''); ?>" 
                                       placeholder="https://www.youtube.com/watch?v=..." required>
                                <small class="text-muted">Enter the full YouTube video URL</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Video Title</label>
                                <input type="text" name="video_title" class="form-control" 
                                       value="<?php echo htmlspecialchars($hero['video_title'] ?? ''); ?>" 
                                       placeholder="e.g., Welcome to Kiboko Academy">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Video Description</label>
                                <textarea name="video_description" class="form-control" rows="3"><?php echo htmlspecialchars($hero['video_description'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="status" class="form-check-input" id="status" 
                                           <?php echo (!isset($hero) || $hero['status']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="status">Active</label>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Changes
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Preview -->
                <?php if (!empty($hero['video_url'])): 
                    // Extract YouTube ID
                    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $hero['video_url'], $matches);
                    $youtube_id = $matches[1] ?? '';
                ?>
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Preview</h5>
                    </div>
                    <div class="card-body">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/<?php echo $youtube_id; ?>" 
                                    allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
</body>
</html>
@endverbatim
