// Crea una constante para trabajar con los input de radio
const radios = document.querySelectorAll('input[name="identificacion"]');

// Por cada radio se llama a la función rellenarNacionalidad pasándole el valor del radio
radios.forEach(radio => {
    radio.addEventListener('change', function() {
        rellenarNacionalidad(this.value);
    });
})

// Función para rellenar la lista de opciones de la nacionalidad según el valor del radio seleccionado
function rellenarNacionalidad(valorSeleccionado) { // valorSeleccionado es el nombre de la variable pasada por parámetros

    // Crea una constante para trabajar con el input select de nacionalidad
    const select = document.getElementById("nacionalidad");

    // Vacía las opciones del select
    select.innerHTML = "";

    // Según el valor de identificacion se rellenará el select con España si es dni o con una lista de opciones que recibe en foormato JSON de una base de datos
    if (valorSeleccionado == "dni") {
        const optionElement = document.createElement("option");
        optionElement.value = 199;
        optionElement.textContent = "España";
        select.appendChild(optionElement);

    } else if (valorSeleccionado == "nie") {

        // Llama al archivo PHP para obtener los países desde la base de datos
        fetch('php/databases/paisesBD.php')
            .then(response => response.json())
            .then(data => {

                const paises = data.paises;

                // Por cada país añade una opcion dentro del select
                paises.forEach(pais => {
                    const optionElement = document.createElement("option");
                    optionElement.value = pais.id;
                    optionElement.textContent = pais.nombre;
                    select.appendChild(optionElement);
                });
            })
            .catch(error => {
                console.error('Error al cargar los países:', error);
            });
    } else {
        alert("Error de nacionalidad")
    }
}

/*
// Función para rellenar el select de localidad según el código postal obsoleta por estar censurada la API
function rellenarLocalidad(codigoPostalIntroducido) {

    const select = document.getElementById("localidad");
    select.innerHTML = "";

    fetch("php/apis/codigosPostales.php/codPostal?codPostal=" + codigoPostalIntroducido)
        .then(response => response.json())
        .then(result => {

            // Limpia la lista para no dar conflicto al ejecutarse la función
            localidadesSet.clear();

            // Por cada localidad se añade a la lista
            result.forEach(item => {
                localidadesSet.add(item.city);
            });

            // Por cada localidad en la lista se añade la opción en el select
            localidadesSet.forEach(localidad => {
                const optionElement = document.createElement("option");
                optionElement.value = localidad;
                optionElement.textContent = localidad;
                select.appendChild(optionElement);
            });
        })
        .catch(error => console.error('Error al cargar las localidades:', error));
    
}
*/

// Función para rellenar el select de provincias según el código postal
function rellenarProvincia(codigoPostalIntroducido) {
    const select = document.getElementById("provincia");
    const postalNV = document.getElementById("postalNV");
    select.innerHTML = ""; // Limpiamos el select

    /*
    // Este fetch llamaría a la API que está censurada para realizar esta función pero como está censurada no funciona
    fetch("php/apis/codigosPostales.php/codPostal?codPostal=" + codigoPostalIntroducido)
        .then(response => response.json())
        .then(result => {

            // Limpia la lista para no dar conflicto al ejecutarse la función
            provinciasSet.clear();

            // Por cada provincia se añade a la lista
            result.forEach(item => {
                provinciasSet.add(item.state);
            });

            // Establece la viarable a true para comprobar que la función ya se ha ejecutado
            provinciasCargadas = true;

            // Si tanto provincias como regiones fiscales ya están cargadas, hace la comparación llamando a la función correspondiente
            if (provinciasCargadas && regionesCargadas) {
                compararProvinciasConRegiones();
            }

            // Por cada provincia en la lista se añade la opción en el select
            provinciasSet.forEach(provincia => {
                const optionElement = document.createElement("option");
                optionElement.value = provincia;
                optionElement.textContent = provincia;
                select.appendChild(optionElement);
            });
        })
        .catch(error => console.error('Error al cargar las provincias:', error));
    */

    // Obtiene los dos primeros dígitos del código postal
    const dosPrimerosDigitos = codigoPostalIntroducido.substring(0, 2);

    // Solicitud al archivo JSON
    fetch("json/codigosPostales.json")
        .then(response => {
            if (!response.ok) {
                throw new Error("No se pudo cargar el archivo JSON.");
            }
            return response.json();
        })
        .then(data => {
            // Verifica que la estructura del JSON sea la esperada
            if (!data.codigo || !Array.isArray(data.codigo)) {
                throw new Error("El archivo JSON no tiene la estructura esperada.");
            }

            // Filtra las provincias que coincidan con los dos primeros dígitos
            const provinciasCoincidentes = data.codigo.filter(item => item.cp === dosPrimerosDigitos);

            // Añade las opciones al select
            provinciasCoincidentes.forEach(item => {
                const optionElement = document.createElement("option");
                optionElement.value = item.nombre;
                optionElement.textContent = item.nombre;
                select.appendChild(optionElement);
            });

            // Verifica si hay provincias coincidentes
            if (provinciasCoincidentes.length === 0) {
                postalNV.style.opacity = "1";
                postalNV.innerText = "No hay provincias con este código";
            } else {
                postalNV.style.opacity = "0"; // Oculta el mensaje de error
            }

        })
        .catch(error => {
            console.error('Error al cargar las provincias:', error);
            postalNV.style.opacity = "1";
            postalNV.innerText = "Error al cargar las provincias";
        });
}


// Obtención de las regiones fiscales de la base de datos y adición al select correspondiente
const selectFiscal = document.getElementById("regionFiscal");
selectFiscal.innerHTML = "";

fetch('php/databases/regionesFiscalesBD.php')
    .then(response => response.json())
    .then(data => {

        // Variable para almacenar como array las regiones que se reciben en el JSON y luego añadirlo al array creado anteriormente
        const regiones = data.regiones;
        regionesFiscales = regiones;

        // Por cada región en el array se añade la opción en el select
        regiones.forEach(region => {
            const optionElement = document.createElement("option");
            optionElement.value = region.id;
            optionElement.textContent = region.nombre;
            selectFiscal.appendChild(optionElement);
        });
        
    })
    .catch(error => {
        console.error('Error al cargar las regiones fiscales:', error);
    });



// Rellena el select de provincias según el código postal
document.getElementById("codPostal").addEventListener("input", function () {
    const codigoPost = this.value;
    const postalNV = document.getElementById("postalNV");

    if (codigoPost.length === 0) {
        // Si no hay nada en el input, oculta el mensaje de error
        postalNV.style.opacity = "0";
    } else {
        // Si hay algo en el input, llama a la función para rellenar las provincias
        rellenarProvincia(codigoPost);
    }
});