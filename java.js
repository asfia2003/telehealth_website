
document.addEventListener("DOMContentLoaded", () => {
    const roleSelect = document.getElementById("role");
    const doctorFields = document.getElementById("doctorFields");

    // Toggle doctor fields based on role selection
    roleSelect.addEventListener("change", () => {
        if (roleSelect.value === "doctor") {
            doctorFields.style.display = "block";
        } else {
            doctorFields.style.display = "none";
        }
    });
});