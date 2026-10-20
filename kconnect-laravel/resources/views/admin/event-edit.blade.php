<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - Kiboko Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
        }
        
        body {
            background: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            background: white;
            height: 100vh;
            position: fixed;
            width: 260px;
            padding: 20px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            overflow-y: auto;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 30px;
        }
        
        .page-header {
            background: white;
            border-radius: 15px;
            padding: 20px 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .form-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
        }
        
        .form-label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--kiboko-blue);
            box-shadow: 0 0 0 3px rgba(0,154,218,0.1);
        }
        
        .btn-gradient {
            background: var(--grad);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,154,218,0.2);
            color: white;
        }
        
        .btn-outline-secondary {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-outline-secondary:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    
    <div class="main-content">
        <div class="page-header">
            <div>
                <h4 class="fw-bold mb-1">Edit Event</h4>
                <p class="text-muted mb-0">Update event details</p>
            </div>
            <a href="news-events.php" class="btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
        
        @if(isset($error))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i> {!! $error !!}
        </div>
        @endif
        
        <div class="form-container">
            <form method="POST" action="">
@csrf
                <div class="mb-4">
                    <label class="form-label">Event Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="title" required value="{{ $event['title'] }}">
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Event Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="event_date" required value="{!! $event['event_date'] !!}">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Event Time (Optional)</label>
                        <input type="time" class="form-control" name="event_time" value="{!! !empty($event['event_time']) ? date('H:i', strtotime($event['event_time'])) : '' !!}">
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control" name="location" value="{{ $event['location'] }}">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-select" name="category" required>
                            <option value="">Select Category</option>
                            <option value="Get together" {!! $event['category'] == 'Get together' ? 'selected' : '' !!}>Get together</option>
                            <option value="Orientation" {!! $event['category'] == 'Orientation' ? 'selected' : '' !!}>Orientation</option>
                            <option value="Training" {!! $event['category'] == 'Training' ? 'selected' : '' !!}>Training</option>
                            <option value="Meeting" {!! $event['category'] == 'Meeting' ? 'selected' : '' !!}>Meeting</option>
                            <option value="Social" {!! $event['category'] == 'Social' ? 'selected' : '' !!}>Social</option>
                            <option value="Other" {!! $event['category'] == 'Other' ? 'selected' : '' !!}>Other</option>
                        </select>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="rsvp_required" id="rsvp_required" {!! $event['rsvp_required'] ? 'checked' : '' !!}>
                            <label class="form-check-label" for="rsvp_required">
                                RSVP Required
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">RSVP URL (Optional)</label>
                        <input type="url" class="form-control" name="rsvp_url" value="{{ $event['rsvp_url'] }}">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Description (Optional)</label>
                    <textarea class="form-control" name="description" rows="3">{{ $event['description'] }}</textarea>
                </div>
                
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="status" id="status" {!! $event['status'] ? 'checked' : '' !!}>
                        <label class="form-check-label" for="status">
                            Active (visible on homepage)
                        </label>
                    </div>
                </div>
                
                <div class="d-flex gap-3">
                    <button type="submit" class="btn-gradient">
                        <i class="fas fa-save"></i> Update Event
                    </button>
                    <a href="news-events.php" class="btn-outline-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>