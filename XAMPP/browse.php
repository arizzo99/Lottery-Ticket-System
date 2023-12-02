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
                    <a href="logout.php" class="nav-link">Logout</a>
                </li>
                </li>
            </ul>
        </div>
    </nav>
    
    <?php 

    $_SESSION['selected_tickets'] = isset($_SESSION['selected_tickets']) ? $_SESSION['selected_tickets'] : array();

    if(isset($_POST['submit'])){
        if(isset($_SESSION['selected_tickets']) and sizeof($_SESSION['selected_tickets']) > 0){
            $errors = array();
            # store info of the selected tickets
            $all_tickets = array();
            # remove all rows from selection table
            foreach($_SESSION['selected_tickets'] as $value){
                array_push($all_tickets, $value);
                $key = array_search($value, $_SESSION['selected_tickets']);
                unset($_SESSION['selected_tickets'][$key]);
            }
            # send ticket information to shopping_cart table where tickets will be put on 'holding'
            $user_id = $_SESSION['user_id'];
            require_once 'database.php';
           
            foreach($all_tickets as $ticket){
                $sql = "SELECT * FROM shopping_cart WHERE $ticket = 'ticket_id'";
                $result = mysqli_query($conn_bool, $sql);
                $alike_row_count = mysqli_num_rows($result);
                if($alike_row_count > 0){
                    array_push($errors, "Ticket " . $ticket ." already exists in someone's cart");
                }
            }

            if(count($errors) > 0){
                foreach($errors as $error){
                    echo "<div class='alert alert-danger'>$error</div>";
                }
            }
            else{
                # once we reach here all ticket information is valid and can be pushed to the database
                $sql = "INSERT INTO shopping_cart (ticket_id, id) VALUES (?, ?)";
                $stmt = mysqli_stmt_init($conn_bool);
                $prepare_stmt = mysqli_stmt_prepare($stmt, $sql);

                

                if($prepare_stmt){
                    foreach($all_tickets as $ticket){
                        mysqli_stmt_bind_param($stmt, "ss", $ticket, $user_id);
                        mysqli_stmt_execute($stmt);
                    }

                    $sql_del = "DELETE FROM tickets WHERE ticket_id = ?";
                    $stmt_del = mysqli_stmt_init($conn_bool);
                    $prepare_stmt_del = mysqli_stmt_prepare($stmt_del, $sql_del);

                    if($prepare_stmt_del){
                        foreach($all_tickets as $ticket){
                            mysqli_stmt_bind_param($stmt_del, "s", $ticket);
                            mysqli_stmt_execute($stmt_del);
                        }

                        echo "<div class='alert alert-info'>Your tickets have been added to your cart!</div>";
                    }
                    else{
                        die("Something went wrong.");
                    }

                    
                }
                else{
                    die("Something went wrong.");
                }
                
            }
        }
        else{
            echo "<div class='alert alert-danger'>There are no tickets in your selection!</div>";
        }
        
    }
    
    foreach($_POST as $key => $value){
        if(strpos($key, 'select_') === 0){
            $ticket_id = substr($key, strlen('select_'));
            if(sizeof($_SESSION['selected_tickets']) == 5){
                echo "<div class='alert alert-danger'>You can only select up to 5 tickets!</div>";
            }
            elseif(!in_array($ticket_id, $_SESSION['selected_tickets'])){
                array_push($_SESSION['selected_tickets'], $ticket_id);
            }

        }
        elseif(strpos($key, 'deselect_') === 0){
            $len = sizeof($_SESSION['selected_tickets']);
            $deselected_ticket = substr($key, strlen('deselect_'));
            $index = array_search($deselected_ticket, $_SESSION['selected_tickets']);

            if(sizeof($_SESSION['selected_tickets']) == 1){
                unset($_SESSION['selected_tickets']);
            }
            else{
                unset($_SESSION['selected_tickets'][$index]);
            }
            

        }
    }
    
    ?>
    
    <form action='browse.php' method='post'>
        
        <?php
        require_once 'database.php';
        $sql = "SELECT ticket_id, price, winning_amount FROM tickets";
        $result = mysqli_query($conn_bool, $sql);
        $data = $result->fetch_all(MYSQLI_ASSOC);
        ?>

        <!-- selected tickets table -->
        <table class="table table-striped table-dark table-hover">
            <thead class="thead-dark">
                <tr>
                    <th scope='col'>Selected Tickets</th>
                    <th scope='col'></th>
                </tr>
            </thead>
            <?php if (isset($_SESSION['selected_tickets'])): ?>
                <tbody>
                    <?php foreach($_SESSION['selected_tickets'] as $tick): ?>
                    <tr>
                        <div class="form-group">
                            <td><?= htmlspecialchars($tick)?></td>
                        </div>
                        <div class="form-btn">
                            <td><input type="submit" name='deselect_<?= $tick ?>' value='deselect'></td>
                        </div>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            <?php else: ?>
                <tbody>
                    <tr>
                        <td colspan='2'>No tickets selected.</td>
                    </tr>
                </tbody>
            <?php endif ?>
        </table>

        <input type="submit" name="submit" value="submit selections">
        <h6>Please browse from the tickets below, you are only allowed to submit 5 selections at a time.</h4>
        <!-- ticket browsing table -->
        <table class='table table-striped table-dark table-hover'>
            <thead class='thead-dark'>
                <tr>
                    <th scope='col'>Ticket Number</th>
                    <th scope='col'>Price</th>
                    <th scope='col'>Winning Amount</th>
                    <th scope='col'>Select Tickets</th>
                </tr>
            </thead>
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
                    <div class="form-btn">
                        <td><input type='submit' name='select_<?= $row['ticket_id']?>' value='select'></td>
                    </div>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </form>

</body>
</html>