{{-- Shared <head> for the Satisfaction Surveys admin pages --}}
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root { --kiboko-green:#76bc21; --kiboko-blue:#009ada; --kiboko-dark:#0f172a; --grad:linear-gradient(135deg,#009ada 0%,#76bc21 100%); }
    * { box-sizing: border-box; }
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; color: #0f172a; }
    .main-content { margin-left: 280px; padding: 24px 32px; min-height: 100vh; }
    .page-head { display:flex; flex-wrap:wrap; gap:12px; align-items:center; justify-content:space-between; margin-bottom:20px; }
    .page-head h1 { font-size: 24px; font-weight: 800; margin: 0; }
    .page-head p { color:#64748b; margin:4px 0 0; font-size:14px; }
    .crumb { font-size:13px; color:#64748b; margin-bottom:6px; }
    .crumb a { color: var(--kiboko-blue); text-decoration:none; }
    .card-k { background:#fff; border:1px solid #eef2f6; border-radius:18px; padding:20px; margin-bottom:20px; }
    .card-k h2 { font-size:16px; font-weight:700; margin:0 0 12px; }
    .btn-k { background: var(--grad); color:#fff; border:0; font-weight:600; }
    .btn-k:hover { color:#fff; opacity:.92; }
    .stat { background:#fff; border:1px solid #eef2f6; border-radius:16px; padding:16px; text-align:center; }
    .stat .n { font-size:28px; font-weight:800; }
    .stat .l { font-size:12px; color:#64748b; font-weight:500; }
    .badge-st { font-size:11px; font-weight:700; padding:5px 10px; border-radius:20px; text-transform:uppercase; letter-spacing:.4px; }
    .st-draft { background:#f1f5f9; color:#475569; }
    .st-open { background:#dcfce7; color:#15803d; }
    .st-closed { background:#fee2e2; color:#b91c1c; }
    .link-box { display:flex; gap:6px; align-items:center; }
    .link-box input { font-size:12px; background:#f8fafc; }
    .form-label { font-weight:600; font-size:13px; }
    .muted { color:#64748b; font-size:13px; }
    @media (max-width: 992px) { .main-content { margin-left: 0; padding: 16px; } }
</style>
