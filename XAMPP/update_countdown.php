<?php
session_start();

$secondsInADay = 24 * 60 * 60;
$initialCountdown = 7 * $secondsInADay;

if (!isset($_SESSION['countdown'])) {
    $_SESSION['countdown'] = $initialCountdown;
} else {
    $_SESSION['countdown'] -= 1;
}

$countdownInSeconds = $_SESSION['countdown'];
$days = floor($countdownInSeconds / $secondsInADay);
$hours = floor(($countdownInSeconds % $secondsInADay) / 3600);
$minutes = floor(($countdownInSeconds % 3600) / 60);
$seconds = $countdownInSeconds % 60;

// Return updated countdown values as JSON
echo json_encode(['countdown' => "$days d $hours h $minutes m $seconds s"]);
?>