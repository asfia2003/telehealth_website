<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'doctor') {
    header("Location: login.html");
    exit();
}

$doctor_id = $_SESSION['user_id'];
$doctor_name = $_SESSION['name'];

// Fetch appointments for the doctor
$appointments_sql = "SELECT a.id, u.name AS patient_name, a.appointment_date, a.status, a.patient_id 
                     FROM appointments a
                     JOIN users u ON a.patient_id = u.id
                     WHERE a.doctor_id = '$doctor_id' 
                     ORDER BY a.appointment_date ASC";
$appointments_result = $conn->query($appointments_sql);

// Fetch messages for the doctor
$messages_sql = "SELECT m.message, u.name AS patient_name, m.created_at 
                 FROM messages m
                 JOIN users u ON m.patient_id = u.id
                 WHERE m.doctor_id = '$doctor_id'
                 ORDER BY m.created_at DESC";
$messages_result = $conn->query($messages_sql);

// Handle appointment actions (Approve / Deny)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointment_id = $_POST['appointment_id'];
    $patient_id = $_POST['patient_id'];
    $action = $_POST['action'];

    // Update appointment status
    $update_sql = "UPDATE appointments SET status = '$action' WHERE id = '$appointment_id'";
    $conn->query($update_sql);

    // Add message for patient
    $message = $action == 'approved' ? 
               "Your appointment has been approved by Dr. $doctor_name." : 
               "Your appointment has been denied by Dr. $doctor_name.";
    $message_sql = "INSERT INTO messages (patient_id, doctor_id, message) VALUES ('$patient_id', '$doctor_id', '$message')";
    $conn->query($message_sql);

    // Redirect to refresh the dashboard
    header("Location: doctor_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="doctor.css">
</head>
<body>
<div class="dashboard-container">
    <div class="sidebar">
        <h2>Doctor Dashboard</h2>
        <ul>
            <li><a href="#">Welcome, Dr. <?php echo $doctor_name; ?></a></li>
            <li><a href="#">Appointments</a></li>
            <li><a href="#">Messages</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <h1>Patient Appointments</h1>
        <?php if ($appointments_result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Patient Name</th>
                    <th>Appointment Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $appointments_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['patient_name']; ?></td>
                        <td><?php echo date('d M Y, h:i A', strtotime($row['appointment_date'])); ?></td>
                        <td><?php echo ucfirst($row['status']); ?></td>
                        <td>
                            <form action="doctor_dashboard.php" method="POST">
                                <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="patient_id" value="<?php echo $row['patient_id']; ?>">
                                <?php if ($row['status'] == 'pending'): ?>
                                    <button type="submit" name="action" value="approved">Approve</button>
                                    <button type="submit" name="action" value="cancelled">Deny</button>
                                <?php else: ?>
                                    <em><?php echo ucfirst($row['status']); ?></em>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No appointments at the moment.</p>
        <?php endif; ?>

        <div class="message-box">
            <h2>Messages</h2>
            <?php if ($messages_result->num_rows > 0): ?>
                <ul>
                    <?php while ($row = $messages_result->fetch_assoc()): ?>
                        <li>
                            <strong><?php echo $row['patient_name']; ?>:</strong> 
                            <?php echo $row['message']; ?> 
                            <em>(<?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?>)</em>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No messages yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>