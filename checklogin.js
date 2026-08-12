fetch("api_login.php")
    .then(r => r.json())
    .then(data => {
        if (!data.autenticado) {
            window.location.href = "login.html";
            return;
        }
        const u = data.usuario;
        document.getElementById("userInfo").textContent = u.nom + " " + u.ape + " — " + u.rol;
    });

document.getElementById("btnCerrarSesion").addEventListener("click", function(e) {
    e.preventDefault();
    fetch("api_login.php?accion=cerrar")
        .then(() => { window.location.href = "login.html"; });
});