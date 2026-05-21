const loginForm = document.getElementById("loginForm");
const registerForm = document.getElementById("registerForm");

const tabLogin = document.getElementById("tabLogin");
const tabRegister = document.getElementById("tabRegister");


function toggle(){
    loginForm.classList.toggle("active");
    registerForm.classList.toggle("active");

    tabLogin.classList.toggle("active");
    tabRegister.classList.toggle("active");
}

tabLogin.onclick = () => {
    loginForm.classList.add("active");
    registerForm.classList.remove("active");

    tabLogin.classList.add("active");
    tabRegister.classList.remove("active");
};

tabRegister.onclick = () => {
    registerForm.classList.add("active");
    loginForm.classList.remove("active");

    tabRegister.classList.add("active");
    tabLogin.classList.remove("active");
};

const rolSelect = document.getElementById("rolSelect");
const areaSelect = document.getElementById("areaSelect");
const areaGroup = document.getElementById("areaGroup");

/* FUNCION PRINCIPAL */
function actualizarArea() {

    if(rolSelect.value === "ADMIN"){

        areaSelect.disabled = true;
        areaSelect.value = "";
        areaSelect.removeAttribute("required");

        areaGroup.style.opacity = "0.5"; // visual

    }else{

        areaSelect.disabled = false;
        areaSelect.setAttribute("required", "true");

        areaGroup.style.opacity = "1";
    }
}

/* 🔥 CUANDO CAMBIA */
rolSelect.addEventListener("change", actualizarArea);

/* 🔥 CUANDO CARGA LA PÁGINA */
document.addEventListener("DOMContentLoaded", actualizarArea);


/* MOSTRAR CONTRASEÑA */

function togglePassword(id, icon){
    const input = document.getElementById(id);

    if(input.type === "password"){
        input.type = "text";
        icon.classList.replace("bx-show", "bx-hide");
    } else {
        input.type = "password";
        icon.classList.replace("bx-hide", "bx-show");
    }
}

