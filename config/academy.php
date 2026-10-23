<?php

/*
| K-Academy module settings (.env). The K-Academy database connection is "kacademy" in
| config/database.php (KA_DB_*).
*/

return [
    // Shown on certificate PDFs
    'certificate' => [
        'institution_name' => env('KA_CERT_INSTITUTION', 'Kiboko Group'),
        'institution_address' => env('KA_CERT_ADDRESS', ''),
        'institution_website' => env('KA_CERT_WEBSITE', 'www.kibokogroup.com'),
        'signatory_name' => env('KA_CERT_SIGNATORY_NAME', 'K-Academy'),
        'signatory_title' => env('KA_CERT_SIGNATORY_TITLE', 'Learning & Development'),
    ],

    // Largest lesson video an admin can upload, in MB (the server's upload_max_filesize and
    // post_max_size must allow it too)
    'max_video_mb' => (int) env('KA_MAX_VIDEO_MB', 500),
];
