<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [
    'sourceUrl'             => env('LISTFILE_SOURCE_URL', 'https://github.com/wowdev/wow-listfile/raw/master/community-listfile.csv'),
    'sourceUserEmail'       => env('LISTFILE_SOURCE_USER_EMAIL', 'blizzard@schattenhain.de'),
    'sourceSeparator'       => env('LISTFILE_SOURCE_SEPARATOR', ';'),
    'importQueryBufferSize' => env('LISTFILE_IMPORT_QUERY_BUFFER_SIZE', 1000),
    'cachePath'             => env('LISTFILE_CACHE_PATH', 'listfile.csv'),
    'customIdStart'         => env('LISTFILE_CUSTOM_ID_START', 1000000000),
];
