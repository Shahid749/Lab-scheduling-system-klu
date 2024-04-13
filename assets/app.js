function showError(msg, title = 'Error!', oc = true) {
    Swal.fire({
        title: title,
        text: msg,
        icon: 'error',
        allowOutsideClick: oc
    });
}

function openPDF(msg, PDFLINK) {
    Swal.fire({
        itle: 'Success!',
        text: msg,
        icon: 'success',
        confirmButtonText: `OPEN / DOWNLOAD PDF <i class="far fa-arrow-right"></i>`,
        preConfirm: () => window.open(PDFLINK)
    });
}

function generatePDF(msg, uploadId) {
    Swal.fire({
        title: 'Success!',
        text: msg,
        icon: 'success',
        showCancelButton: true,
        confirmButtonText: `GENERATE PDF <i class="fas fa-robot"></i>`,
        showLoaderOnConfirm: true,
        preConfirm: async () => {
            try {
                const response = await fetch(`api/generate_pdf?id=${uploadId}`);
                if (!response.ok) {
                    return Swal.showValidationMessage(`${JSON.stringify(await response.json())}`);
                }
                return response.json();
            } catch (error) {
                Swal.showValidationMessage(`Request failed: ${error}`);
            }
        },
        allowOutsideClick: false
    }).then((res) => {
        console.log(res.value);
        let status = res.value.status;
        if (!status) {
            showError(res.value.errorMsg, 'Unable To Generate!', false);
            return;
        }
        PDFLINK = res.value.data.pdfLink;
        openPDF("PDF Generated Successfully!", PDFLINK)
    });

}