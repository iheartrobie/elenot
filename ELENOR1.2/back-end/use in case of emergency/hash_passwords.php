



<?php
// Include your database connection file
include_once '../../connections.php';

// Fetch all users
$query = "SELECT userid, password FROM login_tbl";
$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $userid = $row['userid'];
        $plain_password = $row['password'];

        // Hash the plain password
        $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

        // Update the password in the database
        $update_query = "UPDATE login_tbl SET password = ? WHERE userid = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $hashed_password, $userid);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "Password for user ID $userid updated successfully.<br>";
        } else {
            echo "Failed to update password for user ID $userid.<br>";
        }
    }
} else {
    echo "Error fetching users: " . mysqli_error($conn);
}
?>
