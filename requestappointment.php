<?php
include 'db.php';  // Include your database connection
session_start();

// Ensure the user is logged in and is a patient
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'patient') {
    header("Location: login.html");  // Redirect if not a patient
    exit();
}

$patient_id = $_SESSION['user_id'];    // Patient ID from session
$doctor_id = $_POST['doctor_id'];      // Doctor ID from the form
$appointment_date = $_POST['appointment_date'];  // Appointment date from the form

// Debugging: check if the values are received correctly
// echo "Patient ID: $patient_id, Doctor ID: $doctor_id, Appointment Date: $appointment_date";

// Insert the appointment request into the database
$appointment_sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, status) 
                    VALUES ('$patient_id', '$doctor_id', '$appointment_date', 'pending')";

if ($conn->query($appointment_sql) === TRUE) {
    echo "Appointment request sent successfully.";
} else {
    echo "Error: " . $conn->error;  // Show the error if it fails
}
?>