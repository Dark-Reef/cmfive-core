<?php 
use Html\Form\InputField;

echo $form;
echo CmfiveScriptComponentRegister::getComponent("Axios")->_include();
echo CmfiveScriptComponentRegister::getComponent("ToastJS")->_include();
?>

<script>
    new Vue({
        el: "#mfa_qr_code",

        data: function () {
            return {
                mfa_enabled: <?php echo $is_mfa_enabled; ?>,
                setting_up_mfa: false
            }
        },
        methods: {
            enableMfa: function() {
                var mfa_qr_code = document.getElementById("mfa_qr_code");
                axios.get('/auth/ajaxGetMfaQrCode').then(function(response) {
                        mfa_qr_code.innerHTML = response.data.mfa_qr_code + ''
                        setting_up_mfa = true;
                    }).catch(function(error) {
                        mfa_qr_code.innerHTML = "<p>Failed to generate MFA QR Code<p>"
                    });
            }
        },
        computed: {
            isMfaEnabledToString: function() {
                debugger;
                if (this.setting_up_mfa) {
                    return "Cancel";
                }
                return this.mfa_enabled == 0 ? "Enable" : "Disable"
            }
        }
    });
</script>


<form action="" method="post" class="small-12 columns">
    <div class="row-fluid clearfix small-12 multicolform">
        <div class="panel clearfix">
            <label class="small-12 columns">Item Name
                <?php echo (new \Html\Form\InputField\Text([
                    'id|name' => 'mfa_code',
                    'required' => 'true'
                ])); ?>
            </label>
        </div>
    </div>
</form>