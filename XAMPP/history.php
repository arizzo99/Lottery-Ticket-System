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
    <title>Ticket History</title>
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
                    <a href="history.php" class="nav-link">History <span class="sr-only">(current)</span> </a>
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
    <?php
    // if countdown timer is completed then do stuff
    if(isset($_SESSION['drawing'])){
        $sql = "SELECT * FROM tickets WHERE id IS NOT NULL";
        $result = mysqli_query($conn_bool, $sql);
        $data = $result->fetch_all(MYSQLI_ASSOC);

        foreach($data as $row){
            // calculate winnings
            $matched_numbers = 0;
            $winnings = 0;
            $user_nums = $row['user_nums'];
            $winning_nums = $row['winning_nums'];
            $user_id = $row['id'];

            $user_nums_array = explode('-', $user_nums);
            $winning_nums_array = explode('-', $winning_nums);

            for ($i = 0; $i < count($user_nums_array); $i++) {
                if ($user_nums_array[$i] == $winning_nums_array[$i]) {
                    $matching_numbers++;
                }
            }

            $prize_pool = $row['winning_amount'];

            if($matching_numbers == 5){
                $winnings = $prize_pool;
            }
            elseif($matching_numbers == 4){
                $winnings = $prize_pool * .2;
            }
            elseif($matching_numbers == 3){
                $winnings = $prize_pool * .05;
            }
            elseif($matching_numbers == 2){
                $winnings = $prize_pool * .01;
            }
            // insert into drawn_tickets
            $sql = "INSERT INTO drawn_tickets (ticket_id, id, user_nums, winning_numbers, winnings, ticket_type) VALUES (?, ?, ? ,? ,?, ?)";
            $stmt = mysqli_stmt_init($conn_bool);
            $prepare_stmt = mysqli_stmt_prepare($stmt, $sql);

            mysqli_stmt_bind_param($stmt, "ssssss", $ticket_id, $user_id, $user_nums, $winning_numbers, $winnings, $ticket_type);
            mysqli_stmt_execute($stmt);
            
            // delete from tickets
            require_once 'database.php';
            $sql_del = "DELETE FROM tickets WHERE ticket_id = ?";
            $stmt_del = mysqli_stmt_init($conn_bool);
            $prepare_stmt_del = mysqli_stmt_prepare($stmt_del, $sql_del);

            if($prepare_stmt_del){
                mysqli_stmt_bind_param($stmt_del, "s", $ticket_id);
                mysqli_stmt_execute($stmt_del);
            }
            else{
                die("Something went wrong.");
            }
        }
        unset($_SESSION['drawing']);
    }

    $user = $_SESSION['user_id'];

    require_once 'database.php';
    $sql = "SELECT * FROM tickets WHERE $user = id";
    $result = mysqli_query($conn_bool, $sql);
    $data = $result->fetch_all(MYSQLI_ASSOC);


    ?>

    <h4> Purchased Tickets </h4>
    <table class='table table-striped table-dark table-hover'>
        <thead class='thead-dark'>
            <tr>
                <th scope='col'>Ticket Number</th>
                <th scope='col'>Ticket Type</th>
                <th scope='col'>Price</th>
                <th scope='col'>Winning Amount</th>
                <th scope='col'>Your Selection</th>
            </tr>
        </thead>
        <?php foreach($data as $row): ?>

        <!-- Purchased Tickets table -->
        <tbody>
            <tr>
                <div class="form-group">
                    <td><?= htmlspecialchars($row['ticket_id'])?></td>
                    <input type="hidden" name="ticket_id" value=<?= htmlspecialchars($row['ticket_id'])?>>
                </div>
                <div class="form-group">
                    <td><?= htmlspecialchars($row['ticket_type'])?></td>
                    <input type="hidden" name="ticket_id" value=<?= htmlspecialchars($row['ticket_type'])?>>
                </div>
                <div class="form-group">
                    <td><?= '$' . htmlspecialchars($row['price'])?></td>
                    <input type="hidden" name="price" value=<?= htmlspecialchars($row['price'])?>>
                </div>
                <div class="form-group">
                    <td><?= '$' . htmlspecialchars($row['winning_amount'])?></td>
                    <input type="hidden" name="winning_amount" value=<?= htmlspecialchars($row['winning_amount'])?>>
                </div>
                <div class="form-btn">
                    <td><?= htmlspecialchars($row['user_nums'])?></td>
                    <input type="hidden" name="winning_amount" value=<?= htmlspecialchars($row['winning_amount'])?>>
                </div>
            </tr>
            <?php endforeach ?>
         </tbody>
    </table>


    <?php 
    $user = $_SESSION['user_id'];
    require_once 'database.php';
    $sql = "SELECT * FROM drawn_tickets WHERE $user = id";
    $result = mysqli_query($conn_bool, $sql);
    $data = $result->fetch_all(MYSQLI_ASSOC);

    foreach($_POST as $key => $value){
        if(strpos($key, 'claim_') === 0){
            $ticket_id = substr($key, strlen('claim_'));
            require_once "database.php";
            $sql = "SELECT * FROM drawn_tickets WHERE $ticket_id = ticket_id";
            $result = mysqli_query($conn_bool, $sql);
            $data2 = $result->fetch_all(MYSQLI_ASSOC);

            foreach($data2 as $row){
                $winnings = $row['winnings'];
            }

            if($winnings < 599){
                echo "<div class='alert alert-success'>You have successfully claimed your winnings!</div>";
                require_once 'database.php';
                $sql_del = "DELETE FROM drawn_tickets WHERE ticket_id = ?";
                $stmt_del = mysqli_stmt_init($conn_bool);
                $prepare_stmt_del = mysqli_stmt_prepare($stmt_del, $sql_del);

                if($prepare_stmt_del){
                    mysqli_stmt_bind_param($stmt_del, "s", $ticket_id);
                    mysqli_stmt_execute($stmt_del);
                }
                else{
                    die("Something went wrong.");
                }
            } 
            else{
                echo "<div class='alert alert-info'>Your winnings exceed $599, you must print your ticket for redemption.</div>";
                echo "<div class='alert alert-info'>This Receipt is for ticket ".$ticket_id."</div>";        
            }
        }
    }
    ?>


    <h4> Drawn Tickets </h4>
    <table class='table table-striped table-dark table-hover'>
        <thead class='thead-dark'>
            <tr>
                <th scope='col'>Ticket Number</th>
                <th scope='col'>Ticket Type</th>
                <th scope='col'>Your Selection</th>
                <th scope='col'>Winning Numbers</th>
                <th scope='col'>Winnings</th>
                <th scope='col'>Claim Winnings</th>
            </tr>
        </thead>
        <?php foreach($data as $row): ?>

        <!-- Drawn Tickets table -->
        <tbody>
            <tr>
                <div class="form-group">
                    <td><?= htmlspecialchars($row['ticket_id'])?></td>
                    <input type="hidden" name="ticket_id" value=<?= htmlspecialchars($row['ticket_id'])?>>
                </div>
                <div class="form-group">
                    <td><?= htmlspecialchars($row['ticket_type'])?></td>
                    <input type="hidden" name="ticket_type" value=<?= htmlspecialchars($row['ticket_type'])?>>
                </div>
                <div class="form-group">
                    <td><?= htmlspecialchars($row['user_nums'])?></td>
                    <input type="hidden" name="user_nums" value=<?= htmlspecialchars($row['user_nums'])?>>
                </div>
                <div class="form-group">
                    <td><?= htmlspecialchars($row['winning_numbers'])?></td>
                    <input type="hidden" name="winning_numbers" value=<?= htmlspecialchars($row['winning_numbers'])?>>
                </div>
                <div class="form-btn">
                    <td><?= '$' . htmlspecialchars($row['winnings'])?></td>
                    <input type="hidden" name="winnings" value=<?= htmlspecialchars($row['winnings'])?>>
                </div>
                <div class="form-btn">
                    <td><input type='submit' name='claim_<?= $row['ticket_id']?>' value='claim'></td>
                </div>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <div>
            <button onClick="window.print()">
                Click here if prompted to print for receipt
            </button>
        </div>
</body>
</html>
