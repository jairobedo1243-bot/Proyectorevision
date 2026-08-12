


document.getElementById("loginForm").addEventListener("submit", async function(e) {
    e.preventDefault();
    const errorDiv = document.getElementById("loginError");
    errorDiv.style.display = "none";

    const email = document.getElementById("email").value;
    const contrasena = document.getElementById("contrasena").value;

    try {
        const res = await fetch("http://localhost/Proyectorevision-main/api_login.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ email, contrasena })
        });
        const data = await res.json();

        if (!res.ok) {
            errorDiv.textContent = data.error || "Error al iniciar sesión";
            errorDiv.style.display = "block";
            return;
        }

        window.location.href = "index.html";
    } catch (err) {
        errorDiv.textContent = "Error de conexión con el servidor";
        errorDiv.style.display = "block";
    }
});