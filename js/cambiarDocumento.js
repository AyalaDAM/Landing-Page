// Deshabilita el campo de texto para introducir DNI o NIE
const dniInput = document.getElementById("dni");
dniInput.disabled = true;

// Crea una constante para trabajar con el input radio
const radio = document.querySelectorAll('input[name="identificacion"]');

// Por cada radio crea un listener que ejecuta una función con cada cambio realizado
radio.forEach(radio => {
    radio.addEventListener('change', function() {

        // Variables para manipular el label, input y mensaje de error relativos al DNI o NIE
            let changeDniNie = document.getElementById("DniNie");
            let changePH = document.getElementById("dni");
            let dniNok = document.getElementById("dniNok");

            // Al cambiar elimina el contenido del input dni (que también sirve para nie a pesar del nombre)
        document.querySelector('#dni').value = '';
        dniNok.style.opacity = "0";
        
        // Según el valor cambia el input para corresponderse a un DNI o un NIE y activa el campoo de texto
        if (this.value == "dni") {
            changeDniNie.innerText = "DNI";
            changePH.placeholder = "Ej. 01234589A"
            dniInput.disabled = false;
        } else if (this.value == "nie") {
            changeDniNie.innerText = "NIE";
            changePH.placeholder = "Ej. X0123458A"
            dniInput.disabled = false;
        } else {
            // En caso de no ser ninguno de los dos vuelve a deshabilitar el campo de texto y lo resetea
            dniInput.disabled = true;
            dniInput.value = "";
            changeDniNie.innerText = "";
        }
    })
})