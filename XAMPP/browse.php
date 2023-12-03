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
                foreach($all_tickets as $ticket){
                    $sql = "SELECT price, winning_amount, winning_numbers FROM tickets WHERE $ticket = ticket_id";
                    $result = mysqli_query($conn_bool, $sql);
                    $data = $result->fetch_all(MYSQLI_ASSOC);
                    foreach($data as $val){
                        $price = $val['price'];
                        $winning_amount = $val['winning_amount'];
                        $winning_numbers = $val['winning_numbers'];
                    }
                    
                    $sql = "INSERT INTO shopping_cart (ticket_id, id, price, winning_amount, winning_numbers) VALUES (?, ?, ? ,? ,?)";
                    $stmt = mysqli_stmt_init($conn_bool);
                    $prepare_stmt = mysqli_stmt_prepare($stmt, $sql);

                    mysqli_stmt_bind_param($stmt, "sssss", $ticket, $user_id, $price, $winning_amount, $winning_numbers);
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
        }
        else{
            echo "<div class='alert alert-danger'>There are no tickets in your selection!</div>";
        }
        
    }
    elseif(isset($_POST['random'])){
        $rand_tickets = array();
        $num_selected = 0;

        require_once 'database.php';
        $sql = "SELECT ticket_id, price, winning_amount FROM tickets WHERE id IS NULL";
        $result = mysqli_query($conn_bool, $sql);
        $data = $result->fetch_all(MYSQLI_ASSOC);

        if(isset($_SESSION['selected_tickets']) and sizeof($_SESSION['selected_tickets'])){
            # user has some tickets selected. determine how many they have selected to see how many we need to randomly select.
            $num_selected = sizeof($_SESSION['selected_tickets']);
        }
        else{
            # user has no tickets selected, select 5 random tickets
        }

        $rand_selection_count = 5 - $num_selected;

        
        $all_tickets = array();
        foreach($data as $ticket){
            array_push($all_tickets, $ticket['ticket_id']);
        }

        while(sizeof($rand_tickets) !== $rand_selection_count){
            $k = array_rand($all_tickets);
            $random_tick = $all_tickets[$k];

            if(!in_array($random_tick, $rand_tickets) and !in_array($random_tick, $_SESSION['selected_tickets'])){
                array_push($rand_tickets, $random_tick);
            }
        }

        foreach($rand_tickets as $tick){
            array_push($_SESSION['selected_tickets'], $tick);
        }


    }
    elseif(isset($_POST['delete'])){
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
            
            require_once 'database.php';
            $sql_del = "DELETE FROM tickets WHERE ticket_id = ?";
            $stmt_del = mysqli_stmt_init($conn_bool);
            $prepare_stmt_del = mysqli_stmt_prepare($stmt_del, $sql_del);

            if($prepare_stmt_del){
                foreach($all_tickets as $ticket){
                    mysqli_stmt_bind_param($stmt_del, "s", $ticket);
                    mysqli_stmt_execute($stmt_del);
                }
                echo "<div class='alert alert-info'>Ticket(s) have been deleted</div>";
            }
            else{
                die("Something went wrong.");
            }
        }
        else{
            echo "<div class='alert alert-danger'>There are no tickets in your selection!</div>";
        }
    }
    elseif(isset($_POST['add'])){
        $_SESSION['is_adding'] = "yes";
    }
    elseif(isset($_POST['add_selections'])){
        echo "<div class='alert alert-info'>Ticket Added</div>";
        unset($_SESSION['is_adding']);
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
        $sql = "SELECT ticket_id, price, winning_amount FROM tickets WHERE id IS NULL";
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
        <input type="submit" name="random" value="select random">
        <?php if(isset($_SESSION['admin'])): ?>
            <input type="submit" name="delete" value="delete selected tickets">
        <?php endif ?>
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
        <?php if(isset($_SESSION['admin'])):?>
            <input type="submit" name="add" value="add ticket">
        <?php endif ?>
        <?php if(isset($_SESSION['is_adding'])): ?>
            <div class="container">
                <div class="form-group">
                    <input type="text" class="form-control" name="price" placeholder="Enter Price Here">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="price" placeholder="Enter Prize Money Here">
                </div>
                <div class="form-group">
                    <input type="number" min="1" max="50" class="form-control" name="num1" placeholder="Winning Number 1">
                </div>
                <div class="form-group">
                    <input type="number" min="1" max="50" class="form-control" name="num2" placeholder="Winning Number 2">
                </div>
                <div class="form-group">
                    <input type="number" min="1" max="50" class="form-control" name="num3" placeholder="Winning Number 3">
                </div>
                <div class="form-group">
                    <input type="number" min="1" max="50" class="form-control" name="num4" placeholder="Winning Number 4">
                </div>
                <div class="form-group">
                    <input type="number" min="1" max="50" class="form-control" name="num5" placeholder="Winning Number 5">
                </div>
                <div class="form-btn">
                    <input type="submit" class="btn btn-primary" value="Add Entered Selections" name="add_selections">
                </div>
            </div>
            
        <?php endif?>
    </form>

</body>
</html>
