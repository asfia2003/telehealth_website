<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user details
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];

            // Redirect based on role
            if ($user['role'] == 'doctor') {
                header("Location: doctor_dashboard.php");
            } else {
                header("Location: patient_dashboard.php");
            }
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "No user found with this email!";
    }
}
?>