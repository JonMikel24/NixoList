document.getElementById('form').addEventListener('submit', function (e) {
    let hayError = false;

    // Obtener elementos
    const usuario = document.getElementById('usuario');
    const email = document.getElementById('email');
    const contra = document.getElementById('contrasena');

    const errorUsu = document.getElementById('error-usu');
    const errorEmail = document.getElementById('error-email');
    const errorContra = document.getElementById('error-contra');

    // Limpiar errores previos
    [errorUsu, errorEmail, errorContra].forEach(el => el.textContent = '');
    [usuario, email, contra].forEach(el => el.classList.remove('is-invalid'));

    // 1. Validación Usuario (mín 3 caracteres, sin caracteres especiales)
    const regexUsuario = /^[a-zA-Z0-9]+$/;
    if (usuario.value.length < 3) {
        errorUsu.textContent = 'El usuario debe tener al menos 3 caracteres.';
        usuario.classList.add('is-invalid');
        hayError = true;
    } else if (!regexUsuario.test(usuario.value)) {
        errorUsu.textContent = 'El usuario no permite espacios ni caracteres especiales.';
        usuario.classList.add('is-invalid');
        hayError = true;
    }

    // 2. Validación Email (Formato estándar)
    const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!regexEmail.test(email.value)) {
        errorEmail.textContent = 'Ingresa un correo electrónico válido.';
        email.classList.add('is-invalid');
        hayError = true;
    }

    // 3. Validación Contraseña (mín 6, una mayúscula y un número)
    const tieneMayuscula = /[A-Z]/.test(contra.value);
    const tieneNumero = /[0-9]/.test(contra.value);

    if (contra.value.length < 6) {
        errorContra.textContent = 'La contraseña debe tener al menos 6 caracteres.';
        contra.classList.add('is-invalid');
        hayError = true;
    } else if (!tieneMayuscula || !tieneNumero) {
        errorContra.textContent = 'La contraseña debe incluir una mayúscula y un número.';
        contra.classList.add('is-invalid');
        hayError = true;
    }

    // Si hay algún error, no enviamos el formulario
    if (hayError) {
        e.preventDefault();
    }
});