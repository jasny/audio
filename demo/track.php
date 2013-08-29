<?php

use Jasny\Audio\Track;

require_once '../src/Jasny/Audio/Track.php';

$track = new Track("tracks/" . (isset($_GET['track']) ? $_GET['track'] : 'demo.wav'));

$id3 = function_exists('id3_get_tag') ? id3_get_tag((string)$track) : array();
$result = $id3 + (array)$track->getAnnotations(true) + (array)$track->getStats();

header('Content-Type: application/json');
echo json_encode($result);
