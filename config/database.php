<?php
return [
    'host' => getenv('DB_HOST') !== false ? getenv('DB_HOST') : 'localhost',
    'database' => getenv('DB_NAME') !== false ? getenv('DB_NAME') : 'studenthub',
    'user' => getenv('DB_USER') !== false ? getenv('DB_USER') : 'root',
    'password' => getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',
    'charset' => getenv('DB_CHARSET') !== false ? getenv('DB_CHARSET') : 'utf8mb4',
];
