console.log("JS file loaded");

function validateForm() {
    console.log("validateForm called");

    const name = document.getElementById("name").value.trim();
    const personalId = document.getElementById("personal_id").value.trim();
    const phone = document.getElementById("phone").value.trim();
    const workplace = document.getElementById("workplace").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    if (name === "") {
        alert("Please enter your full name.");
        return false;
    }

    if (personalId.length < 16) {
        alert("Personal ID must be at least 16 characters.");
        return false;
    }

    const phonePattern = /^\+2507[89]\d{7}$/;
    if (!phone.match(phonePattern)) {
        alert("Please enter a valid Rwandan phone number starting with +25078 or +25079 followed by 7 digits (total 13 characters).");
        return false;
    }

    if (workplace === "") {
        alert("Please enter your workplace.");
        return false;
    }

    const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,}$/i;
    if (!email.match(emailPattern)) {
        alert("Please enter a valid email address.");
        return false;
    }

    if (password.length < 6) {
        alert("Password should be at least 6 characters long.");
        return false;
    }

    return true;
}

window.validateForm = validateForm;

