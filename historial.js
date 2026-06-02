const arrayTickets = [];

function mostrarTickets() {
    //muestran la tabla de tickets y ocultan la de reportes
    const tablaTickets =  document.getElementById("tablaTickets");
    const cuerpoTablaTickets = document.getElementById("cuerpoTablaTickets");
    tablaTickets.classList.remove("d-none");
    //ocultan la tabla de reportes
    const tablaReportes =  document.getElementById("tablaReportes");
    const cuerpoTablaReportes = document.getElementById("cuerpoTablaReportes");
    tablaReportes.classList.add("d-none");

}

function mostrarReportes() {
    //muestran la tabla de reportes y ocultan la de tickets
    const tablaReportes =  document.getElementById("tablaReportes");
    const cuerpoTablaReportes = document.getElementById("cuerpoTablaReportes");
    tablaReportes.classList.remove("d-none");
    //ocultan la tabla de tickets
    const tablaTickets =  document.getElementById("tablaTickets");
    const cuerpoTablaTickets = document.getElementById("cuerpoTablaTickets");
    tablaTickets.classList.add("d-none");

}

document.getElementById("botonMostrarTickets").addEventListener("click", mostrarTickets);
document.getElementById("botonMostrarReportes").addEventListener("click", mostrarReportes);



