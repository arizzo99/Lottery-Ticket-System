<?php
session_start();
if(!isset($_SESSION["user"])){
    header("Location: login.php");
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <h1>Texas Lottery Tickets</h1>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a href="#.php" class="navbar-brand">TLPS</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a href="index.php" class="nav-link">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a href="#.php" class="nav-link">Browse </a>
                </li>
                <li class="nav-item">
                    <a href="#.php" class="nav-link">Ticket Search </a>
                </li>
                <li class="nav-item">
                    <a href="#.php" class="nav-link">My Profile </a>
                </li>
                <li class="nav-item">
                    <a href="#.php" class="nav-link">History </a>
                </li>
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">Logout</a>
                </li>
                

                </li>
            </ul>
        </div>
    
    </nav>
    

</body>
</html>
