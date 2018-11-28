<?php echo $form; ?>

<script>
    new Vue({
        el: "#mfa_qr_code",

        data: function () {
            return {
                mfa_enabled: "<?php echo $mfa_enabled; ?>"
            }
        },
        watch: {
            mfa_enabled: function(v) {
                var mfa_qr_code = document.getElementById("mfa_qr_code");
                
                if (v) {
                    mfa_qr_code.innerHTML = "<loading-indicator :show='true'></loading-indicator>";
                    axios.get('/auth/getMfaQrCode')
                    .then(function(result) {
                        mfa_qr_code.innerHTML = data;
                    })
                    .catch(function(error) {
                        new Toast("Failed to retrieve QR Code").show();
                    });
                    // $.get("/auth/gettwofactorbarcode", function(data, status){
                    //     mfa_qr_code.innerHTML = data;
                    // });
                }

                if (!v) {
                    mfa_qr_code.innerHTML = "";
                }
            }
        }
    });
</script>