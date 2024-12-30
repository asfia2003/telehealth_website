document.addEventListener("DOMContentLoaded", () => {
    const currentUser = JSON.parse(sessionStorage.getItem("currentUser"));

    if (!currentUser) {
        alert("You are not logged in. Redirecting to login page.");
        window.location.href = "login.html";
        return;
    }

    // Update UI with user data
    document.querySelector(".header h1").textContent = Welcome, ${currentUser.name};
    document.getElementById("lastLogin").textContent = currentUser.lastLogin || "First Login";

    // You can also dynamically load user-specific information here
});