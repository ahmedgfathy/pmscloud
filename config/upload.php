<?php

return [
    'max_filesize' => env('UPLOAD_MAX_FILESIZE', '500M'),
    'post_max_size' => env('POST_MAX_SIZE', '500M'),
    'max_execution_time' => env('MAX_EXECUTION_TIME', 300),
];
