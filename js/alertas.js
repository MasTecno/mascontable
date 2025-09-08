
function mostrarMensaje(mensaje, estado) {
    const divAlertas = document.getElementById("divAlertas");

    const divMensaje = document.createElement("div");
    divMensaje.className = "border-l-4 p-2.5 mb-6 hidden";
    divAlertas.appendChild(divMensaje);

    const divFlex = document.createElement("div");
    divFlex.className = "flex items-center";
    divMensaje.appendChild(divFlex);

    const divIcono = document.createElement("div");
    divIcono.className = "flex-shrink-0";
    divFlex.appendChild(divIcono);

    const divTexto = document.createElement("div");
    divTexto.className = "ml-3";
    divFlex.appendChild(divTexto);

    const textoMensaje = document.createElement("p");
    textoMensaje.className = "text-sm font-semibold";
    divTexto.appendChild(textoMensaje);

    divMensaje.classList.remove("hidden");
    textoMensaje.textContent = mensaje;

    const iconos = {
        "success": "<i class='fa-solid fa-check-circle text-lg'></i>",
        "error": "<i class='fa-solid fa-exclamation-triangle text-lg'></i>",
        "warning": "<i class='fa-solid fa-exclamation-circle text-lg'></i>",
        "info": "<i class='fa-solid fa-info-circle text-lg'></i>"
    }

    switch(estado) {
        case "success":
            divIcono.innerHTML = iconos.success;
            divIcono.classList.add("text-green-500");
            divMensaje.classList.add("bg-green-50", "border-green-500");
            textoMensaje.classList.add("text-green-500");
        break;

        case "error":
            divIcono.innerHTML = iconos.error;
            divIcono.classList.add("text-red-500");
            divMensaje.classList.add("bg-red-50", "border-red-500");
            textoMensaje.classList.add("text-red-500");
        break;
        
        case "warning":
            divIcono.innerHTML = iconos.warning;
            divIcono.classList.add("text-yellow-500");
            divMensaje.classList.add("bg-yellow-50", "border-yellow-500");
            textoMensaje.classList.add("text-yellow-500");
        break;

        case "info":
            divIcono.innerHTML = iconos.info;
            divIcono.classList.add("text-sky-500");
            divMensaje.classList.add("bg-sky-50", "border-sky-500");
            textoMensaje.classList.add("text-sky-500");
        break;
    }

    setTimeout(() => {
        divMensaje.remove();
    }, 2500);
}