<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'patient') {
    header("Location: login.html");
    exit();
}

$patient_id = $_SESSION['user_id'];
$patient_name = $_SESSION['name'];

// Fetch all available doctors
$doctors_sql = "SELECT d.id, u.name AS doctor_name, d.specialization, d.experience, d.available_timings 
                FROM doctors d
                JOIN users u ON d.user_id = u.id";
$doctors_result = $conn->query($doctors_sql);

// Fetch messages from doctors
$messages_sql = "SELECT m.message, u.name AS doctor_name, m.created_at 
                 FROM messages m
                 JOIN users u ON m.doctor_id = u.id
                 WHERE m.patient_id = '$patient_id'
                 ORDER BY m.created_at DESC";
$messages_result = $conn->query($messages_sql);

// Display any session message
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <link rel="stylesheet" href="patient.css">
</head>
<body>
<div class="dashboard-container">
    <div class="sidebar">
        <h2>Patient Dashboard</h2>
        <ul>
            <li><a href="#">Welcome, <?php echo $patient_name; ?></a></li>
            <li><a href="#">Appointments</a></li>
            <li><a href="#">Messages</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <!-- Display success or error messages -->
        <?php if (isset($message)): ?>
            <div class="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <h1>Available Doctors</h1>
        <?php if ($doctors_result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Name</th>
                    <th>Specialization</th>
                    <th>Experience</th>
                    <th>Available Timings</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $doctors_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['doctor_name']; ?></td>
                        <td><?php echo $row['specialization']; ?></td>
                        <td><?php echo $row['experience']; ?> years</td>
                        <td><?php echo $row['available_timings']; ?></td>
                        <td>
                            <form action="requestappointment.php" method="POST">
                                <input type="hidden" name="doctor_id" value="<?php echo $row['id']; ?>">
                                <label for="appointment_date">Select Appointment Date:</label>
                                <input type="datetime-local" name="appointment_date" id="appointment_date" required>
                                <button type="submit">Request Appointment</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No doctors available at the moment.</p>
        <?php endif; ?>

        <div class="message-box">
            <h2>Your Messages</h2>
            <?php if ($messages_result->num_rows > 0): ?>
                <ul>
                    <?php while ($row = $messages_result->fetch_assoc()): ?>
                        <li>
                            <strong><?php echo $row['doctor_name']; ?>:</strong> 
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