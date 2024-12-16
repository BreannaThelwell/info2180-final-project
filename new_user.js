document.addEventListener("DOMContentLoaded", function () {
    const newUserForm = document.getElementById("newUserForm");
    const feedback = document.getElementById("feedback");

    newUserForm.addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(newUserForm);

        fetch("new_user.php", {
            method: "POST",
            body: formData,
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    feedback.textContent = data.message;
                    feedback.style.color = "green";
                    feedback.style.display = "block";
                    newUserForm.reset();
                } else {
                    feedback.textContent = `Error: ${data.error}`;
                    feedback.style.color = "red";
                    feedback.style.display = "block";
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                feedback.textContent = "An unexpected error occurred.";
                feedback.style.color = "red";
                feedback.style.display = "block";
            });
    });
});
