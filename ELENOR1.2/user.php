<?php
session_start();

if (!isset($_SESSION['email']) || !isset($_SESSION['userpic'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$userpic = $_SESSION['userpic'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="styles/user.css">
</head>
<body>
    <nav>
          <img src="<?php echo htmlspecialchars($userpic); ?>" alt="Profile Picture" width="200"><br><br><br>
          <?php echo htmlspecialchars($email); ?><br><br>
          <button>Nav1</button><br>
          <button>Nav2</button><br>
          <button>Nav3</button><br>
          <button>Nav4</button><br>
          <button>Nav5</button><br>
          <button>Nav6</button><br><br>
          <button onclick="logout()">Log Out</button>
    </nav>
    <div class="contents">
        <div class="nav1-content">
            
        </div>
        <div class="nav2-content">

        </div>
        <div class="nav3-content">

        </div>
        <div class="nav4-content">

        </div>
        <div class="nav5-content">

        </div>
        <div class="nav6-content">
            
        </div>
    </div>

<script>
function logout() {
    window.location.href = 'back-end/logout.php';
}
</script>

</body>
</html>
