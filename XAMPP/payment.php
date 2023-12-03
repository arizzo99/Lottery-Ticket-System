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
    <?php
    echo "<p class='text-center'>Grand Total : $" . $_SESSION['total_cost'] . "</p>"; 
    ?>

    <div class="container">
        <?php
        if(isset($_POST['submit'])){
            $card_num = $_POST['cardnum'];
            $exp = $_POST['expdate'];
            $cvv = $_POST['cvv'];

            $errors = array();

            if(empty($card_num) OR empty($exp) OR  empty($cvv)){
                array_push($errors, "All fields are required.");
            }
            elseif(strlen($cvv) !== 3){
                array_push($errors, "CVV is invalid");
            }

            if(count($errors) > 0){
                foreach($errors as $error){
                    echo "<div class='alert alert-danger'>$error</div>";
                }
            }
            else{
                echo "<div class='alert alert-success'>Payment Successful</div>";
                echo "<div class='alert alert-info'>Redirecting to number selection...</div>";
                header("Location: ticket_selection.php");
            }
        }
        
        ?>
        <form action="payment.php" method="post">
            
            <div class="form-group">
                <input type="text" class="form-control" name="cardnum" placeholder="Card Number...">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="expdate" placeholder="Expiration Date...">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="cvv" placeholder="CVV...">
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value='Pay Amount' name="submit">
            </div>
        </form>
        <div>
            <p>
                We take payments through paypal!
                <a href="https://www.paypal.com/us/signin" target="_blank"> Pay Here </a>
            </p>
        </div>
    </div>
    
        
    
</body>
</html>