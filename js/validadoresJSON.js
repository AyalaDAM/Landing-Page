/**
 * Listeners para recoger los datos introducidos en los campos del formulario para enviarlos en formato JSON a validadoresJSON y
 * que se hagan las validaciones del backend para mostrar u ocultar a tiempo real mensajes datos válidos o inválidos.
 */

// Recoge el valor del contenido del input nombre y realiza las validaciones
document.getElementById("nombre").addEventListener("input",function() {

    // Crea un JSON con el dato nombre y valor igual al del input nombre
    let jsonNombre = {
        nombre: nombre.value
    }
    
    // Variable para manipular el CSS del mensaje de error de nombre no válido
    let nombreNV = document.getElementById("nombreNV");

    // Llamada al archivo de validadores de datos, envía JSON y recibe JSON
    fetch('php/validadoresJSON.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify(jsonNombre)
    })
    .then(response => response.json())
    .then(result => {   // Result es un JSON con un dato valid y valor true o false

        // Si valid es falso muestra el mensaje de error, si es true lo oculta
        if (result.valid == false) {
            nombreNV.style.opacity = "1";
        } else {
            nombreNV.style.opacity = "0";
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
    });
})


// Recoge el valor del contenido del input apellido1 y realiza las validaciones
document.getElementById("apellido1").addEventListener("input",function() {

    let jsonApellido1 = {
        apellido1: apellido1.value
    }
    
    let apellido1NV = document.getElementById("apellido1NV");

    fetch('php/validadoresJSON.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify(jsonApellido1)
    })
    .then(response => response.json())
    .then(result => {

        if (result.valid == false) {
            apellido1NV.style.opacity = "1";
        } else {
            apellido1NV.style.opacity = "0";
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
    });
    })

// Recoge el valor del contenido del input apellido2 y realiza las validaciones
document.getElementById("apellido2").addEventListener("input",function() {

    let jsonApellido2 = {
        apellido2: apellido2.value
    }
    
    let apellido2NV = document.getElementById("apellido2NV");

    fetch('php/validadoresJSON.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify(jsonApellido2)
    })
    .then(response => response.json())
    .then(result => {

        if (result.valid == false) {
            apellido2NV.style.opacity = "1";
        } else {
            apellido2NV.style.opacity = "0";
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
    });
})



// Recoge el valor del contenido del input dni y realiza las validaciones
document.getElementById("dni").addEventListener("input",function() {

    // Variable para contener el valor del area de texto de DNI o NIE
    let checkDniNie = dni.value;
    // Variable para contener el valor del radio seleccionado
    let checkRadio = document.querySelector('input[name="identificacion"]:checked');

    // Variable para manipular el CSS del mensaje de error de DNI
    let dniNok = document.getElementById("dniNok");

    if (checkDniNie.length == 9) {  // Se ejecuta si la longitud de DNI es 9, es decir, una longitud válida

        // Variable para estructurar el JSON que contendrá DNI o NIE
        let jsonDniNie = {}

        // Si checkRadio está seleccionando DNI el JSON contendrá DNI, en caso contrario NIE
        if (checkRadio && checkRadio.value === "dni") {
            jsonDniNie = {dni: dni.value};
        } else if (checkRadio && checkRadio.value === "nie") {
            jsonDniNie = {nie: dni.value};
        } else {
            alert("Selecciona un tipo de documentación primero");
        }

        // Fetch para mandar en raw el JSON a otro archivo y recibir un JSON con valid true o false
        fetch('php/validadoresJSON.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(jsonDniNie)
        })
        .then(response => response.json())
        .then(result => {   // Result es un JSON con un dato valid y valor true o false

            // Si el DNI o NIE tiene un formato válido se ejecutaría el if que haría una llamada a la API mirror pero como está censurada simplemente da luz verde
            if (result.valid == true) {

                /*
                // API mirror para ocultar la API censurada

                const formdata = new FormData();
                // Se le manda el valor que está en el campo de texto de DNI o NIE
                formdata.append("validateDNI", checkDniNie);

                const requestOptions = {
                method: "POST",
                body: formdata,
                redirect: "follow"
                };

                // Fetch para mandar en formdata el valor de DNI o NIE a la API real que consulta la base de datos (used significa que existe y notused que no)
                fetch("php/apis/comprobarDNI.php", requestOptions)
                .then((response) => response.json())
                .then((result) => {     // Result es un JSON con un dato response con valor used o notused

                    // Si response es used muestra el mensaje de error de identificación ocupada, si es notused muestra uno de luz verde
                    if (result.response == "used") {
                        dniNok.style.opacity = "1";
                        dniNok.style.color = "red";
                        dniNok.innerText = "Identificación ocupada";
                    } else if (result.response == "notused") {
                        dniNok.style.opacity = "1";
                        dniNok.style.color = "green";
                        dniNok.style.marginTop = "1px";
                        dniNok.innerText = "Identificación válida";
                    }
                })
                .catch((error) => console.error(error));
                */

                dniNok.style.opacity = "1";
                dniNok.style.color = "green";
                dniNok.style.marginTop = "1px";
                dniNok.innerText = "Identificación válida";

            } else {    // En caso que valid sea false significa que el DNI o NIE no tiene un formato válido y muestra un mensaje de error
                dniNok.style.opacity = "1";
                dniNok.style.color = "red";
                dniNok.innerText = "Identificación no válida";
            }
        })
        .catch(error => {
            console.error('Error en la solicitud:', error);
        });

    } else if (checkDniNie.length != 9 && checkDniNie.length != 0) {    // En caso de que la longitud sea diferente a 9 y no esté vacío muestra un mensaje de error

        dniNok.style.opacity = "1";
        dniNok.style.color = "red";
        dniNok.innerText = "Identificación no válida";

    } else {    // En cualquier otro caso oculta el mensaje de error

        dniNok.style.opacity = "0";

    }
})



// Obtiene la fecha actual
const today = new Date();

// Calcula la fecha mínima (hace 80 años)
const minDate = new Date();
minDate.setFullYear(today.getFullYear() - 80);
const minDateString = minDate.toISOString().split('T')[0]; // Convertir a formato YYYY-MM-DD

// Calcula la fecha máxima (hace 18 años)
const maxDate = new Date();
maxDate.setFullYear(today.getFullYear() - 18);
const maxDateString = maxDate.toISOString().split('T')[0]; // Convertir a formato YYYY-MM-DD

// Asigna las fechas mínimas y máximas al input
const inputFecha = document.getElementById('fecNac');
inputFecha.setAttribute('min', minDateString);
inputFecha.setAttribute('max', maxDateString);

// Llama a la función que contiene con el valor del input fecha cuando tenga alguno
inputFecha.addEventListener('input', function() {
    validarFechaNacimiento();
});

// Función de validación de la fecha
function validarFechaNacimiento() {

    // Variable objeto fecha que es igual al valor del input fecha
    const fechaIngresada = new Date(inputFecha.value);
    const fecNacNV = document.getElementById('fecNacNV');
  
    // Verifica si la fecha es válida y en caso de no serlo muestra el mensaje de error y establece a no valido el input
    if (fechaIngresada < minDate || fechaIngresada > maxDate || isNaN(fechaIngresada)) {
      inputFecha.setCustomValidity("Fecha no válida.");
      fecNacNV.style.opacity = "1";
    } else {
      inputFecha.setCustomValidity("");
      fecNacNV.style.opacity = "0";
    }
}



// Recoge el valor del contenido del input domicilio y realiza las validaciones
document.getElementById("domicilio").addEventListener("input",function() {

    let jsonDomicilio = {
        domicilio: domicilio.value
    }

    let domicilioNV = document.getElementById("domicilioNV");

    fetch('php/validadoresJSON.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify(jsonDomicilio)
    })
    .then(response => response.json())
    .then(result => {

        if (result.valid == false) {
            domicilioNV.style.opacity = "1";
        } else {
            domicilioNV.style.opacity = "0";
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
    });
})


// Recoge el valor del contenido del input email y realiza las validaciones
document.getElementById("email").addEventListener("input",function() {

    let jsonEmail = {
        email: email.value
    }

    const emailCheck = document.getElementById("email").value;
    let emailNV = document.getElementById("emailNV");

    if (emailCheck != 0) {
        fetch('php/validadoresJSON.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(jsonEmail)
        })
        .then(response => response.json())
        .then(result => {
            if (result.valid == true) {

                emailNV.style.opacity = "0";

                /*
                const formdata = new FormData();
                formdata.append("validateMAIL", emailCheck)

                const requestOptions = {
                method: "POST",
                body: formdata,
                redirect: "follow"
                };

                fetch("php/apis/comprobarMAIL.php", requestOptions)
                .then((response) => response.json())
                .then((result) => {
                    if (result.response == "error") {
                        emailNV.style.opacity = "1";
                        emailNV.style.color = "red";
                        emailNV.innerText = "Email no válido";
                    } else if (result.response == "used") {
                        emailNV.style.opacity = "1";
                        emailNV.style.color = "red";
                        emailNV.innerText = "Email ocupado";
                    } else if (result.response == "notused") {
                        emailNV.style.opacity = "0";
                    }
                })
                .catch((error) => console.error(error));
                */

            } else {
                emailNV.innerText = "Email no válido";
                emailNV.style.opacity = "1";
            }
        })
        .catch(error => {
            console.error("Error en la solicitud:",error);
        });
    } else {
        emailNV.style.opacity = "0";
    }
})


// Recoge el valor del contenido del input confirmaEmail y comprueba que sea igual al input email
document.getElementById("confirmaEmail").addEventListener("input",function() {

    let confirmaEmail = document.getElementById("confirmaEmail").value;
    let email = document.getElementById("email").value;
    let confirmaNV = document.getElementById("confirmaNV");

    if (confirmaEmail != email) {
        confirmaNV.style.opacity = "1";
    } else {
        confirmaNV.style.opacity = "0";
    }
})


// c
document.getElementById("telefono").addEventListener("input",function() {

    let telefonoCheck = telefono.value;
    let telefonoNV = document.getElementById("telefonoNV");

    let jsonTelefono = {
        telefono: telefono.value
    }

    if (telefonoCheck.length != 0) {
        fetch('php/validadoresJSON.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(jsonTelefono)
        })
        .then(response => response.json())
        .then(result => {
            if (result.valid == false) {
                telefonoNV.style.opacity = "1";
            } else {
                telefonoNV.style.opacity = "0";
            }
        })
        .catch(error => {
            console.error("Error en la solicitud:",error);
        });
    } else {
        telefonoNV.style.opacity = "0";
    }
})



// Función para comprobar que una cadena de texto no contenta solo números
function contieneNumerosYLetras(valor) {
    return /[a-zA-Z]/.test(valor) && !/^\d+$/.test(valor);
}

// Función devuelve true si no encuentra una palabra en una lista
function noContienePalabrasSoeces(valor, lista) {
    return !lista.some(palabra => new RegExp(palabra, "i").test(valor));
}

// Recoge el valor del contenido del input usuario y realiza validaciones
document.getElementById("usuario").addEventListener("input",function() {

    let usuarioCheck = usuario.value;
    let usuarioNV = document.getElementById("usuarioNV")

    if (usuarioCheck.length != 0) {

        if (usuarioCheck.length > 30) {

            usuarioNV.innerText = "El nombre no debe tener más de 30 caracteres";
            usuarioNV.style.opacity = "1";

        } else {

            if (contieneNumerosYLetras(usuarioCheck)) {
                usuarioNV.innerText = "Nombre de usuario no válido";
                usuarioNV.style.opacity = "0";

                fetch('json/palabrasSoeces.json')
                    .then(response => response.json())
                    .then(data => {
                        
                        const palabrasSoeces = noContienePalabrasSoeces(usuarioCheck, data.palabra);

                        if (palabrasSoeces) {

                            /*
                            const formdata = new FormData();
                            formdata.append("validateALIAS", usuarioCheck);

                            const requestOptions = {
                            method: "POST",
                            body: formdata,
                            redirect: "follow"
                            };

                            fetch("php/apis/comprobarALIAS.php", requestOptions)
                            .then((response) => response.json())
                            .then((result) => {
                                
                                if (result.response == "used") {
                                    usuarioNV.innerText = "Nombre de usuario ocupado";
                                    usuarioNV.style.color = "red";
                                    usuarioNV.style.opacity = "1";
                                } else if (result.response == "notused") {
                                    usuarioNV.innerText = "Nombre de usuario no válido";
                                    usuarioNV.style.opacity = "0";
                                }

                            })
                            .catch((error) => console.error(error));
                            */

                            usuarioNV.style.opacity = "0";
                            usuarioNV.innerText = "Nombre de usuario no válido";    // Aunque no se muestre error se cambia el texto para devolverlo al mensaje original

                        } else {

                            usuarioNV.innerText = "El nombre contiene una palabra soez";
                            usuarioNV.style.opacity = "1";

                        }

                    })
                    .catch(error => console.error('Error al cargar el archivo JSON:', error));

            } else {

                usuarioNV.innerText = "El nombre de usuario no debe contener únicamente números";
                usuarioNV.style.opacity = "1";

            }
        }

    } else {

        usuarioNV.innerText = "Nombre de usuario no válido";
        usuarioNV.style.opacity = "0";

    }
})



function contieneLetra(valor) {
    return /[a-zA-Z]/.test(valor);
}

function contieneNumero(valor) {
    return /[0-9]/.test(valor);
}

function contieneCaracterEspecial(valor) {
    return /[!@#$%^&*(),.?":{}|<>]/.test(valor);
}

function noContieneDatosPersonales(cadena, variables) {
    const cadenaLower = cadena.toLowerCase();
    let contador = 0;

    for (let i = 0; i < variables.length; i++) {
        const variable = String(variables[i]).toLowerCase();

        if (cadenaLower.includes(variable)) {
            contador++;
        }
    }

    console.log(contador)
    return !contador >= 1;
}

// Recoge el valor del contenido del input contraseña y realiza validaciones
document.getElementById("password").addEventListener("input",function() {

    let passwordCheck = password.value;
    let caracNV = document.getElementById("caracNV")
    let letraNV = document.getElementById("letraNV")
    let numNV = document.getElementById("numNV")
    let espeNV = document.getElementById("espeNV")
    let persNV = document.getElementById("persNV")

    let annoPass = new Date(document.getElementById('fecNac').value).getFullYear();

    let datosPersonales = [
        nombre.value,
        apellido1,
        apellido2,
        annoPass,
        "Mangel1321"    // Cambiar
    ]

    if (passwordCheck.length != 0) {

        if (passwordCheck.length >= 8 && passwordCheck.length <= 64) {
            caracNV.style.color = "green";
        } else {
            caracNV.style.color = "red";
        }

        if (contieneLetra(passwordCheck)) {
            letraNV.style.color = "green";
        } else {
            letraNV.style.color = "red";
        }

        if (contieneNumero(passwordCheck)) {
            numNV.style.color = "green";
        } else {
            numNV.style.color = "red";
        }

        if (contieneCaracterEspecial(passwordCheck)) {
            espeNV.style.color = "green";
        } else {
            espeNV.style.color = "red";
        }

        if (noContieneDatosPersonales(passwordCheck,datosPersonales)) {
            persNV.style.color = "green";
        } else {
            persNV.style.color = "red";
        }

    } else {
        caracNV.style.color = "black";
        letraNV.style.color = "black";
        numNV.style.color = "black";
        espeNV.style.color = "black";
        persNV.style.color = "black";
    }
})



// Recoge el valor del contenido del input confirmaPassword y comprueba que sea igual al input password
document.getElementById("confirmaPassword").addEventListener("input",function() {

    let confirmaPassword = document.getElementById("confirmaPassword").value;
    let password = document.getElementById("password").value;
    let confirmaPassNV = document.getElementById("confirmaPassNV");

    if (confirmaPassword != password) {
        confirmaPassNV.style.opacity = "1";
    } else {
        confirmaPassNV.style.opacity = "0";
    }
})


// Recoge el valor del contenido del input respuestaSeguridad y comprueba que no esté vacío
document.getElementById("respuestaSeguridad").addEventListener("input",function() {

    let respuestaSeguridad = document.getElementById("respuestaSeguridad").value;
    let respuestaNV = document.getElementById("respuestaNV");

    if (respuestaSeguridad != 0) {
        respuestaNV.style.opacity = "0";
    } else {
        respuestaNV.style.opacity = "1";
    }
})