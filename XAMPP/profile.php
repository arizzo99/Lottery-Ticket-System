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
    <form action="profile.php" method="post">
        <?php
        if(isset($_SESSION['user_id'])){
            $user = $_SESSION['user_id'];
            require_once "database.php";
           
            $sql = "SELECT first_name,last_name,address,phone_number,email FROM users WHERE $user=id";
            $result = mysqli_query($conn_bool, $sql);
            $data = $result->fetch_all(MYSQLI_ASSOC);
        }
             ?><div class="form-group">
          

            <table class="table table-striped table-dark table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">First Name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col"> Address</th>
                          <th scope="col">Phone Number</th>
                        <th scope="col">Email</th>
                     </tr>
                </thead>
                
                <?php foreach($data as $row): ?>
                <tbody>
                <tr>
                       
                        <div class="form-group">
                            <td><?=  htmlspecialchars($row['first_name'])?></td>
                            <
                            </div>
                        <div class="form-group">
                            <td><?=  htmlspecialchars($row['last_name'])?></td>
                            
                        </div>
                        <div class="form-group">
                            <td><?=  htmlspecialchars($row['address'])?></td>
                            
                            <div class="form-group">
                            <td><?= htmlspecialchars($row['phone_number'])?></td>
                           
                        </div>
                        <div class="form-group">
                            <td><?=  htmlspecialchars($row['email'])?></td>
                            
                        </div>
                        
                      
                        </tr>
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



