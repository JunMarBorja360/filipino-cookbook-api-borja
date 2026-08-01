<?php
/**
 * Example database configuration for the Filipino Cookbook API.
 *
 * Copy this file to "config.php" (in the same folder) and fill in your
 * own local values. config.php is listed in .gitignore and must never
 * be committed, since it may contain real credentials.
 *
 *   cp api/config.example.php api/config.php
 */

return [
    'db_host' => 'localhost',
    'db_name' => 'filipino_cookbook_api',
    'db_user' => 'YOUR_DATABASE_USERNAME',
    'db_pass' => 'YOUR_DATABASE_PASSWORD',

    // Change this to your own secret token before deploying anywhere
    // other than your local machine.
    'api_token' => 'YOUR_API_TOKEN',
];
