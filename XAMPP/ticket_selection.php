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
   
    <div class="container">
        <?php
        if(isset($_SESSION['tickets'])){
            $forms_to_submit = sizeof($_SESSION['tickets']);
            echo 'Picking for Ticket '.$_SESSION['tickets'][0].'.';
            if($forms_to_submit > 1){
                $current_ticket = $_SESSION['tickets'][0];
                # form submitted
                if(isset($_POST['submit'])){
                    $user_numbers = '';
                    $num1 = $_POST['num1'];
                    $num2 = $_POST['num2'];
                    $num3 = $_POST['num3'];
                    $num4 = $_POST['num4'];
                    $num5 = $_POST['num5'];

                    $user_numbers = $user_numbers.$num1;
                    $user_numbers = $user_numbers.'-'.$num2;
                    $user_numbers = $user_numbers.'-'.$num3;
                    $user_numbers = $user_numbers.'-'.$num4;
                    $user_numbers = $user_numbers.'-'.$num5;

                    # grab ticket information from shopping_cart
                    require_once "database.php";
                    $sql = "SELECT ticket_id, price, winning_amount, winning_numbers FROM shopping_cart WHERE $current_ticket = ticket_id";
                    $result = mysqli_query($conn_bool, $sql);
                    $data = $result->fetch_all(MYSQLI_ASSOC);

                    foreach($data as $value){
                        $price = $value['price'];
                        $winning_amount = $value['winning_amount'];
                        $winning_numbers = $value['winning_numbers'];
                    }
                    # push ticket information to tickets now setting id and user_numbers
                    require_once "database.php";
                    $sql = "INSERT INTO tickets (ticket_id, price, winning_amount, id, winning_numbers, user_nums) VALUES (?,?,?,?,?,?)";
                    $stmt = mysqli_stmt_init($conn_bool);
                    $prepare_stmt = mysqli_stmt_prepare($stmt, $sql);

                    if($prepare_stmt){
                        mysqli_stmt_bind_param($stmt, "iiiiss", $current_ticket, $price, $winning_amount, $_SESSION['user_id'], $winning_numbers,$user_numbers);
                        mysqli_stmt_execute($stmt);
                    }
                    else{
                        die("Something went wrong with ticket insertion.");
                    }
                    # delete ticket information from shopping cart
                    $sql = "DELETE FROM shopping_cart WHERE ticket_id = ?";
                    $stmt = mysqli_stmt_init($conn_bool);
                    $prepare_stmt = mysqli_stmt_prepare($stmt, $sql);

                    if($prepare_stmt){
                        mysqli_stmt_bind_param($stmt, "i", $current_ticket);
                        mysqli_stmt_execute($stmt);
                    }
                    # unset ticket_id from session and redirect back to ticket_selection
                    $key = array_search($current_ticket, $_SESSION['tickets']);

                    array_shift($_SESSION['tickets']);
                    $_SESSION['tickets'] = array_values($_SESSION['tickets']);
                    header("Location: ticket_selection.php");
                }
            }else{
                $current_ticket = $_SESSION['tickets'][0];
                # form submitted
                if(isset($_POST['submit'])){
                    $user_numbers = '';
                    $num1 = $_POST['num1'];
                    $num2 = $_POST['num2'];
                    $num3 = $_POST['num3'];
                    $num4 = $_POST['num4'];
                    $num5 = $_POST['num5'];

                    $user_numbers = $user_numbers.$num1;
                    $user_numbers = $user_numbers.'-'.$num2;
                    $user_numbers = $user_numbers.'-'.$num3;
                    $user_numbers = $user_numbers.'-'.$num4;
                    $user_numbers = $user_numbers.'-'.$num5;

                    # grab ticket information from shopping_cart
                    require_once "database.php";
                    $sql = "SELECT ticket_id, price, winning_amount, winning_numbers FROM shopping_cart WHERE $current_ticket = ticket_id";
                    $result = mysqli_query($conn_bool, $sql);
                    $data = $result->fetch_all(MYSQLI_ASSOC);

                    foreach($data as $value){
                        $price = $value['price'];
                        $winning_amount = $value['winning_amount'];
                        $winning_numbers = $value['winning_numbers'];
                    }
                    # push ticket information to tickets now setting id and user_numbers
                    require_once "database.php";
                    $sql = "INSERT INTO tickets (ticket_id, price, winning_amount, id, winning_numbers, user_nums) VALUES (?,?,?,?,?,?)";
                    $stmt = mysqli_stmt_init($conn_bool);
                    $prepare_stmt = mysqli_stmt_prepare($stmt, $sql);

                    if($prepare_stmt){
                        mysqli_stmt_bind_param($stmt, "iiiiss", $current_ticket, $price, $winning_amount, $_SESSION['user_id'], $winning_numbers,$user_numbers);
                        mysqli_stmt_execute($stmt);
                    }
                    else{
                        die("Something went wrong with ticket insertion.");
                    }
                    # delete ticket information from shopping cart
                    $sql = "DELETE FROM shopping_cart WHERE ticket_id = ?";
                    $stmt = mysqli_stmt_init($conn_bool);
                    $prepare_stmt = mysqli_stmt_prepare($stmt, $sql);

                    if($prepare_stmt){
                        mysqli_stmt_bind_param($stmt, "i", $current_ticket);
                        mysqli_stmt_execute($stmt);
                    }
                    # unset ticket_id from session and redirect back to ticket_selection
                    $key = array_search($current_ticket, $_SESSION['tickets']);
                    unset($_SESSION['tickets']);
                    
                    header("Location: browse.php");
                }
            }
        }
        else{
            echo "<div class='alert alert-danger'>Why am I here</div>";
        }
        ?>
        <p>Enter numbers for your ticket ranging from 1-50</p>
        <form action="ticket_selection.php" method="post">
            <div class="form-group">
                <input type="number" min="1" max="50" class="form-control" name="num1" placeholder="Number 1">
            </div>
            <div class="form-group">
                <input type="number" min="1" max="50" class="form-control" name="num2" placeholder="Number 2">
            </div>
            <div class="form-group">
                <input type="number" min="1" max="50" class="form-control" name="num3" placeholder="Number 3">
            </div>
            <div class="form-group">
                <input type="number" min="1" max="50" class="form-control" name="num4" placeholder="Number 4">
            </div>
            <div class="form-group">
                <input type="number" min="1" max="50" class="form-control" name="num5" placeholder="Number 5">
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Submit Number Selections" name="submit">
            </div>

        </form>
    </div>

    
    
</body>
</html>