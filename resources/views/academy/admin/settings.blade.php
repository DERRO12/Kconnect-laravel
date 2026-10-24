@include('academy.partials.header')
@include('academy.partials.sidebar')
@verbatim


<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">System Settings</h1>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <!-- Settings Tabs -->
    <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button">
                <i class="bi bi-gear"></i> General
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="email-tab" data-bs-toggle="tab" data-bs-target="#email" type="button">
                <i class="bi bi-envelope"></i> Email
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button">
                <i class="bi bi-shield-lock"></i> Security
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="backup-tab" data-bs-toggle="tab" data-bs-target="#backup" type="button">
                <i class="bi bi-database"></i> Backup
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="settingsTabContent">
        <!-- General Settings -->
        <div class="tab-pane fade show active" id="general" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="section" value="general">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="app_name" class="form-label">Application Name</label>
                                    <input type="text" class="form-control" id="app_name" name="app_name" 
                                           value="<?php echo 'K-Academy'; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="app_email" class="form-label">System Email</label>
                                    <input type="email" class="form-control" id="app_email" name="app_email" 
                                           value="<?php echo 'hr@kibokogroup.com'; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="app_url" class="form-label">Application URL</label>
                                    <input type="url" class="form-control" id="app_url" name="app_url" 
                                           value="<?php echo ka_app_url(); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="timezone" class="form-label">Timezone</label>
                                    <select class="form-select" id="timezone" name="timezone">
                                        <option value="UTC">UTC</option>
                                        <option value="America/New_York">Eastern Time</option>
                                        <option value="America/Chicago">Central Time</option>
                                        <option value="America/Denver">Mountain Time</option>
                                        <option value="America/Los_Angeles">Pacific Time</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save General Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Email Settings -->
        <div class="tab-pane fade" id="email" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="section" value="email">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="smtp_host" class="form-label">SMTP Host</label>
                                    <input type="text" class="form-control" id="smtp_host" name="smtp_host" 
                                           placeholder="smtp.gmail.com">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="smtp_port" class="form-label">SMTP Port</label>
                                    <input type="number" class="form-control" id="smtp_port" name="smtp_port" 
                                           value="587">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="smtp_user" class="form-label">SMTP Username</label>
                                    <input type="text" class="form-control" id="smtp_user" name="smtp_user">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="smtp_pass" class="form-label">SMTP Password</label>
                                    <input type="password" class="form-control" id="smtp_pass" name="smtp_pass">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="smtp_encryption" value="tls" checked>
                                        <label class="form-check-label" for="smtp_encryption">
                                            Use TLS Encryption
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Email Settings
                        </button>
                        <button type="button" class="btn btn-outline-secondary ms-2" onclick="testEmailSettings()">
                            <i class="bi bi-send"></i> Test Connection
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Security Settings -->
        <div class="tab-pane fade" id="security" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="section" value="security">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="session_timeout" class="form-label">Session Timeout (minutes)</label>
                                    <input type="number" class="form-control" id="session_timeout" name="session_timeout" 
                                           value="30" min="5" max="480">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_login_attempts" class="form-label">Max Login Attempts</label>
                                    <input type="number" class="form-control" id="max_login_attempts" name="max_login_attempts" 
                                           value="5" min="3" max="20">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="two_factor_auth" name="two_factor_auth">
                                        <label class="form-check-label" for="two_factor_auth">
                                            Enable Two-Factor Authentication
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="password_expiry" name="password_expiry" checked>
                                        <label class="form-check-label" for="password_expiry">
                                            Force Password Change Every 90 Days
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Security Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Backup Settings -->
        <div class="tab-pane fade" id="backup" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="mb-3">Database Backup</h6>
                    <p class="text-muted mb-4">Create a backup of your database and files.</p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-grid">
                                <button class="btn btn-primary" onclick="createBackup('database')">
                                    <i class="bi bi-database"></i> Backup Database Only
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-grid">
                                <button class="btn btn-success" onclick="createBackup('full')">
                                    <i class="bi bi-archive"></i> Full System Backup
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h6 class="mb-3">Scheduled Backups</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="backup_frequency" class="form-label">Frequency</label>
                                <select class="form-select" id="backup_frequency">
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="backup_time" class="form-label">Time</label>
                                <input type="time" class="form-control" id="backup_time" value="02:00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="backup_retention" class="form-label">Retention (days)</label>
                                <input type="number" class="form-control" id="backup_retention" value="30">
                            </div>
                        </div>
                    </div>
                    
                    <button class="btn btn-outline-primary">
                        <i class="bi bi-clock"></i> Schedule Backups
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function testEmailSettings() {
    Swal.fire({
        title: 'Testing Email Settings',
        text: 'Sending test email...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
            
            // Simulate test (replace with actual AJAX call)
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Test email sent successfully'
                });
            }, 2000);
        }
    });
}

function createBackup(type) {
    Swal.fire({
        title: 'Create Backup',
        text: `Create ${type} backup? This may take a few minutes.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, create backup'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Creating Backup',
                text: 'Please wait...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    
                    // Simulate backup (replace with actual AJAX call)
                    setTimeout(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Backup Created!',
                            text: 'Your backup has been created successfully'
                        });
                    }, 3000);
                }
            });
        }
    });
}
</script>


@endverbatim
