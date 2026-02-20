<?php return array (
  'cache_enabled' => 1,
  'default_lifetime' => 3600,
  'default_compress' => 0,
  'cache_rules' => 
  array (
  ),
  'db_cache' => 
  array (
    'all' => 1,
    'tables' => 
    array (
      'carousel' => 0,
      'pages' => 0,
      'shop_products' => 0,
      'tenders' => 0,
      'visitor_logs' => 0,
      'gallery' => 0,
      'news' => 0,
    ),
  ),
  'static_cache' => 
  array (
    '/templates/default/js/js.js' => 1,
    '/templates/default/css/style.css' => 1,
    '/templates/default/css/style.php' => 1,
  ),
  'external_cache' => 
  array (
    'fonts' => 1,
    'icons' => 1,
  ),
  'redis_enabled' => 0,
  'redis_host' => '127.0.0.1',
  'redis_port' => 6379,
  'redis_password' => NULL,
);
