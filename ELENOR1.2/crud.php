<?php

// Function to create a new user
function createUser($conn, $email, $password, $userpic, $acc_type) {
    // Sanitize inputs
    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);
    $userpic = mysqli_real_escape_string($conn, $userpic);
    $acc_type = mysqli_real_escape_string($conn, $acc_type);

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into the database
    $query = "INSERT INTO `login_tbl` (`email`, `password`, `userpic`, `acc_type`) VALUES ('$email', '$hashedPassword', '$userpic', '$acc_type')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        return true; // User created successfully
    } else {
        return false; // Failed to create user
    }
}

// Function to read user data
function readUser($conn, $userId) {
    // Sanitize input
    $userId = mysqli_real_escape_string($conn, $userId);

    // Fetch user data from the database
    $query = "SELECT * FROM `login_tbl` WHERE `userid`='$userId'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result); // Return user data
    } else {
        return null; // User not found
    }
}

// Function to update user data
function updateUser($conn, $userId, $newData) {
    // Sanitize input
    $userId = mysqli_real_escape_string($conn, $userId);

    // Extract new data
    $email = mysqli_real_escape_string($conn, $newData['email']);
    $password = mysqli_real_escape_string($conn, $newData['password']);
    $userpic = mysqli_real_escape_string($conn, $newData['userpic']);
    $acc_type = mysqli_real_escape_string($conn, $newData['acc_type']);

    // Hash the new password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Update user data in the database
    $query = "UPDATE `login_tbl` SET `email`='$email', `password`='$hashedPassword', `userpic`='$userpic', `acc_type`='$acc_type' WHERE `userid`='$userId'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        return true; // User updated successfully
    } else {
        return false; // Failed to update user
    }
}

// Function to delete a user
function deleteUser($conn, $userId) {
    // Sanitize input
    $userId = mysqli_real_escape_string($conn, $userId);

    // Delete user from the database
    $query = "DELETE FROM `login_tbl` WHERE `userid`='$userId'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        return true; // User deleted successfully
    } else {
        return false; // Failed to delete user
    }
}

?>
