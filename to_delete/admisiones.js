
    /*--------------------------------------------------------------------------------------------------------------------- */    

        document.addEventListener("DOMContentLoaded", function() {
    // Elementos de la interfaz
    const solicitudesPendientesSection = document.getElementById('solicitudes-pendientes');
    const detalleSolicitudSection = document.getElementById('detalle-solicitud');
    const btnVolverSolicitudes = document.getElementById('btnVolverSolicitudes');
    const rechazarSolicitudRadio = document.getElementById('rechazarSolicitud');
    const observacionesRechazo = document.getElementById('observacionesRechazo');
    const aprobarSolicitudRadio = document.getElementById('aprobarSolicitud');
    const solicitudIdDisplay = document.getElementById('solicitud-id-display');
    
    // Función para cargar datos desde el API
    function cargarSolicitudes() {
        fetch("http://localhost:3000/public/api/AdmissionsListarSolicitudes.php")
            .then(response => {
                if (!response.ok) throw new Error("Error al cargar datos");
                return response.json();
            })
            .then(data => {
                const tbody = document.querySelector("#solicitudes-pendientes tbody");
                tbody.innerHTML = ""; // Limpia contenido inicial

                data.forEach(solicitud => {
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td><span class="badge bg-info text-dark">${solicitud.ID_SOLICITUD}</span></td>
                        <td>${solicitud.NOMBRE_ESTUDIANTE}</td>
                        <td>${solicitud.CARRERA_PRINCIPAL}</td>
                        <td>${solicitud.FECHA_DE_ENVIO}</td>
                        <td><span class="badge ${obtenerClaseEstado(solicitud.ESTADO)}">${solicitud.ESTADO}</span></td>
                        <td>
                            <button class="btn btn-primary btn-sm btn-revisar" data-solicitud-id="${solicitud.ID_SOLICITUD}">Revisar</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });

                // Asignar eventos a los nuevos botones
                asignarEventosRevisar();
            })
            .catch(error => {
                console.error(error);
                alert("Error al cargar las solicitudes.");
            });
    }

    // Función para determinar la clase CSS según el estado
    function obtenerClaseEstado(estado) {
        switch(estado.toLowerCase()) {
            case 'pendiente': return 'bg-warning text-dark';
            case 'aprobado': return 'bg-success text-white';
            case 'rechazado': return 'bg-danger text-white';
            default: return 'bg-secondary text-white';
        }
    }

    // Función para asignar eventos a los botones "Revisar"
    function asignarEventosRevisar() {
        const revisarButtons = document.querySelectorAll('.btn-revisar');
        
        revisarButtons.forEach(button => {
            button.addEventListener('click', function() {
                const solicitudId = this.dataset.solicitudId;
                solicitudIdDisplay.textContent = solicitudId;

                // Ocultar lista y mostrar detalle
                solicitudesPendientesSection.style.display = 'none';
                detalleSolicitudSection.style.display = 'block';

                // Aquí se hace otro fetch para cargar los detalles específicos
                cargarDetalleSolicitud(solicitudId);
            });
        });
    }

    // Función para cargar el detalle de una solicitud (simplificado)
    function cargarDetalleSolicitud(solicitudId) {
        // Aquí iría tu llamada al backend para obtener los detalles
        // Por ahora usamos un ejemplo estático
        console.log(`Cargando detalles para solicitud ${solicitudId}`);

         rederCheckBox();
        
        // Restablecer el estado de los campos de decisión y observaciones
        aprobarSolicitudRadio.checked = false;
        rechazarSolicitudRadio.checked = false;
        observacionesRechazo.value = '';
        observacionesRechazo.disabled = true;
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => checkbox.checked = false);
    }

    // Evento para volver a la lista
    btnVolverSolicitudes.addEventListener('click', function() {
        detalleSolicitudSection.style.display = 'none';
        solicitudesPendientesSection.style.display = 'block';
    });

    // Habilitar/deshabilitar el campo de observaciones según la decisión
    rechazarSolicitudRadio.addEventListener('change', function() {
        observacionesRechazo.disabled = !this.checked;
    });

    aprobarSolicitudRadio.addEventListener('change', function() {
        if (this.checked) {
            observacionesRechazo.value = '';
            observacionesRechazo.disabled = true;
        }
    });

    // Lógica para el botón Guardar Decisión
    document.getElementById('btnGuardarDecision').addEventListener('click', function() {
        const decision = document.querySelector('input[name="decisionRevisor"]:checked');
        if (!decision) {
            alert('Por favor, selecciona si apruebas o rechazas la solicitud.');
            return;
        }

        const solicitudId = solicitudIdDisplay.textContent;
        const decisionValue = decision.value;
        const observaciones = observacionesRechazo.value;

        // Aquí iría tu llamada al backend para guardar la decisión
        console.log(`Decisión para ${solicitudId}: ${decisionValue} - ${observaciones}`);
        alert(`Decisión registrada para solicitud ${solicitudId}`);
    });

    // Cargar las solicitudes al inicio
    cargarSolicitudes();
});


document.addEventListener('DOMContentLoaded', function() {
    const nacionalidadSelect = document.getElementById('modalNacionalidad');
    const identidadContainer = document.getElementById('modalIdentidadContainer');
    const pasaporteContainer = document.getElementById('modalPasaporteContainer');
    const documentoIdentidadContainer = document.getElementById('modalDocumentoIdentidadContainer');
    const pasaporteDocumentoContainer = document.getElementById('modalPasaporteDocumentoContainer');
    const apostillaContainer = document.getElementById('modalApostillaContainer');
    const enviarBtn = document.getElementById('modalEnviarSolicitud');

    // Manejar cambio de nacionalidad
    nacionalidadSelect.addEventListener('change', function() {
        if (this.value === 'EX') {
            pasaporteContainer.style.display = 'block';
            pasaporteDocumentoContainer.style.display = 'block';
            apostillaContainer.style.display = 'block';
            documentoIdentidadContainer.style.display = 'none';
            
            // Actualizar requeridos
            document.getElementById('modalIdentidad').required = false;
            document.getElementById('modalDocumentoIdentidad').required = false;
            document.getElementById('modalPasaporte').required = true;
            document.getElementById('modalPasaporteDocumento').required = true;
        } else {
            pasaporteContainer.style.display = 'none';
            pasaporteDocumentoContainer.style.display = 'none';
            apostillaContainer.style.display = 'none';
            documentoIdentidadContainer.style.display = 'block';
            
            // Restaurar requeridos
            document.getElementById('modalIdentidad').required = true;
            document.getElementById('modalDocumentoIdentidad').required = true;
            document.getElementById('modalPasaporte').required = false;
            document.getElementById('modalPasaporteDocumento').required = false;
        }
    });

    // Manejar envío del formulario
    enviarBtn.addEventListener('click', function() {
        const form = document.getElementById('formularioAdmisionModal');
        if (form.checkValidity()) {
            // Aquí iría la lógica para enviar el formulario
            alert('Solicitud enviada con éxito. Será revisada por el personal correspondiente.');
            // Cerrar el modal después de enviar
            const modal = bootstrap.Modal.getInstance(document.getElementById('solicitudModal'));
            modal.hide();
        } else {
            form.reportValidity();
        }
    });
});


/*---------------------------------------------------------------------------------------------------------------- */
const validationData = ["Nombre corresponden con el titulo", 
                        "Apellidos corresponden con el titulo", 
                        "Numero de identidad consistente",
                        "Numero  telefonico consistente", 
                        "Numero pasaporte consistente",
                        "Documento Titulo correcto y legible"         
];

function rederCheckBox(){
    const contenedor =  docuement.getElementById("contenedorValidaciones");

    if(!contenedor){
        console.log("no existe la etiqueta con id contenedorValidaciones");
        return ;
    }


    let htmlCheckBoxs="";

    validationData.forEach((contenido, index) => {
        
        htmlCheckBoxs += ` <div class="form-check">
                <input class="form-check-input" type="checkbox${index}" id="check${contenido}" name="validaciones" value="${contenido}">
                <label class="form-check-label" for="docLegible" >
                  ${contenido}
                </label>
              </div>`
    });

    contenedor.innerHTML= htmlCheckBoxs;
}