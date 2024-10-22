const vgLoader=document.querySelector('.container-loader-full');

window.onload = function() {
    document.getElementById('MenuOrdenes').classList.add('menu-activo','fw-bold');
    vgLoader.classList.add('loader-full-hidden');    
};

async function FnModificarSolicitudCliente(){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        const formData = new FormData();
        formData.append('id', document.getElementById('txtId').value);
        formData.append('clidireccion', document.getElementById('txtCliDireccion').value);
        formData.append('clicontacto', document.getElementById('txtCliContacto').value);
        formData.append('clitelefono', document.getElementById('txtCliTelefono').value);
        formData.append('clicorreo', document.getElementById('txtCliCorreo').value);

        const response = await fetch('/solicitudes/update/ModificarSolicitudCliente.php', {
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