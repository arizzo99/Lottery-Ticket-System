<?php
session_start();
if(!isset($_SESSION["user"])){
    header("Location: login.php");
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search for Tickets</title>
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
                    <a href="ticket_search.php" class="nav-link">Ticket Search <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a href="profile.php" class="nav-link">My Profile </a>
                </li>
                <li class="nav-item">
                    <a href="history.php" class="nav-link">History </a>
                </li>
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">Logout</a>
                </li>
                </li>
            </ul>
        </div>
    </nav>
    
    <form action="ticket_search.php" method="post">
        <?php
        if(isset($_POST['search'])){
            # implement error throwing for invalid inputs
            $search_term = $_POST['search'];
            require_once "database.php";
            $sql = "SELECT * FROM tickets WHERE CAST(ticket_id AS CHAR) LIKE '%$search_term%'";
            $result = mysqli_query($conn_bool, $sql);
            $data = $result->fetch_all(MYSQLI_ASSOC);
        }
        ?>
        <div class="form-group">
            <input type="text" class="form-control" name="search" placeholder="Enter ticket number here...">

            <table class="table table-striped table-dark table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Ticket Number</th>
                        <th scope="col">Price</th>
                        <th scope="col">Winning Amount</th>
                    </tr>
                </thead>
                <?php if(isset($_POST['search'])): ?>
                <?php foreach($data as $row): ?>
                <tbody>
                        <tr>
                        <div class="form-group">
                            <td><?= htmlspecialchars($row['ticket_id'])?></td>
                            <input type="hidden" name="ticket_id" value=<?= htmlspecialchars($row['ticket_id'])?>>
                        </div>
                        <div class="form-group">
                            <td><?= '$' . htmlspecialchars($row['price'])?></td>
                            <input type="hidden" name="price" value=<?= htmlspecialchars($row['price'])?>>
                        </div>
                        <div class="form-group">
                            <td><?= '$' . htmlspecialchars($row['winning_amount'])?></td>
                            <input type="hidden" name="winning_amount" value=<?= htmlspecialchars($row['winning_amount'])?>>
                        </div>
                        </tr>
                    <?php endforeach ?>
                </tbody>
                <?php else: ?>
                <tbody>
                    <tr>
                        <td colspan='5'>No tickets selected.</td>
                    </tr>
                </tbody>
            <?php endif ?>
                
            </table>
        </div>
    </form>
</body>
</html>