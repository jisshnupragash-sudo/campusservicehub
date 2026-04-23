(() => {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');

    Array.from(forms).forEach((form) => {
        form.addEventListener('submit', (event) => {
            const imageInput = form.querySelector('.image-input');
            if (imageInput && imageInput.files.length > 0) {
                const file = imageInput.files[0];
                const validTypes = ['image/jpeg', 'image/png'];
                const maxSize = 2 * 1024 * 1024;

                if (!validTypes.includes(file.type)) {
                    event.preventDefault();
                    event.stopPropagation();
                    alert('Only JPG and PNG files are allowed.');
                    return;
                }

                if (file.size > maxSize) {
                    event.preventDefault();
                    event.stopPropagation();
                    alert('Image must be less than 2MB.');
                    return;
                }
            }

            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
