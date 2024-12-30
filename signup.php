<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encrypt password

    // Insert into users table
    $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
    
    if ($conn->query($sql) === TRUE) {
        $user_id = $conn->insert_id;

        // If the role is doctor, insert into doctors table
        if ($role == "doctor") {
            $specialization = $_POST['specialization'];
            $experience = $_POST['experience'];
            $available_timings = $_POST['available_timings'];

            $doctor_sql = "INSERT INTO doctors (user_id, specialization, experience, available_timings) 
                           VALUES ('$user_id', '$specialization', '$experience', '$available_timings')";
            $conn->query($doctor_sql);
        }

        echo "Registration successful!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>