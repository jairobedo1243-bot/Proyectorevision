// =============================================
// MÓDULO: Usuarios
// =============================================

const usuarios = [];

const botonAgregarUsuario = document.getElementById("botonAgregarUsuario");
const mensajeUsuario = document.getElementById("mensajeUsuario");

botonAgregarUsuario.addEventListener("click", function () {
    const nombre = document.getElementById("nombreUsuario").value.trim();
    const correo = document.getElementById("correoUsuario").value.trim();
    const rol    = document.getElementById("rolUsuario").value;

    mostrarMensaje(mensajeUsuario, "", "");

    if (nombre === "") {
        mostrarMensaje(mensajeUsuario, "Ingresá el nombre del usuario.", "error");
        return;
    }
    if (correo === "") {
        mostrarMensaje(mensajeUsuario, "Ingresá el correo electrónico.", "error");
        return;
    }
    if (rol === "") {
        mostrarMensaje(mensajeUsuario, "Seleccioná un rol.", "error");
        return;
    }

    const duplicado = usuarios.some(function (u) { return u.correo === correo; });
    if (duplicado) {
        mostrarMensaje(mensajeUsuario, "Ya existe un usuario con ese correo.", "error");
        return;
    }

    usuarios.push({ nombre: nombre, correo: correo, rol: rol });
    mostrarMensaje(mensajeUsuario, "Usuario agregado: " + nombre + " (" + rol + ")", "exito");

    document.getElementById("nombreUsuario").value = "";
    document.getElementById("correoUsuario").value = "";
    document.getElementById("rolUsuario").value    = "";

    renderTablaUsuarios();
});

function renderTablaUsuarios() {
    const contenedor = document.getElementById("contenedorTablaUsuarios");

    if (usuarios.length === 0) {
        contenedor.innerHTML = "<p class='empty-state'>No hay usuarios registrados aún.</p>";
        return;
    }

    let html = "<table><thead><tr><th>#</th><th>Nombre</th><th>Correo</th><th>Rol</th><th>Acción</th></tr></thead><tbody>";
    for (let i = 0; i < usuarios.length; i++) {
        const u = usuarios[i];
        html += "<tr>";
        html += "<td>" + (i + 1) + "</td>";
        html += "<td>" + u.nombre + "</td>";
        html += "<td>" + u.correo + "</td>";
        html += "<td><span class='badge badge-info'>" + u.rol + "</span></td>";
        html += "<td><button class='btn-danger btn-sm' onclick='eliminarUsuario(" + i + ")'>Eliminar</button></td>";
        html += "</tr>";
    }
    html += "</tbody></table>";
    contenedor.innerHTML = html;
}

function eliminarUsuario(i) {
    if (confirm("¿Eliminar el usuario \"" + usuarios[i].nombre + "\"?")) {
        usuarios.splice(i, 1);
        renderTablaUsuarios();
        mostrarMensaje(mensajeUsuario, "Usuario eliminado.", "info");
    }
}

// Helper compartido
function mostrarMensaje(el, texto, tipo) {
    el.textContent = texto;
    el.className   = "mensaje " + tipo;
}
