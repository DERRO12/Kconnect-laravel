@include('academy.partials.header')
@include('academy.partials.sidebar')
@verbatim

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">My Profile</h1>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center">
                    <div class="profile-image mb-3">
                        <?php if ($user['profile_image']): ?>
                            <img src="assets/uploads/profiles/<?php echo $user['profile_image']; ?>" 
                                 alt="Profile" class="rounded-circle" width="150" height="150">
                        <?php else: ?>
                            <div class="avatar-circle bg-gradient text-white mx-auto" 
                                 style="width: 150px; height: 150px; font-size: 4rem;">
                                <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <h4><?php echo $user['full_name']; ?></h4>
                    <p class="text-muted"><?php echo $user['employee_code']; ?></p>
                    
                    <div class="mt-3">
                        <span class="badge bg-<?php echo $user['status'] === 'active' ? 'success' : 'warning'; ?>">
                            <?php echo ucfirst($user['status']); ?>
                        </span>
                        <span class="badge bg-info"><?php echo ucfirst($user['role']); ?></span>
                    </div>
                    
                    <div class="mt-3 text-start">
                        <p><i class="bi bi-envelope me-2"></i> <?php echo $user['email']; ?></p>
                        <p><i class="bi bi-calendar me-2"></i> Joined: <?php echo date('F d, Y', strtotime($user['created_at'])); ?></p>
                        <p><i class="bi bi-clock me-2"></i> Last Login: <?php echo $user['last_login'] ? ka_time_ago($user['last_login']) : 'Never'; ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Edit Profile</h6>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="full_name" 
                                   value="<?php echo e($user['full_name']); ?>" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" 
                                   value="<?php echo e($user['email']); ?>" readonly>
                            <div class="form-text">Your name and e-mail come from your K-Connect profile.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="profile_image" class="form-label">Profile Image</label>
                            <input type="file" class="form-control" id="profile_image" name="profile_image" 
                                   accept="image/*">
                            <div class="form-text">Max size: 2MB. Allowed: JPG, PNG, GIF</div>
                        </div>
                        
                        <hr>
                        
                        <h6 class="mb-3">Account Information</h6>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Employee Code</label>
                                    <input type="text" class="form-control" value="<?php echo $user['employee_code']; ?>" readonly disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Account Status</label>
                                    <input type="text" class="form-control" value="<?php echo ucfirst($user['status']); ?>" readonly disabled>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update Profile
                        </button>
                        
                        <a href="change-password" class="btn btn-outline-secondary">
                            <i class="bi bi-key"></i> Change Password
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>


@endverbatim
