const vgLoader=document.querySelector('.container-loader-full');

window.onload = function() {
    document.getElementById('MenuSolicitudes').classList.add('menu-activo','fw-bold');
    vgLoader.classList.add('loader-full-hidden');
};

function FnModalFinalizarSolicitud(){
    const modalFinalizarSolicitud=new bootstrap.Modal(document.getElementById('modalFinalizarSolicitud'), {
        keyboard: false
    }).show();
};

async function FnFinalizarSolicitud(){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        const formData = new FormData();
        formData.append('id', document.getElementById('txtId').value);
        const response = await fetch('/solicitudes/update/FinalizarSolicitud.php', {
            method:'POST',
            body: formData
        });

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`);}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);} 

        setTimeout(function(){location.reload();},500);

    } catch (ex) {
        showToast(ex.message, 'bg-danger');
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},1000);
    }
}

function FnEditarSolicitud(){
    let id = document.getElementById('txtId').value;
    if(id > 0){
        window.location.href='/solicitudes/EditarSolicitud.php?id='+id;
    }
    return false;
}

function FnModalAgregarCheckList(){
    const modalAgregarCheckList=new bootstrap.Modal(document.getElementById('modalAgregarCheckList'), {
        keyboard: false
    }).show();
};

async function FnAgregarCheckList(){
    vgLoader.classList.remove('loader-full-hidden');
    try {        
        const formData = new FormData();
        formData.append('solid', document.getElementById('txtId').value);
        formData.append('plaid', document.getElementById('cbPlantilla').value);
        formData.append('fecha', document.getElementById('dtpFecha').value);
        const response = await fetch("/checklists/insert/AgregarCheckList.php", {
            method: "POST",
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`)}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);}
        setTimeout(()=>{window.location.href='/checklists/CheckList.php?id='+datos.id;},1000);
    } catch (ex) {
        setTimeout(()=>{vgLoader.classList.add('loader-full-hidden');},500);
        showToast(ex.message, 'bg-danger');
    }
}

function FnModalAgregarOrden(){
    const modalAgregarOrden=new bootstrap.Modal(document.getElementById('modalAgregarOrden'), {
        keyboard: false
    }).show();
};

async function FnAgregarOrden(){
    vgLoader.classList.remove('loader-full-hidden');
    try {        
        const formData = new FormData();
        formData.append('solid', document.getElementById('txtId').value);
        formData.append('tipid', document.getElementById('cbOrdTipo').value);
        formData.append('fecha', document.getElementById('dtpOrdFecha').value);
        formData.append('tipnombre', document.getElementById("cbOrdTipo").options[document.getElementById("cbOrdTipo").selectedIndex].text);
        formData.append('nombre', document.getElementById('txtOrdNombre').value);
        formData.append('actnombre', document.getElementById('txtOrdActividad').value);
        const response = await fetch("/gesman/insert/AgregarSolicitudOrden.php",{
            method: "POST",
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`)}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);}
        setTimeout(()=>{window.location.href='/gesman/EditarOrden.php?id='+datos.id;},1000);
    } catch (ex) {
        setTimeout(()=>{vgLoader.classList.add('loader-full-hidden');},500);
        showToast(ex.message, 'bg-danger');
    }
}

function FnSolicitudes(){
    window.location.href='/solicitudes/Solicitudes.php';
    return false;
}