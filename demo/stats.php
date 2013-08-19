<?php

use Jasny\Audio\Track;

require_once '../src/Jasny/Audio/Track.php';

$track = new Track($_GET['track']);
$stats = $track->getStats();

header('Content-Type: application/json');
echo json_encode($stats);
