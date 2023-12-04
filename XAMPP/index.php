<?php
session_start();

if(!isset($_SESSION["user"])){
    header("Location: login.php");
}

$secondsInADay = 24 * 60 * 60;
$initialCountdown = 7 * $secondsInADay;

if (!isset($_SESSION['countdown'])) {
    $_SESSION['countdown'] = $initialCountdown;
    $_SESSION['drawing'] = 'yes';
} else {
    $_SESSION['countdown'] -= 1;
}

$countdownInSeconds = $_SESSION['countdown'];
$days = floor($countdownInSeconds / $secondsInADay);
$hours = floor(($countdownInSeconds % $secondsInADay) / 3600);
$minutes = floor(($countdownInSeconds % 3600) / 60);
$seconds = $countdownInSeconds % 60;
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Texas Lottery Tickets</h1>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fs-4">
        <a href="#.php" class="navbar-brand fs-4">TLPS</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a href="index.php" class="nav-link">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a href="browse.php" class="nav-link">Browse </a>
                </li>
                <li class="nav-item">
                    <a href="ticket_search.php" class="nav-link">Ticket Search </a>
                </li>
                <li class="nav-item">
                    <a href="profile.php" class="nav-link">My Profile </a>
                </li>
                <li class="nav-item">
                    <a href="history.php" class="nav-link">History </a>
                </li>
                <li class="nav-item">
                    <a href="checkout.php" class="nav-link">Checkout </a>
                </li>
                <?php if(isset($_SESSION['admin'])):?>
                <li class="nav-item">
                    <a href="admin.php" class="nav-link">Status Report</a>
                </li>
                <?php endif ?>
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">Logout</a>
                </li>
                

                </li>
            </ul>
        </div>
    </nav>

    <div class="text-center fs-4">
        <h4>Next ticket drawing in: </h4>
    </div>
    
    <div id="counter" class="text-center fs-4">
        <h4>
            <?php echo "$days d $hours h $minutes m $seconds s"; ?>
        </h4>
    </div>

    <script>
        // Function to update the countdown
        function updateCountdown() {
            $.ajax({
                url: 'update_countdown.php', // Create a new PHP file to handle updates
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    // Display the updated countdown
                    document.getElementById("counter").innerHTML = response.countdown;
                }
            });
        }

        // Update the countdown initially
        updateCountdown();

        // Set interval to update the countdown every 1 second
        setInterval(updateCountdown, 1000);
    </script>
    <h5> 
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fs-1">
            <a href="#.php" class="navbar-brand fs-1">Welcome to our Lottery Purchase System</a>
        
            </nav>
        <h6> 
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fs-3">
            <a href="#.php" class="navbar-brand fs-3">Drawings are once a week, we offer PowerBall, Mega Millions, Texas Lotto, and Texas Two Step!</a>
        
            </nav>
            <h7> 
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fs-4">
            <a href="#.php" class="navbar-brand fs-4">Many chances to win, along with different claiming options! </a>
        
            </nav>
            <h8>

            <nav class="navbar navbar-expand-lg navbar-dark bg-dark fs-2">
            <a href="browse.php"" class="navbar-dark fs-2">PLAY NOW! </a>

</body>
</html>
