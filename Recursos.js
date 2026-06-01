const ArrayEquiposRegistrados = [];
const botonAgregarEquipo = document.getElementById("botonAgregarEquipo");
if (!botonAgregarEquipo) {
  console.error('Falta la referencia al botón de Agregar Equipo');
}


botonAgregarEquipo.addEventListener('click', function (event) {
  event.preventDefault(); 

  const nombreEquipoInput = document.getElementById("nombreEquipo");
  const tipoEquipoSelect = document.getElementById("tipoEquipo");

  const nombreEquipo = nombreEquipoInput.value.trim();
  const tipoEquipo = tipoEquipoSelect.value;

 if (nombreEquipo.length > 20 ) {
    alert("El nombre del equipo no puede exceder los 20 caracteres.");
    return;
  }
 });


