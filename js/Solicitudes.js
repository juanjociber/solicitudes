var Nombre = '';
var Equipo = 0;
var FechaInicial = '';
var FechaFinal = '';
var PaginasTotal = 0;
var PaginaActual = 0;

const loader = document.querySelector('.container-loader-full');

window.addEventListener('load', function() {
    document.getElementById('MenuSolicitudes').classList.add('menu-activo','fw-bold');
    const datos = sessionStorage.getItem('gpem_solicitudes');
    if (datos){FnMostrarRegistros(JSON.parse(datos));}
    loader.classList.add('loader-full-hidden');
});

$(document).ready(function() {
    $('#cbEquipo').select2({
        width: 'resolve', //Personalizar el alto del select, aplicar estilo.
        ajax: {
            delay:450,
            url:'/gesman/search/ListarEquipos.php',
            type:'POST',
            dataType:'json',
            data:function(params){
                return {
                    codigo:params.term
                };
            },
            processResults:function(datos){
                return {
                    results:datos.data.map(function(elem) {
                        return {
                            id:elem.id,
                            text:elem.codigo,
                        };
                    })
                }
            },
            cache: true
        },
        placeholder: 'Seleccionar',
        allowClear: true, // Permite borrar la selección
        minimumInputLength:1 //Caracteres minimos para buscar
    });
});

$(document).ready(function() {
    $('#cbEquipo2').select2({
        dropdownParent: $('#modalAgregarSolicitud'),
        width: 'resolve', //Personalizar el alto del select, aplicar estilo.
        ajax: {
            delay: 450, //Tiempo de demora para buscar
            url: '/gesman/search/ListarEquipos.php',
            type: 'POST',
            dataType: 'json',
            data:function(params){
                return {
                    codigo: params.term // parametros a enviar al server. params.term captura lo que se escribe en el input
                };
            },
            processResults:function(datos){
                return {
                    results: datos.data.map(function(elem) {
                        return {
                            id: elem.id,
                            text: elem.codigo,
                        };
                    })
                }
            },
            cache: true
        },
        placeholder: 'Seleccionar'
    });
});

function FnModalAgregarSolicitud(){
    const modalAgregarSolicitud=new bootstrap.Modal(document.getElementById('modalAgregarSolicitud'), {
        keyboard: false
    }).show();
    return false;
}

async function FnAgregarSolicitud(){
    loader.classList.remove('loader-full-hidden');
    try {    
        const formData = new FormData();
        formData.append('equid', document.getElementById('cbEquipo2').value);
        formData.append('equkm', document.getElementById('txtKm').value);
        formData.append('equhm', document.getElementById('txtHm').value);
        formData.append('actividades', document.getElementById('txtActividad').value);
        
        const response = await fetch("/solicitudes/insert/AgregarSolicitud.php", {
            method: "POST",
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`)}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);}

        setTimeout(()=>{window.location.href='/solicitudes/EditarSolicitud.php?id='+datos.id;},1000);
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
        setTimeout(()=>{loader.classList.add('loader-full-hidden');},500);
    }
}

async function FnBuscarSolicitudes(){
    loader.classList.remove('loader-full-hidden');
    try {
        Nombre = document.getElementById('txtNombre').value;
        Equipo = document.getElementById('cbEquipo').value;
        FechaInicial = document.getElementById('dtpFechaInicial').value;
        FechaFinal = document.getElementById('dtpFechaFinal').value;
        PaginasTotal = 0
        PaginaActual = 0
        await FnBuscarSolicitudes2();
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
        document.getElementById('tblSolicitudes').innerHTML=`<div class="col-12"><div class="fst-italic text-danger p-2">${ex.message}</div></div>`;
        document.getElementById("btnSiguiente").classList.add('d-none');
        document.getElementById("btnPrimero").classList.add('d-none');
        sessionStorage.removeItem('gpem_solicitudes');
    } finally {
        setTimeout(function(){loader.classList.add('loader-full-hidden'); }, 500);
    }
}

async function FnBuscarSolicitudes2(){
    try {
        const formData = new FormData();
        formData.append('nombre', Nombre);
        formData.append('equipo', Equipo);
        formData.append('fechainicial', FechaInicial);
        formData.append('fechafinal', FechaFinal);
        formData.append('pagina', PaginasTotal);

        const response = await fetch('/solicitudes/search/BuscarSolicitudes.php', {
            method:'POST',
            body: formData
        });/*.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));*/

        if (!response.ok) { throw new Error(`${response.status} ${response.statusText}`);}
        const datos = await response.json();
        if (!datos.res) { throw new Error(`${datos.msg}`); }

        sessionStorage.setItem('gpem_solicitudes', JSON.stringify(datos));
        FnMostrarRegistros(datos);        
    } catch (ex) {
        throw ex;
    }
}

function FnMostrarRegistros(datos){
    document.getElementById('tblSolicitudes').innerHTML = '';
    let estado = '';
    datos.data.forEach(solicitud => {
        switch (solicitud.estado){
            case 1:
                estado='<span class="badge bg-danger">Anulado</span>';
            break;
            case 2:
                estado='<span class="badge bg-primary">Abierto</span>';
            break;
            case 3:
                estado='<span class="badge bg-success">Cerrado</span>';
            break;
            default:
                estado='<span class="badge bg-light text-dark">Unknown</span>';
        }

        document.getElementById('tblSolicitudes').innerHTML +=`
        <div class="col-12">
            <div class="divselect border-bottom border-secondary mb-1 px-1" onclick="FnSolicitud(${solicitud.id}); return false;">
                <div class="div d-flex justify-content-between">
                    <p class="m-0"><span class="fw-bold">${solicitud.nombre}</span> <span class="text-secondary" style="font-size: 13px;">${solicitud.fecha}</span></p><p class="m-0">${estado}</p>
                </div>
                <div class="div">${solicitud.equcodigo} ${solicitud.actividades}</div>
            </div>
        </div>`;
    });
    FnPaginacion(datos.pag);
}


function FnPaginacion(cantidad) {
    try {
        PaginaActual += 1;
        if (cantidad == 15) {
            PaginasTotal += 15;
            document.getElementById("btnSiguiente").classList.remove('d-none');
        } else {
            document.getElementById("btnSiguiente").classList.add('d-none');
        }

        if (PaginaActual > 1) {
            document.getElementById("btnPrimero").classList.remove('d-none');
        } else {
            document.getElementById("btnPrimero").classList.add('d-none');
        }
    } catch (ex) {
        throw ex;
    }
}

async function FnBuscarSiguiente() {
    loader.classList.remove('loader-full-hidden');
    try {
        await FnBuscarSolicitudes2();
    } catch (ex) {
        document.getElementById("btnSiguiente").classList.add('d-none');
        showToast(ex.message, 'bg-danger');
    } finally {
        setTimeout(function(){loader.classList.add('loader-full-hidden');},500);
    }
}

async function FnBuscarPrimero() {
    loader.classList.remove('loader-full-hidden');
    try {
        PaginasTotal = 0
        PaginaActual = 0
        await FnBuscarSolicitudes2()
    } catch (ex) {
        document.getElementById("btnPrimero").classList.add('d-none');
        showToast(ex.message, 'bg-danger');
    } finally {
        setTimeout(function(){loader.classList.add('loader-full-hidden'); }, 500);
    }
}

function FnSolicitud(id){
    if(id > 0){
        window.location.href='/solicitudes/Solicitud.php?id='+id;
    }
    return false;
}