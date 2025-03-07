/**
 * Listeners para envíar los datos de los formularios al backend para su validación y procesamiento y, tras ello, realizar
 * las transiciones correspondientes mediante funciones.
 */

// Variables para manipular los formularios correspondientes
const formulario1 = document.querySelector('#form1');
const formulario2 = document.querySelector('#form2');
const formulario3 = document.querySelector('#form3');

// Realiza el envío del formulario correspondiente mediante la función enviarFormulario
document.querySelector('#registro').addEventListener('click', function(event) {
    event.preventDefault();
    enviarFormulario(formulario1);
    console.log("Enviado 1")
})

// Realiza el envío del formulario correspondiente mediante la función enviarFormulario
document.querySelector('#continuar').addEventListener('click', function(event) {
    event.preventDefault();
    enviarFormulario(formulario2);
    console.log("Enviado 2")
})

// Realiza el envío del formulario correspondiente mediante la función enviarFormulario
document.querySelector('#finalizar').addEventListener('click', function(event) {
  if (!enviarFormularioCompleto(formulario1,formulario2,formulario3)) {
  event.preventDefault();
  }
  console.log("Enviado 3")
})

// Función para recibir un formulario, enviarlo al archivo que lo valida y, en caso de recibir una respuesta válida, realiza la transición correspondiente
function enviarFormulario(formulario) {

    // Variable para contener los datos del formulario recibido
    const formData = new FormData(formulario);
  
    // Adición del id del formulario para identificarlo en el archivo que recibe varios para sus validaciones
    formData.append('formu', formulario.id);

    // Envío del formulario en formData para recibir una respuesta JSON Success true o false
    fetch('php/recibirDatosRegistro.php', {
      method: 'POST',
      body: formData,
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {

        console.log('Formulario enviado correctamente.')

        // Según el id del formulario llama a la función de transisición correspondiente
        if (formulario.id == "form1") {
            transicionRegistro();
        } else if (formulario.id == "form2") {
            transicionContinuar();
        }

      } else {
        console.log('Error al enviar formulario:', data.message);
        alert("Debes completar todos los datos del formulario")
      }
    })
    .catch(error => console.error('Error:', error));
  }

  function enviarFormularioCompleto(formulario1, formulario2, formulario3) {
    // Crea un objeto FormData para cada formulario
    const formData1 = new FormData(formulario1);
    const formData2 = new FormData(formulario2);
    const formData3 = new FormData(formulario3);

    // Crea un objeto FormData vacío para combinar todos los datos
    const formDataFinal = new FormData();

    // Agrega los datos del primer formulario a formDataFinal
    formData1.forEach((value, key) => {
        formDataFinal.append(key, value);
    });

    // Agrega los datos del segundo formulario a formDataFinal
    formData2.forEach((value, key) => {
        formDataFinal.append(key, value);
    });

    // Agrega los datos del tercer formulario a formDataFinal
    formData3.forEach((value, key) => {
        formDataFinal.append(key, value);
    });

    // Añade un dato adicional para indicar que es el formulario completo
    formDataFinal.append('formu', 'form3');

    // Muestra las keys de formDataFinal
    formDataFinal.forEach((value, key) => {
        console.log(`Key: ${key}, Value: ${value}`);
    });

    // Envía los datos del formulario al servidor
    fetch('php/recibirDatosRegistro.php', {
        method: 'POST',
        body: formDataFinal,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Formulario completo enviado correctamente.');
            return true;
        } else {
            console.log('Error al enviar formulario completo:', data.message);
            alert("Debes completar todos los datos del formulario");
        }
    })
    .catch(error => console.error('Error:', error));
}

// Función para realizar la transición del primer al segundo formulario
function transicionRegistro() {
    // Variable para el contenedor de formularios
    const formContainer = document.querySelector('.formtest');

    // Variables para los formularios individualmente
    const form1 = document.querySelector('.form-container');
    const form2 = document.querySelector('.form-container2');
    const form3 = document.querySelector('.form-container3');

    // Añadir a la lista de la clase la subclase con translateX para mover el 1 a la izquierda y que se oculte
    form1.classList.add('hide');

    // Añadir a la lista de la clase la subclase con translateX para mover el 2 a la izquierda y que se muestre
    form2.classList.add('show');

    // Añadir a la lista de la clase la subclase con translateX para mover el 3 a la izquierda y que se prepare justo para salir
    form3.classList.add('show1');
}

// Función para realizar la transición del segundo al tercer formulario
function transicionContinuar() {
    // Variable para el contenedor de formularios
    const formContainer = document.querySelector('.formtest');

    // Variables para los formularios individualmente
    const form1 = document.querySelector('.form-container');
    const form2 = document.querySelector('.form-container2');
    const form3 = document.querySelector('.form-container3');

    // Quitar de la lista de la clase la subclase con translateX para que el 2 no se quede en el sitio
    form2.classList.remove('show');

    // Añadir a la lista de la clase la subclase con translateX para mover el 2 a la izquierda y que se oculte
    form2.classList.add('hide');

    // Añadir a la lista de la clase la subclase con translateX para mover el 3 a la izquierda y que se muestre
    form3.classList.add('show2');
}



/* Eventos para convertir en funciones más adelante */

// Evento para realizar una función cada vez que se le da click al botón atrás del segundo formulario
document.querySelector('#atras1').addEventListener('click', function(event) {
    event.preventDefault(); // Prevenir el action del botón

    // Variable para el contenedor de formularios
    const formContainer = document.querySelector('.formtest');

    // Variables para los formularios individualmente
    const form1 = document.querySelector('.form-container');
    const form2 = document.querySelector('.form-container2');
    const form3 = document.querySelector('.form-container3');

    // Quitar a la lista de la clase la subclase con translateX para mover el 1 a la derecha y que se muestre
    form1.classList.remove('hide');

    // Quitar a la lista de la clase la subclase con translateX para mover el 2 a la derecha y que se oculte
    form2.classList.remove('show');

    // Quitar a la lista de la clase la subclase con translateX para mover el 3 a la derecha y que vaya a la posición inicial
    form3.classList.remove('show1');
});

// Evento para realizar una función cada vez que se le da click al botón atrás del tercer formulario
document.querySelector('#atras2').addEventListener('click', function(event) {
    event.preventDefault(); // Prevenir el action del botón

    // Variable para el contenedor de formularios
    const formContainer = document.querySelector('.formtest');

    // Variables para los formularios individualmente
    const form1 = document.querySelector('.form-container');
    const form2 = document.querySelector('.form-container2');
    const form3 = document.querySelector('.form-container3');

    // Añadir de la lista de la clase la subclase con translateX para que el 2 vuelva a su sitio
    form2.classList.add('show');
    // Quitar a la lista de la clase la subclase con translateX para mover el 2 a la derecha y que se muestre
    form2.classList.remove('hide');

    // Quitar a la lista de la clase la subclase con translateX para mover el 3 a la derecha y que se oculte
    form3.classList.remove('show2');
});