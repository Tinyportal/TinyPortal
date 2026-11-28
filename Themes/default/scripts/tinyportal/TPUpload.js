function tpUploadHasAllowedExtension(filename, accept) {
    const dot = filename.lastIndexOf('.');
    return dot !== -1 && accept.split(',').includes(filename.slice(dot).toLowerCase());
}

const tpUploadInputSelector = 'input[type="file"][data-tp-upload-type-error]';

async function tpUploadQuicklistImage(input, file) {
    const form = input.form;
    const data = new FormData();
    data.append('image', file);
    data.append(input.dataset.tpSessionVar, input.dataset.tpSessionId);

    input.disabled = true;
    try {
        const response = await fetch(input.dataset.tpQuicklistUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: data,
        });
        if (!response.ok) {
            throw new Error('Image upload failed');
        }

        const result = await response.json();
        if ((result.success !== true && result.success !== 'true') || typeof result.data !== 'string' || !result.data) {
            throw new Error('Invalid image upload response');
        }

        const imageUrl = new URL(result.data, document.baseURI);
        const filename = imageUrl.pathname.split('/').pop();
        for (const list of form.querySelectorAll('.tp_quicklist .tp_thumb')) {
            if (Array.from(list.querySelectorAll('img')).some((image) => image.src === imageUrl.href)) {
                continue;
            }

            const image = document.createElement('img');
            image.src = imageUrl.href;
            image.alt = filename;
            image.title = filename;
            list.append(image);
        }
    } catch {
        alert(input.dataset.tpUploadFailed);
    } finally {
        input.value = '';
        input.disabled = false;
    }
}

document.addEventListener('change', async (event) => {
    const input = event.target;
    if (!input.matches(tpUploadInputSelector)) {
        return;
    }

    const file = input.files[0];
    const invalid = file && !tpUploadHasAllowedExtension(file.name, input.accept);
    input.setCustomValidity(invalid ? input.dataset.tpUploadTypeError : '');
    if (invalid) {
        input.reportValidity();
    } else if (file && input.dataset.tpQuicklistUrl) {
        await tpUploadQuicklistImage(input, file);
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
