<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin View</title>
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
                    <a href="index.php" class="nav-link">Home </a>
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
                    <a href="admin.php" class="nav-link">Status Report<span class="sr-only">(current)</span></a>
                </li>
                <?php endif ?>
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">Logout</a>
                </li>
                </li>
            </ul>
        </div>
    </nav>
    
    <?php 
    require_once "database.php";
    $sql = "SELECT * FROM tickets WHERE user_nums IS NOT NULL";
    $result = mysqli_query($conn_bool, $sql);
    $alike_row_count = mysqli_num_rows($result);
    $data = $result->fetch_all(MYSQLI_ASSOC);

    $tickets_sold = $alike_row_count;
    $amount_sold = 0;
    foreach($data as $row){
        $amount_sold = $amount_sold + $row['price'];
    }
    ?>

    <table class="table table-striped table-dark table-hover">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Tickets Sold</th>
                <th scope="col">Amount Sold</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <div class="form-group">
                    <td><?= htmlspecialchars($tickets_sold)?></td>
                </div>
                <div class="form-group">
                    <td><?= '$' .htmlspecialchars($amount_sold)?></td>
                </div>
            </tr>
        </tbody>
    </table>
    
</body>
</html>