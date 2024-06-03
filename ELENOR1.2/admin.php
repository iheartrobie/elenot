<?php
session_start();

include_once 'connections.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['userpic'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$userpic = $_SESSION['userpic'];

function fetchAllUsers($conn) {
    $query = "SELECT * FROM login_tbl";
    $result = mysqli_query($conn, $query);

    $users = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
    }
    return $users;
}

$users = fetchAllUsers($conn);

function changePassword($conn, $email, $oldPassword, $newPassword) {
    $email = mysqli_real_escape_string($conn, $email);
    $oldPassword = mysqli_real_escape_string($conn, $oldPassword);
    $newPassword = mysqli_real_escape_string($conn, $newPassword);

    $query = "SELECT `password` FROM `login_tbl` WHERE `email`='$email'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $hashedPassword = $row['password'];

        if (password_verify($oldPassword, $hashedPassword)) {
            $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $updateQuery = "UPDATE `login_tbl` SET `password`='$hashedNewPassword' WHERE `email`='$email'";
            $updateResult = mysqli_query($conn, $updateQuery);

            if ($updateResult) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function updateProfilePicture($conn, $email, $newImagePath) {
    $email = mysqli_real_escape_string($conn, $email);
    $newImagePath = mysqli_real_escape_string($conn, $newImagePath);

    $updateQuery = "UPDATE `login_tbl` SET `userpic`='$newImagePath' WHERE `email`='$email'";
    $updateResult = mysqli_query($conn, $updateQuery);

    if ($updateResult) {
        return true; 
    } else {
        return false; 
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['old_password']) && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword === $confirmPassword) {
        $passwordChanged = changePassword($conn, $email, $oldPassword, $newPassword);
        if ($passwordChanged) {
            echo "<script>
                alert('Password changed successfully. You will be logged out.');
                window.location.href = 'back-end/logout.php';
            </script>";
            exit();
        } else {
            echo "<script>alert('Failed to change password.');</script>";
        }
    } else {
        echo "<script>alert('New password and confirm password do not match.');</script>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['profile_photo'])) {
    $targetDirectory = "photos/";
    $targetFile = $targetDirectory . basename($_FILES["profile_photo"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["profile_photo"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "<script>alert('File is not an image.');</script>";
        $uploadOk = 0;
    }

    if ($_FILES["profile_photo"]["size"] > 5000000) {
        echo "<script>alert('Sorry, your file is too large.');</script>";
        $uploadOk = 0;
    }

    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif") {
        echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');</script>";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "<script>alert('Sorry, your file was not uploaded.');</script>";
    } else {
        if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $targetFile)) {
            $profilePictureUpdated = updateProfilePicture($conn, $email, $targetFile);
            if ($profilePictureUpdated) {
                echo "<script>alert('Profile picture updated successfully.');</script>";
                header("Refresh:0");
            } else {
                echo "<script>alert('Failed to update profile picture.');</script>";
            }
        } else {
            echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['edit_user'])) {
        // Edit user functionality remains unchanged
        $userid = intval($_POST['userid']);
        $email = $_POST['email'];
        $acc_type = intval($_POST['acc_type']);
        
        $updateQuery = "UPDATE login_tbl SET email = '$email', acc_type = $acc_type WHERE userid = $userid";
        mysqli_query($conn, $updateQuery);
    } elseif (isset($_POST['delete_user'])) {
        // Delete user functionality remains unchanged
        $userid = intval($_POST['userid']);
        $deleteQuery = "DELETE FROM login_tbl WHERE userid = $userid";
        mysqli_query($conn, $deleteQuery);
    } elseif (isset($_POST['add_user'])) {
        $newEmail = $_POST['add_email'];
        $newPassword = $_POST['add_password'];
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                
        $insertQuery = "INSERT INTO login_tbl (email, password, acc_type) VALUES ('$newEmail', '$hashedPassword', 2)";
        mysqli_query($conn, $insertQuery);

        // Handle file upload for profile picture
        if ($_FILES['add_profile_pic']['error'] === UPLOAD_ERR_OK) {
            $targetDirectory = "photos/";
            $targetFile = $targetDirectory . basename($_FILES["add_profile_pic"]["name"]);

            if (move_uploaded_file($_FILES["add_profile_pic"]["tmp_name"], $targetFile)) {
                updateProfilePicture($conn, $newEmail, $targetFile);
                
                header("Location: admin.php");
                exit();
            } else {
                echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
            }
        } else {

            echo "<script>alert('Failed to add user.');</script>";
        }
    }
}

// Fetch all facilities
function fetchAllFacilities($conn) {
    $query = "SELECT * FROM facilities";
    $result = mysqli_query($conn, $query);

    $facilities = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $facilities[] = $row;
        }
    }
    return $facilities;
}

// Fetch all meeting rooms
function fetchAllMeetingRooms($conn) {
    $query = "SELECT * FROM meeting_room";
    $result = mysqli_query($conn, $query);

    $meetingRooms = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $meetingRooms[] = $row;
        }
    }
    return $meetingRooms;
}

// Fetch all reservations
function fetchAllReservations($conn) {
    $query = "SELECT * FROM reservations";
    $result = mysqli_query($conn, $query);

    $reservations = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $reservations[] = $row;
        }
    }
    return $reservations;
}

$facilities = fetchAllFacilities($conn);
$meetingRooms = fetchAllMeetingRooms($conn);
$reservations = fetchAllReservations($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_facility'])) {
        $facilityName = $_POST['facility_name'];
        $description = $_POST['description'];
        
        $insertQuery = "INSERT INTO facilities (facility_name, description) VALUES ('$facilityName', '$description')";
        mysqli_query($conn, $insertQuery);
    }elseif (isset($_POST['edit_facility'])) {
        $facilityId = intval($_POST['facility_id']);
        $facilityName = $_POST['facility_name'];
        $description = $_POST['description'];
        
        $updateQuery = "UPDATE facilities SET facility_name = '$facilityName', description = '$description' WHERE facility_id = $facilityId";
        mysqli_query($conn, $updateQuery);
    }elseif (isset($_POST['delete_facility'])) {
        $facilityId = intval($_POST['facility_id']);
        $deleteQuery = "DELETE FROM facilities WHERE facility_id = $facilityId";
        mysqli_query($conn, $deleteQuery);
    }
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/admin.css">
    <title>Admin Panel</title>
</head>
<body>
    <nav class="navbar">
        <button id="settingsButton"><img src="styles/imgs/setting.png" alt=""></button>
        <center><h1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;FACILITY MANAGEMENT SYSTEM - ADMIN PANEL</h1></center>
    </nav>
<div id="mainContent">



    <nav class="menu">
        <a href="#" class="adminPic"><img src="<?php echo htmlspecialchars($userpic); ?>" alt="">
            <p><?php echo htmlspecialchars($email); ?></p>
        </a>
        <a href="#" data-content="users">
            <img src="styles/imgs/users.png" alt="users-icon"><br>
            Users
        </a>
        <a href="#" data-content="facilities">
            <img src="styles/imgs/facility.png" alt="users-icon"><br>
            Facilities
        </a>
        <a href="#" data-content="meeting">
            <img src="styles/imgs/meeting.png" alt="users-icon"><br>
            Meeting Room
        </a>
        <a href="#" data-content="reserve">
            <img src="styles/imgs/reserve.png" alt="users-icon"><br>
            Reservations
        </a>
        <a href="#" data-content="activity">
            <img src="styles/imgs/log.png" alt="users-icon"><br>
            Activity Log
        </a>
        <a href="#" data-content="feedbacks">
            <img src="styles/imgs/feedback.png" alt="users-icon"><br>
            User Feedbacks
        </a>
        <a href="#" data-content="reports">
            <img src="styles/imgs/report.png" alt="users-icon"><br>
            User Reports
        </a>
    </nav>

</div>

<button id="backButton" style="display : none;">&#8678; Back</button><!--BACK BUTTON-->

<div id="contentContainer">

<div class="users-content content" style="display : none;">
            <div class="usersTable">
                <span class="usersTableHead">
                    <h1>USERS</h1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <button id="addUserButton">Add User</button><br></span>
                <table>
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Email</th>
                            <th>Passwordrd</th>
                            <th>User Picture</th>
                            <th>Account Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['userid']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['password']); ?></td>
                            <td><img src="<?php echo htmlspecialchars($user['userpic']); ?>" alt="User Picture" width="50" height="50"></td>
                            <td><?php echo htmlspecialchars($user['acc_type']); ?></td>
                            <td>
                                <button class="editUserModal" data-userid="<?php echo $user['userid']; ?>" data-email="<?php echo $user['email']; ?>" data-acc_type="<?php echo $user['acc_type']; ?>">Edit</button><br><br>
                                <button class="deleteUserModal" data-userid="<?php echo $user['userid']; ?>">Delete</button>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="facilities-content content" style="display: none;">
    <div class="facilitiesTable">
        <span class="facilitiesTableHead">
            <h1>FACILITIES</h1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <button id="addFacilityButton">Add Facility</button><br></span>
        <table>
            <thead>
                <tr>
                    <th>Facility ID</th>
                    <th>Facility Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($facilities as $facility) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($facility['facility_id']); ?></td>
                    <td><?php echo htmlspecialchars($facility['facility_name']); ?></td>
                    <td><?php echo htmlspecialchars($facility['description']); ?></td>
                    <td>
                        <button class="editFacilityModal" data-facility_id="<?php echo $facility['facility_id']; ?>" data-facility_name="<?php echo $facility['facility_name']; ?>" data-description="<?php echo $facility['description']; ?>">Edit</button><br><br>
                        <button class="deleteFacilityModal" data-facility_id="<?php echo $facility['facility_id']; ?>">Delete</button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

    <div class="meeting-content content" style="display: none;">
    <div class="meetingRoomsTable">
        <span class="meetingRoomsTableHead">
            <h1>MEETING ROOMS</h1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <button id="addMeetingRoomButton">Add Meeting Room</button><br></span>
        <table>
            <thead>
                <tr>
                    <th>Room ID</th>
                    <th>Facility ID</th>
                    <th>Room Name</th>
                    <th>Capacity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($meetingRooms as $meetingRoom) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($meetingRoom['room_id']); ?></td>
                    <td><?php echo htmlspecialchars($meetingRoom['facility_id']); ?></td>
                    <td><?php echo htmlspecialchars($meetingRoom['room_name']); ?></td>
                    <td><?php echo htmlspecialchars($meetingRoom['capacity']); ?></td>
                    <td>
                        <button class="editMeetingRoomModal" data-room_id="<?php echo $meetingRoom['room_id']; ?>" data-facility_id="<?php echo $meetingRoom['facility_id']; ?>" data-room_name="<?php echo $meetingRoom['room_name']; ?>" data-capacity="<?php echo $meetingRoom['capacity']; ?>">Edit</button><br><br>
                        <button class="deleteMeetingRoomModal" data-room_id="<?php echo $meetingRoom['room_id']; ?>">Delete</button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>


<div class="reserve-content content" style="display: none;">
    <div class="reservationsTable">
        <span class="reservationsTableHead">
            <h1>RESERVATIONS</h1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <button id="addReservationButton">Add Reservation</button><br></span>
        <table>
            <thead>
                <tr>
                    <th>Reservation ID</th>
                    <th>Facility ID</th>
                    <th>User ID</th>
                    <th>Reservation Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $reservation) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($reservation['reservation_id']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['facility_id']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['reservation_date']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['start_time']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['end_time']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['status']); ?></td>
                    <td>
                        <button class="editReservationModal" data-reservation_id="<?php echo $reservation['reservation_id']; ?>" data-facility_id="<?php echo $reservation['facility_id']; ?>" data-user_id="<?php echo $reservation['user_id']; ?>" data-reservation_date="<?php echo $reservation['reservation_date']; ?>" data-start_time="<?php echo $reservation['start_time']; ?>" data-end_time="<?php echo $reservation['end_time']; ?>" data-status="<?php echo $reservation['status']; ?>">Edit</button><br><br>
                        <button class="deleteReservationModal" data-reservation_id="<?php echo $reservation['reservation_id']; ?>">Delete</button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

    <div class="activity-content content" style="display: none;">
        activity content
    </div>

    <div class="feedbacks-content content" style="display: none;">
        feedbacks content
    </div>

    <div class="reports-content content" style="display: none;">
        reports content
    </div>
</div>
<center>
<!-- Edit User Modal -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <span id="closeEditModal" class="close">&times;</span>
        <h2>EDIT USER</h2><br>
        <form id="editUserForm" method="post" action="admin.php">
            <input type="hidden" id="editUserId" name="userid">
            <label for="editUserEmail">Email:</label><br><br>
            <input type="text" id="editUserEmail" name="email" required><br><br>
            <label for="editUserAccType">Account Type:</label><br><br>
            <input type="number" id="editUserAccType" name="acc_type" required min="1" max="2"><br><br>
            <input type="submit" name="edit_user" value="Save Changes" class="savechangeBtn">
        </form>
    </div>
</div>

<!-- Delete User Modal -->

<div id="deleteUserModal" class="modal">
    <div class="modal-content">
        <span id="closeDeleteModal" class="close">&times;</span>
        <h2>DELETE USER</h2><br><br>
        <p>Are you sure you want to delete this user?</p><br><br>
        <form id="deleteUserForm" method="post" action="admin.php">
            <input type="hidden" id="deleteUserId" name="userid">
            <input type="submit" name="delete_user" value="Yes" class="delUserBtn">
            <button type="button" id="cancelDelete" class="closedelUserBtn">No</button>
        </form>
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="modal addModal">
    <div class="modal-content">
    <span id="closeAddModal" class="close">&times;</span>
    <h2>ADD USER</h2><br>
    <form id="addUserForm" method="post" action="admin.php" enctype="multipart/form-data">
    <label for="addUserEmail">Email:</label><br><br>
    <input type="text" id="addUserEmail" name="add_email" required><br><br>
    <label for="addUserPassword">Password:</label><br><br>
    <input type="password" id="addUserPassword" name="add_password" required><br><br>
    <label for="addUserProfilePic">Profile Picture:</label><br><br>
    <input type="file" id="addUserProfilePic" name="add_profile_pic" accept="image/*"><br><br>
    <input type="submit" name="add_user" value="Add User" class="addUserBtn">
</form>

    </div>
</div>

    <!-- Add Facility Modal -->
    <div id="addFacilityModal" class="modal">
        <div class="modal-content">
            <span id="closeAddFacilityModal" class="close">&times;</span>
            <h2>Add Facility</h2><br>
            <form id="addFacilityForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="facility_name">Facility Name:</label><br><br>
                <input type="text" id="facility_name" name="facility_name" required><br><br>
                <label for="description">Description:</label><br><br>
                <textarea id="description" name="description" rows="4" required></textarea><br><br>
                <input type="submit" name="add_facility" value="Add Facility" class="addFacilityBtn">
            </form>
        </div>
    </div>

    <!-- Edit Facility Modal -->
    <div id="editFacilityModal" class="modal">
        <div class="modal-content">
            <span id="closeEditFacilityModal" class="close">&times;</span>
            <h2>Edit Facility</h2><br>
            <form id="editFacilityForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" id="edit_facility_id" name="facility_id">
                <label for="edit_facility_name">Facility Name:</label><br><br>
                <input type="text" id="edit_facility_name" name="facility_name" required><br><br>
                <label for="edit_description">Description:</label><br><br>
                <textarea id="edit_description" name="description" rows="4" required></textarea><br><br>
                <input type="submit" name="edit_facility" value="Save Changes" class="saveFacilityBtn">
            </form>
        </div>
    </div>

    <!-- Delete Facility Modal -->
    <div id="deleteFacilityModal" class="modal">
        <div class="modal-content">
            <span id="closeDeleteFacilityModal" class="close">&times;</span>
            <h2>Delete Facility</h2><br><br>
            <p>Are you sure you want to delete this facility?</p><br><br>
            <form id="editFacilityForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="hidden" name="facility_id" value="<?php echo $facility['facility_id']; ?>">
            <label for="edit_facility_name">Facility Name:</label><br><br>
            <input type="text" id="edit_facility_name" name="facility_name" value="<?php echo $facility['facility_name']; ?>" required><br><br>
            <label for="edit_description">Description:</label><br><br>
            <textarea id="edit_description" name="description" rows="4" required><?php echo $facility['description']; ?></textarea><br><br>
            <input type="submit" name="edit_facility" value="Save Changes" class="saveFacilityBtn">
            </form>

        </div>
    </div>


</center>


<!--ADMIN SETTINGS-->
<div class="adminSettings" id="adminSettings" style="display: none;">
    <button id="closeSettings">&#10006;</button>
    <span class="settings-content">
        <span class="first">
            <img src="<?php echo htmlspecialchars($_SESSION['userpic']); ?>" alt="profile-pic-of-admin" class="profile-pic" id="profilePhotoPreview">
        </span>
        <span class="second">
            <h2>Name : <?php echo htmlspecialchars($email); ?></h2><br><br>
            <h2>Change Password : </h2><br>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="old_password">Old Password:</label><br>
                <input type="password" id="old_password" name="old_password" required><br><br>
                <label for="new_password">New Password:</label><br>
                <input type="password" id="new_password" name="new_password" required><br><br>
                <label for="confirm_password">Confirm Password:</label><br>
                <input type="password" id="confirm_password" name="confirm_password" required><br><br>
                <input type="submit" value="Change Password">
            </form>
        </span>
        <span class="third">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                <br><br>
                <label for="profile_photo">Change Profile Photo</label><br><br>
                <input type="file" name="profile_photo" accept="image/*" required onchange="previewProfilePhoto(event)"><br><br>
                <input type="submit" value="Upload Photo" class="uploadPhoto"><br><br>
            </form><br>
            <button class="logoutBtn" onclick="location.href='back-end/logout.php'"><img src="styles/imgs/logout.png" alt="logout-icon">&nbsp; Log-Out</button>
        </span>
    </span>
</div>

<script src="admindom.js"></script>
</body>
</html>
