<?php
include 'connections.php';
session_start();

$signupEmailError = $signupPasswordError = $signupConfirmPasswordError = '';
$loginEmailError = $loginPasswordError = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $errors = array();

    if (empty($email)) {
        $errors['email'] = "!! Email is required !!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "!! Invalid email format !!";
    }

    if (empty($password)) {
        $errors['password'] = "!! Password is required !!";
    }
    if ($password != $confirm_password) {
        $errors['confirm_password'] = "!! Passwords do not match !!";
    }

    $target_file = 'photos/';
    if (isset($_FILES["profile_photo"]) && $_FILES["profile_photo"]["error"] == 0) {
        $target_dir = "photos/";
        $target_file = $target_dir . basename($_FILES["profile_photo"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["profile_photo"]["tmp_name"]);

        if ($check === false) {
            $uploadOk = 0;
        }

        if ($_FILES["profile_photo"]["size"] > 5000000) {
            $uploadOk = 0;
        }

        $allowed_types = array("jpg", "jpeg", "png", "gif");
        if (!in_array($imageFileType, $allowed_types)) {
            $uploadOk = 0;
        }

        if ($uploadOk == 1) {
            if (!move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
                $target_file = 'photos/default.png';
            }
        } else {
            $target_file = 'photos/default.png';
        }
    } else {
        $target_file = 'photos/default.png';
    }

    if (empty($errors)) {
        $stmt_check_email = $conn->prepare("SELECT email FROM login_tbl WHERE email = ?");
        $stmt_check_email->bind_param("s", $email);
        $stmt_check_email->execute();
        $stmt_check_email->store_result();

        if ($stmt_check_email->num_rows > 0) {
            $errors['email'] = "!! Email already exists !!";
        }
        $stmt_check_email->close();
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt_insert_user = $conn->prepare("INSERT INTO login_tbl (email, password, userpic, acc_type) VALUES (?, ?, ?, 2)");
        $stmt_insert_user->bind_param("sss", $email, $hashed_password, $target_file);

        if ($stmt_insert_user->execute()) {
            $_SESSION['user_id'] = $stmt_insert_user->insert_id;
            $_SESSION['email'] = $email;
            $_SESSION['userpic'] = $target_file;
            $_SESSION['acc_type'] = 2;
            echo "<script>
                alert('Registration successful!');
                window.location.href = 'user.php';
                </script>";
            exit;
        } else {
            echo "Error: " . $conn->error;
        }
        $stmt_insert_user->close();
    } else {
        $signupEmailError = $errors['email'] ?? '';
        $signupPasswordError = $errors['password'] ?? '';
        $signupConfirmPasswordError = $errors['confirm_password'] ?? '';
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $errors = array();
    if (empty($email)) {
        $loginEmailError = "!! Email is required !!";
        $errors['email'] = $loginEmailError;
    }
    if (empty($password)) {
        $loginPasswordError = "!! Password is required !!";
        $errors['password'] = $loginPasswordError;
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT userid, email, password, userpic, acc_type FROM login_tbl WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($userid, $db_email, $hashed_password, $userpic, $acc_type);

        if ($stmt->fetch()) {
            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $userid;
                $_SESSION['email'] = $email;
                $_SESSION['userpic'] = $userpic;
                $_SESSION['acc_type'] = $acc_type;

                if ($acc_type == 1) {
                    header("Location: admin.php");
                } else {
                    header("Location: user.php");
                }
                exit;
            } else {
                $loginPasswordError = "Incorrect password !";
            }
        } else {
            $loginEmailError = "Error : User not found";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles/login.css">
</head>
<body>
    <form action="" method="post" class="loginForm" id="loginForm">
        <center>
            <br><h1>-- LOGIN --</h1><br><br>
            <label for="email">EMAIL</label><br><br>
            <input type="text" name="email" autocomplete="off"><br>
            <span id="emailError" class="error"><?php echo htmlspecialchars($loginEmailError); ?></span><br><br>
            <label for="password">PASSWORD</label><br><br>
            <input type="password" name="password" style="-webkit-text-security: square;"><br>
            <span id="passwordError" class="error"><?php echo htmlspecialchars($loginPasswordError); ?></span><br><br><br><br>
            <input type="submit" name="login" value="ENTER &#8680;"><br><br><br>
            <button type="button" class="showForm" onclick="showSignUpForm()">Don't have an account? Sign Up!</button>
        </center>
    </form>

    <form action="login.php" method="post" enctype="multipart/form-data" id="signupForm" style="display: none;" class="signupForm">
        <center>
            <div class="flex-container">
                <div class="flex1">
                    <br><h1>-- CREATE ACCOUNT --</h1><br>
                    <label for="email">NEW EMAIL</label><br><br>
                    <input type="text" name="email" autocomplete="off"><br>
                    <span class="error"><?php echo htmlspecialchars($signupEmailError); ?></span><br><br>
                    <label for="password">PASSWORD</label><br><br>
                    <input type="password" name="password"><br>
                    <span class="error"><?php echo htmlspecialchars($signupPasswordError); ?></span><br><br>
                    <label for="confirm_password">CONFIRM PASSWORD</label><br><br>
                    <input type="password" name="confirm_password"><br>
                    <span class="error"><?php echo htmlspecialchars($signupConfirmPasswordError); ?></span><br><br>
                </div>
                <div class="flex2">
                    <div class="imagePreview" id="imagePreview">
                        <img id="previewImg" src="#" alt="Profile Photo Preview" />
                    </div><br><br>
                    <label for="profile_photo">Profile Photo</label><br>
                    <p>Only JPEG, GIF, PNG, and JPG are allowed.</p><br>
                    <input type="file" name="profile_photo" id="profilePhotoInput"><br><br>
                </div>
            </div>
            <input type="submit" name="signup" value="Register"><br><br>
            <button type="button" class="showForm" onclick="showLoginForm()">Already have an account? Log In!</button>
        </center>
    </form>

<script>
function showSignUpForm() {
    document.getElementById('signupForm').style.display = 'block';
    document.getElementById('signupForm').classList.remove('formExitAnim');
    document.getElementById('signupForm').classList.add('formAnim');
    document.getElementById('loginForm').style.display = 'none';
}

function showLoginForm() {
    document.getElementById('loginForm').style.display = 'block';
    document.getElementById('loginForm').classList.remove('formExitAnim');
    document.getElementById('loginForm').classList.add('formAnim');
    document.getElementById('signupForm').style.display = 'none';
}

document.getElementById('profilePhotoInput').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const previewImg = document.getElementById('previewImg');

    if (file) {
        const reader = new FileReader();

        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewImg.style.display = 'block';
        }

        reader.readAsDataURL(file);
    } else {
        previewImg.src = '';
        previewImg.style.display = 'none';
    }
});
</script>

</body>
</html>
