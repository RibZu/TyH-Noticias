document.addEventListener('DOMContentLoaded', function() {
    const tituloInput = document.querySelector('input[name="titulo"]');
    if(tituloInput) {
        tituloInput.addEventListener('blur', function() {
            if(this.value.length < 10) {
                mostrarError(this, 'El título debe tener al menos 10 caracteres');
            } else if(this.value.length > 100) {
                mostrarError(this, 'El título no puede exceder 100 caracteres');
            } else {
                limpiarError(this);
            }
        });
    }

    const descripcionTextarea = document.querySelector('textarea[name="descripcion"]');
    if(descripcionTextarea) {
        descripcionTextarea.addEventListener('blur', function() {
            if(this.value.length < 50) {
                mostrarError(this, 'La descripción debe tener al menos 50 caracteres');
            } else {
                limpiarError(this);
            }
        });
    }

    const imagenInput = document.querySelector('input[name="imagen"]');
    if(imagenInput) {
        imagenInput.addEventListener('change', function() {
            const file = this.files[0];
            if(file) {
                const extension = file.name.split('.').pop().toLowerCase();
                if(extension !== 'jpg' && extension !== 'png') {
                    mostrarError(this, 'Solo se permiten archivos JPG o PNG');
                    this.value = '';
                } else if(file.size > 2 * 1024 * 1024) {
                    mostrarError(this, 'El archivo no puede exceder 2MB');
                    this.value = '';
                } else {
                    limpiarError(this);
                }
            }
        });
    }
});

function mostrarError(elemento, mensaje) {
    elemento.classList.add('is-invalid');
    let errorDiv = elemento.parentElement.querySelector('.invalid-feedback');
    if(!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        elemento.parentElement.appendChild(errorDiv);
    }
    errorDiv.textContent = mensaje;
}

function limpiarError(elemento) {
    elemento.classList.remove('is-invalid');
    const errorDiv = elemento.parentElement.querySelector('.invalid-feedback');
    if(errorDiv) {
        errorDiv.remove();
    }
}
