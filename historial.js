const arrayTickets = [
  "Ticket 1",
  "Ticket 2",
  
];

function mostrarTickets() {
    //muestran la tabla de tickets y ocultan la de reportes
    const tablaTickets =  document.getElementById("tablaTickets");
    const cuerpoTablaTickets = document.getElementById("cuerpoTablaTickets");
    cuerpoTablaTickets.innerHTML = ""; // Limpiar la tabla antes de mostrar los tickets
    for (const ticket of arrayTickets) {
        const fila = document.createElement("tr");
        const celda = document.createElement("td");
        celda.textContent = ticket;
        fila.appendChild(celda);
        cuerpoTablaTickets.appendChild(fila);
        

    }
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



