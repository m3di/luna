export default {
    call(component, resource, action, models) {
        component.si({
            url: component.route('luna.resources.action', [resource, action]),
            method: 'post',
            data: {
                models: models,
            },
        }).then(x => {
            this.onSuccess(component, x)
        });
    },
    onSuccess(component, data) {
        if (data.action == 'message') {
            component.$swal({
                title: data.message.title,
                text: data.message.text,
                type: data.message.type,
                confirmButtonText: 'خب!',
                target: document.getElementById('app')
            })
        } else if (data.action == 'redirect') {
            if (data.redirect.new_tab) {
                let win = window.open(data.redirect.url, '_blank');
                win.focus();
            } else {
                document.location = data.redirect.url
            }
        } else if (data.action == 'download') {
            let blob = this.base64toBlob(data.download.content, data.download.mime);

            if (window.navigator.msSaveOrOpenBlob) {
                window.navigator.msSaveBlob(blob, data.download.filename);
            } else {
                let elem = window.document.createElement('a');
                elem.href = window.URL.createObjectURL(blob);
                elem.download = data.download.filename;
                document.body.appendChild(elem);
                elem.click();
                document.body.removeChild(elem);
            }
        } else if (data.action == 'refresh') {
            window.location.reload()
        }
    },
    base64toBlob(base64Data, contentType) {
        contentType = contentType || '';
        let sliceSize = 1024;
        let byteCharacters = atob(base64Data);
        let bytesLength = byteCharacters.length;
        let slicesCount = Math.ceil(bytesLength / sliceSize);
        let byteArrays = new Array(slicesCount);

        for (let sliceIndex = 0; sliceIndex < slicesCount; ++sliceIndex) {
            let begin = sliceIndex * sliceSize;
            let end = Math.min(begin + sliceSize, bytesLength);

            let bytes = new Array(end - begin);
            for (let offset = begin, i = 0; offset < end; ++i, ++offset) {
                bytes[i] = byteCharacters[offset].charCodeAt(0);
            }
            byteArrays[sliceIndex] = new Uint8Array(bytes);
        }
        return new Blob(byteArrays, {type: contentType});
    }
}