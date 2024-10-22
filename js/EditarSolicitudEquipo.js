const vgLoader=document.querySelector('.container-loader-full');

window.onload = function() {
    document.getElementById('MenuOrdenes').classList.add('menu-activo','fw-bold');
    vgLoader.classList.add('loader-full-hidden');    
};

async function FnModificarSolicitudEquipo(){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        const formData = new FormData();
        formData.append('id', document.getElementById('txtId').value);
        formData.append('equnombre', document.getElementById('txtEquNombre').value);
        formData.append('equmarca', document.getElementById('txtEquMarca').value);
        formData.append('equmodelo', document.getElementById('txtEquModelo').value);
        formData.append('equplaca', document.getElementById('txtEquPlaca').value);
        formData.append('equserie', document.getElementById('txtEquSerie').value);
        formData.append('equmotor', document.getElementById('txtEquMotor').value);
        formData.append('equdiferencial', document.getElementById('txtEquDiferencial').value);
        formData.append('equtransmision', document.getElementById('txtEquTransmision').value);
        formData.append('equkm', document.getElementById('txtEquKm').value);
        formData.append('equhm', document.getElementById('txtEquHm').value);

        const response = await fetch('/solicitudes/update/ModificarSolicitudEquipo.php', {
            method:'POST',
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`);}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);} 
        
        setTimeout(function(){location.reload();},500);

    } catch (ex) {
        showToast(ex.message, 'bg-danger');
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},1000);
    }
}

function FnSolicitud(){
    id = document.getElementById('txtId').value;
    if(id > 0){
        window.location.href='/solicitudes/Solicitud.php?id='+id;
    }
    return false;
}

function FnListarSolicitudes(){
    window.location.href='/solicitudes/Solicitudes.php';
    return false;
}