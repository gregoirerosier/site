<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=300');

$lat = filter_input(INPUT_GET, 'lat', FILTER_VALIDATE_FLOAT);
$lng = filter_input(INPUT_GET, 'lng', FILTER_VALIDATE_FLOAT);
$radius = filter_input(INPUT_GET, 'radius', FILTER_VALIDATE_INT) ?: 25;

echo json_encode([
  'provider' => 'sample',
  'query' => ['lat' => $lat, 'lng' => $lng, 'radius_km' => $radius],
  'studios' => [
    [
      'id' => 'studio_101',
      'name' => 'Ink District Studio',
      'rating' => 4.9,
      'review_count' => 128,
      'distance_km' => 0.8,
      'open_now' => true,
      'address' => '123 Example Street',
      'phone' => '250-555-0101',
      'website' => 'https://example.com'
    ],
    [
      'id' => 'studio_102',
      'name' => 'Black Lotus Tattoo',
      'rating' => 4.8,
      'review_count' => 96,
      'distance_km' => 1.3,
      'open_now' => true,
      'address' => '456 Sample Avenue',
      'phone' => '250-555-0102',
      'website' => 'https://example.com'
    ]
  ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
