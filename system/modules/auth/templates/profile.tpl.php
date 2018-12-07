<?php 
use Html\Form\InputField;

echo $form;
echo CmfiveScriptComponentRegister::getComponent("Axios")->_include();
echo CmfiveScriptComponentRegister::getComponent("ToastJS")->_include();

$var = "<form action='auth/confirmMfa' method='post' class='small-12 columns'><div class='row-fluid clearfix small-12 multicolform'><div><label class='small-12 columns'>MFA Code";
$var .= (new \Html\Form\InputField\Number(['id|name' => 'mfa_code', 'required' => 'true'])) . "</label></div></div></form><br><label class='small-12 columns'>" . (new \Html\Form\InputField\Submit(['value' => 'Submit', 'class' => 'button small'])) . "</label>";
?>

<script>
    var app = new Vue({
        el: "#mfa_qr_code",

        data: function () {
            return {
                mfa_enabled: <?php echo $is_mfa_enabled; ?>,
                mfa_qr_code_form: "<?php echo $var; ?>",
                setting_up_mfa: false
            }
        },
        methods: {
            enableMfa: function() {
                var mfa_qr_code = document.getElementById("mfa_qr_code");
                axios.get('/auth/ajaxGetMfaQrCode').then(function(response) {
                        mfa_qr_code.innerHTML = response.data.mfa_qr_code + app.mfa_qr_code_form;
                        app.setting_up_mfa = true;
                    }).catch(function(error) {
                        mfa_qr_code.innerHTML = "<p>Failed to generate MFA QR Code<p>"
                        console.log(error);
                    });
            }
        },
        computed: {
            isMfaEnabledToString: function() {
                if (this.setting_up_mfa) {
                    return "Cancel";
                }
                return this.mfa_enabled == 0 ? "Enable" : "Disable"
            }
        }
    });
</script>