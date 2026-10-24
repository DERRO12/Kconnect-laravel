@include('academy.partials.header')
@include('academy.partials.sidebar')
@verbatim

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Top Navigation -->
                <nav class="navbar navbar-expand navbar-light bg-white mb-4 rounded-bottom shadow-sm">
                    <div class="container-fluid">
                        <h5 class="mb-0">Manage Courses</h5>
                        <div class="navbar-nav ms-auto">
                            <a href="add_course" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Add New Course
                            </a>
                        </div>
                    </div>
                </nav>

                <?php echo ka_flash_html(); ?>

                <!-- Courses Table -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover datatable" id="coursesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Thumbnail</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Level</th>
                                        <th>Lessons</th>
                                        <th>Enrollments</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($courses as $course): ?>
                                    <tr>
                                        <td><?php echo $course['id']; ?></td>
                                        <td>
                                            <?php if ($course['thumbnail']): ?>
                                                <img src="../assets/uploads/courses/<?php echo $course['thumbnail']; ?>" 
                                                     alt="<?php echo $course['title']; ?>"
                                                     class="rounded" width="50" height="50">
                                            <?php else: ?>
                                                <div class="bg-light rounded p-2 text-center">
                                                    <i class="bi bi-image text-secondary"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo $course['title']; ?></strong><br>
                                            <small class="text-secondary"><?php echo $course['slug']; ?></small>
                                        </td>
                                        <td><?php echo $course['category'] ?: '-'; ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $course['level'] == 'beginner' ? 'success' : 
                                                    ($course['level'] == 'intermediate' ? 'warning' : 'danger');
                                            ?>">
                                                <?php echo ucfirst($course['level']); ?>
                                            </span>
                                        </td>
                                        <td class="text-center"><?php echo $course['lesson_count']; ?></td>
                                        <td class="text-center"><?php echo $course['enrollment_count']; ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $course['status'] == 'published' ? 'success' : 'secondary'; ?>">
                                                <?php echo ucfirst($course['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($course['created_at'])); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="edit_course?id=<?php echo $course['id']; ?>" 
                                                   class="btn btn-outline-primary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="manage_lessons?course_id=<?php echo $course['id']; ?>" 
                                                   class="btn btn-outline-info" title="Manage Lessons">
                                                    <i class="bi bi-list-check"></i>
                                                </a>
                                                <a href="manage_quiz?course_id=<?php echo $course['id']; ?>" 
                                                   class="btn btn-outline-warning" title="Manage Quiz">
                                                    <i class="bi bi-patch-question"></i>
                                                </a>
                                                <a href="javascript:void(0)" 
                                                   onclick="confirmDelete(<?php echo $course['id']; ?>)"                                                   class="btn btn-outline-danger" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this course? This action cannot be undone and will also delete all lessons, quizzes, and related data.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#coursesTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 25
            });
        });

        function confirmDelete(courseId) {
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            document.getElementById('confirmDeleteBtn').href = 
                `manage_courses?delete=${courseId}&token=<?php echo $csrf_token; ?>`;
            modal.show();
        }
    </script>
</body>
</html>
@endverbatim
