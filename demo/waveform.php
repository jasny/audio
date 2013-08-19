<?php

use Jasny\Audio\Waveform;

require_once '../src/Jasny/Audio/Track.php';
require_once '../src/Jasny/Audio/Waveform.php';

$format = preg_match('~^application/json\b~', $_SERVER['HTTP_ACCEPT']) ? 'json' : 'png';
$track = isset($_GET['track']) ? $_GET['track'] : 'demo.wav';

$waveform = new Waveform("tracks/$track", $_GET);
$waveform->output($format);
