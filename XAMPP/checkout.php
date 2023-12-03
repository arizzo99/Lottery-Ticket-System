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
    <title>Document</title>
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
                        <a href="checkout.php" class="nav-link">Checkout <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="logout.php" class="nav-link">Logout</a>
                    </li>
                    </li>
                </ul>
            </div>
    </nav>
    <?php 
    foreach($_POST as $key => $value){
        if(strpos($key, 'remove_') === 0){
            require_once "database.php";
            $ticket_id = substr($key, strlen('remove_'));
            $sql = "SELECT ticket_id, price, winning_amount, winning_numbers FROM shopping_cart WHERE $ticket_id = ticket_id";
            $result = mysqli_query($conn_bool, $sql);
            $data = $result->fetch_all(MYSQLI_ASSOC);
            

            foreach($data as $tick){
                $price = $tick['price'];
                $winning_amount = $tick['winning_amount'];
                $winning_numbers = $tick['winning_numbers'];
                
            }

            require_once "database.php";
            $sql = "INSERT INTO tickets (ticket_id, price, winning_amount, winning_numbers) VALUES (?,?,?,?)";
            $stmt = mysqli_stmt_init($conn_bool);
            $prepare_stmt = mysqli_stmt_prepare($stmt, $sql);
            # push ticket back into tickets table
            if($prepare_stmt){
                mysqli_stmt_bind_param($stmt, "iiis", $ticket_id, $price, $winning_amount, $winning_numbers);
                mysqli_stmt_execute($stmt);
                

            }
            else{
                die("Something went wrong.");
            }
            # remove ticket from shopping_cart

            $sql = "DELETE FROM shopping_cart WHERE ticket_id = ?";
            $stmt = mysqli_stmt_init($conn_bool);
            $prepare_stmt = mysqli_stmt_prepare($stmt, $sql);

            if($prepare_stmt){
                mysqli_stmt_bind_param($stmt, "i", $ticket_id);
                mysqli_stmt_execute($stmt);
            }

            echo '<div class="alert alert-success">Ticket Successfully Removed From Cart</div>';
        }
        if(isset($_POST['checkout'])){
            echo '<div class="alert alert-success">Checkout process begun</div>';
            $_SESSION['tickets'] = array();
            $user_id = $_SESSION['user_id'];

            require_once "database.php";
            $sql = "SELECT ticket_id, price, winning_amount FROM shopping_cart WHERE $user_id = id";
            $result = mysqli_query($conn_bool, $sql);
            $data = $result->fetch_all(MYSQLI_ASSOC);     
            
            foreach($data as $val){
                array_push($_SESSION['tickets'], $val['ticket_id']);
            }
            header("Location: payment.php");
        }
    }
    ?>

    <h4> Your cart </h4>
    <form action="checkout.php" method="post">
        <?php
        $user_id = $_SESSION['user_id'];
        require_once "database.php";
        $sql = "SELECT ticket_id, price, winning_amount FROM shopping_cart WHERE $user_id = id";
        $result = mysqli_query($conn_bool, $sql);
        $data = $result->fetch_all(MYSQLI_ASSOC);     
        $total_cost = 0;
        ?>
        <table class="table table-striped table-dark table-hover">
            <thead class="thead-dark">
                <tr>
                    <th scope='col'>Ticket Number</th>
                    <th scope='col'>Price</th>
                    <th scope='col'>Winning Amount</th>
                    <th scope='col'></th>
                </tr>
            </thead>
            <?php if (sizeof($data) > 0): ?>
                <tbody>
                    <?php foreach($data as $tick): ?>
                    <tr>
                        <div class="form-group">
                            <td><?= htmlspecialchars($tick['ticket_id'])?></td>
                        </div>
                        <div class="form-group">
                            <td><?= '$' . htmlspecialchars($tick['price'])?></td>
                            <?php $total_cost = $total_cost + $tick['price'];?>

                        </div>
                        <div class="form-group">
                            <td><?= '$' . htmlspecialchars($tick['winning_amount'])?></td>
                        </div>
                        <div class="form-btn">
                            <td><input type="submit" name='remove_<?= $tick['ticket_id'] ?>' value='remove'></td>
                        </div>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            <?php else: ?>
                <tbody>
                    <tr>
                        <td colspan='5'>No tickets in cart.</td>
                    </tr>
                </tbody>
            <?php endif ?>
            
        </table>
        <div class="row d-flex justify-content-center">
            <input class='text-center' type="submit" name="checkout" value="checkout">    
        </div>
    
    </form>
    <?php 
    echo "<p class='text-center'>Grand Total : $" . $total_cost . "</p>";
    $_SESSION['total_cost'] = $total_cost;
    ?>
    
    

    
    
</body>
</html>