document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("newUserForm");
    const feedback = document.getElementById("feedback");

    form.addEventListener("submit", (e) => {
        e.preventDefault();

        const formData = new FormData(form);

        fetch("new_user.php", {
            method: "POST",
            body: formData,
        })
            .then((response) => response.json())
            .then((data) => {
                feedback.style.display = "block";

                if (data.success) {
                    feedback.textContent = "User created successfully!";
                    feedback.style.color = "green";
                } else {
                    feedback.textContent = data.error || "An error occurred.";
                    feedback.style.color = "red";
                }
            })
            .catch(() => {
                feedback.style.display = "block";
                feedback.textContent = "Failed to create user. Please try again.";
                feedback.style.color = "red";
            });
    });
});
