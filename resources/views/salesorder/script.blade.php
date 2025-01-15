<script src="{{ asset('/js/jquery.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('/js/html2pdf.bundle.min.js') }}"></script>
<script>
    function closeScript() {
        setTimeout(function () {
            window.open(window.location, '_self').close();
        }, 1000);
    }

    $(window).on('load', function () {
        var element = document.getElementById('boxes');
        var opt = {
            filename: '{{$user->salesorderNumberFormat($salesorder->salesorder_id)}}',
            image: {type: 'jpeg', quality: 1},
            html2canvas: {scale: 4, dpi: 72, letterRendering: true},
            jsPDF: {unit: 'in', format: 'A4'}
        };
        html2pdf().set(opt).from(element).save().then(closeScript);
    });

 // Qr code
 document.addEventListener('DOMContentLoaded', function () {
    const qrToggle = document.getElementById('salesorder_qr_enabled');

    qrToggle.addEventListener('change', function () {
        const isChecked = this.checked ? 'on' : 'off';

        // Send AJAX request to update setting
        fetch('/update-qrcode-setting', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                salesorder_qr_enabled: isChecked
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            // Optional: Handle success response
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
            // Optional: Handle error
        });
    });
});


</script>
