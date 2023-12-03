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
    <title>Browse Tickets</title>
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
                    <a href="browse.php" class="nav-link">Browse <span class="sr-only">(current)</span></a>
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
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">Logout</a>
                </li>
                </li>
            </ul>
        </div>
    </nav>

     <form action="history.php" method="post">
        <?php
        if(isset($_SESSION['user_id'])){
            require_once "database.php";
            $user = $_SESSION['user_id'];
            $sql = "SELECT user_nums, price,winning_numbers,winning_amount,ticket_type FROM tickets WHERE $user=id";
             $result = mysqli_query($conn_bool, $sql);
             $data = $result->fetch_all(MYSQLI_ASSOC);
}
        ?>
   
        
           <?php foreach($_POST as $key => $value){
        # detecting if claim button was hit
        if(strpos($key, 'claim') === 0){
            # $key = winning_amount for row hit
           print_r($key);
        }
    }
    ?>
            <table class="table table-striped table-dark table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">User Number</th>
                        <th scope="col">Price</th>
                        <th scope="col">Winning Number</th>
                        <th scope="col"> Winning Amount</th>
                         <th scope="col"> Ticket Type</th>
                        </tr>
                </thead>
               <?php foreach($data as $row): ?>
                <tbody>
                <tr>
                           <div class="form-group">
                            <td><?=  htmlspecialchars($row['user_nums'])?></td>
                            
                            </div>
                        <div class="form-group">
                            <td><?=  htmlspecialchars($row['price'])?></td>
                            
                        </div>
                        <div class="form-group">
                            <td><?=  htmlspecialchars($row['winning_numbers'])?></td>
                            </div>

                            
                            <div class="form-group">
                            <td><?= htmlspecialchars($row['winning_amount'])?></td>
                            </div>
                            <td><?= htmlspecialchars($row['ticket_type'])?></td>
                            </div>
                            <div class="form-btn">
                        <td><input type='submit' name='claim_<?= $row['winning_amount']?>' value='claim'></td>
                    </div>
                           
                           
                     
                       
                    <?php endforeach ?>
                </tbody>
                
                <tbody>
                    <tr>
                        
                    </tr>
                </tbody>
            
                
            </table>
        </div>
    </form>

</body>
</html>