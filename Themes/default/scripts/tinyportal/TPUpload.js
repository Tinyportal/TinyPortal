function tpUploadHasAllowedExtension(filename, accept) {
    const dot = filename.lastIndexOf('.');
    return dot !== -1 && accept.split(',').includes(filename.slice(dot).toLowerCase());
}

const tpUploadInputSelector = 'input[type="file"][data-tp-upload-type-error]';

document.addEventListener('change', (event) => {
    const input = event.target;
    if (!input.matches(tpUploadInputSelector)) {
        return;
    }

    const file = input.files[0];
    const invalid = file && !tpUploadHasAllowedExtension(file.name, input.accept);
    input.setCustomValidity(invalid ? input.dataset.tpUploadTypeError : '');
    if (invalid) {
        input.reportValidity();
    } else if (file && input.hasAttribute('data-tp-upload-auto-submit')) {
        const invalidUpload = input.form.querySelector(tpUploadInputSelector + ':invalid');
        if (invalidUpload) {
            invalidUpload.reportValidity();
        } else {
            input.form.submit();
        }
    }
});

document.addEventListener('reset', (event) => {
    event.target.querySelectorAll(tpUploadInputSelector).forEach((input) => input.setCustomValidity(''));
});
