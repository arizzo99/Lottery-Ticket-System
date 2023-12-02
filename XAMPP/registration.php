<?php
session_start();
if(isset($_SESSION["user"])){
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
    <head>
        <meta charset = "UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registration</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <header>
            <h1>Registration</h1>
        </header>

        <div class="container">
            <?php
            # check if form is submitted
            if(isset($_POST["submit"])){
                $firstname = $_POST['firstname'];
                $lastname = $_POST['lastname'];
                $address = $_POST['address'];
                $phonenum = $_POST['phonenum'];
                $email = $_POST['email'];
                $password = $_POST['password'];
                $password_repeat = $_POST['confirm_password'];

                # password ecnryption
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                # data validation and error catching
                $errors = array();
                if(empty($firstname) OR empty($lastname) OR empty($address)OR empty($phonenum) OR empty($email) OR empty($password) OR empty($password_repeat)){
                    array_push($errors, "All fields are required");
                }
                if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                    array_push($errors, "Email is not valid");
                }
                if($password !== $password_repeat){
                    array_push($errors, "Passwords must match");
                }

                require_once "database.php";
                $sql = "SELECT * FROM users WHERE email = '$email'";
                $result = mysqli_query($conn_bool, $sql);
                $alike_row_count = mysqli_num_rows($result);
                
                if($alike_row_count > 0){
                    array_push($errors, "Account already exists with this email.");
                }
            
                # if errors is empty then submission can continue
                if(count($errors) > 0){
                    foreach($errors as $error){
                        echo "<div class='alert alert-danger'>$error</div>";
                    }
                }
                else{
                    # once we reach here all information is valid and can be pushed to the database
                    
                    $sql = "INSERT INTO users (first_name, last_name, address, phone_number, email, password) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = mysqli_stmt_init($conn_bool);
                    $prepare_stmt = mysqli_stmt_prepare($stmt, $sql);

                    if($prepare_stmt){
                        mysqli_stmt_bind_param($stmt, "ssssss", $firstname, $lastname, $address, $phonenum, $email, $password_hash);
                        mysqli_stmt_execute($stmt);
                        echo '<div class="alert alert-success">Registration Successful!</div>';
                    }
                    else{
                        die("Something went wrong.");
                    }
                
                }
            }
            ?>
            <form action="registration.php" method="post">
                <div class="form-group">
                    <input type="text" class="form-control" name="firstname" placeholder="First Name:">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="lastname" placeholder="Last Name:">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="address" placeholder="Address:">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="phonenum" placeholder="Phone Number:">
                </div>
                <div class="form-group">
                    <input type="email" class="form-control" name="email" placeholder="Email:">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="password" placeholder="Password:">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="confirm_password" placeholder="Repeat Password:">
                </div>
                <div class="form-btn">
                    <input type="submit" class="btn btn-primary" value="Register" name="submit">
                </div>
            </form>
            <div>
            <p>
                Already Registered?
                <a href="login.php">Login Here</a>
            </p>
        </div>
        </div>

        
    </body>
</html>
