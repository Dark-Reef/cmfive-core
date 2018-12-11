<div v-cloak id="security_settings">
    <div class="small-6 columns">
        <div class="small-12 columns">
            <h3>Change Password</h3>
        </div>
        <form method="post" @submit.prevent="changePassword">
            <div class="small-12 columns">
                <label>Old Password</label>
                <input type="password" v-if="!show_passwords.old_password" v-model="user_passwords.old_password" required>
                <input type="text" v-if="show_passwords.old_password" v-model="user_passwords.old_password" required>
                <label>New Password</label>
                <input type="password" v-if="!show_passwords.new_password" v-model="user_passwords.new_password" required>
                <input type="text" v-if="show_passwords.new_password" v-model="user_passwords.new_password" required>
                <label>Confirm New Password</label>
                <input type="password" v-if="!show_passwords.confirm_new_password" v-model="user_passwords.confirm_new_password" required>
                <input type="text" v-if="show_passwords.confirm_new_password" v-model="user_passwords.confirm_new_password" required>
            </div>
            <div class="small-12 columns" style="padding-top: 1rem">
                <input type="submit" class="button small" v-model="getPasswordButtonText()" :disabled="is_loading">
            </div>
        </form>
    </div>
    <div class="small-6 columns">
        <div class="small-12 columns">
            <h3>Multi Factor Authentication</h3>
        </div>
        <div class="small-12 columns" style="padding-top: 1.25rem">
            <div class="container">
                <div class="small-4 columns container-item">
                    <label style="font-weight: bold;">Google Authenticator</label>
                </div>
                <div class="small-6 columns container-item">
                    <label style="font-weight: bold; float: right;" v-html="getMfaConfigurationLabelText()"></label>
                </div>
                <div class="small-2 columns container-item">
                    <button v-bind:class="{ 'small': true, 'alert': user.is_mfa_enabled }" style="float: right; margin: 0;" @click="editMfa()">{{ getMfaEditButtonText() }}</button>
                </div>
            </div>
        </div>
    </div>
    <modal id="confirm-mfa-modal" modal-title="Confirm MFA" style="max-width: 30rem;">
        <div class="small-6 columns" v-html="mfa_qr_code">
        </div>
        <div class="small-6 columns">
            <form method="post" @submit.prevent="confirmMfa">
                <label>MFA Code<label>
                <input type="text" v-model="mfa_code" required>
                <input type="submit" class="button small" style="margin-top: 1rem;" v-model="getConfirmMfaButtonText()" :disabled="is_loading">
            </form>
        </div>
    </modal>
    <modal id="remove-mfa-modal" modal-title="Remove MFA" style="max-width: 30rem;">
        <form method="post" @submit.prevent="removeMfa">
            <label>Password</label>
            <input type="password" v-model="user_passwords.remove_mfa_password" required>
            <input type="submit" class="button small alert" style="margin-top: 1rem;" value="Remove" :disabled="is_loading">
        </form>
    </modal>
</div>
<script>
    var security_settings = new Vue({
        el: '#security_settings',
        data: function() {
            return {
                user: <?php echo json_encode($user_array); ?>,
                user_passwords: {old_password: '', new_password: '', confirm_new_password: '', remove_mfa_password: ''},
                show_passwords: {old_password: false, new_password: false, confirm_new_password: false},
                mfa_code: null,
                mfa_qr_code: null,
                show_mfa_modal: false,
                is_loading: false,
            }
        },
        methods: {
            editMfa: function() {
                if (this.user.is_mfa_enabled) {
                    $('#remove-mfa-modal').foundation('reveal', 'open');
                } else {
                    axios.post('/auth/ajaxGetMfaQrCode', {
                        user_id: security_settings.user.id
                    }).then(function(response) {
                        security_settings.mfa_qr_code = response.data.mfa_qr_code;
                        security_settings.mfa_code = null;
                        security_settings.show_mfa_modal = true;
                        $('#confirm-mfa-modal').foundation('reveal', 'open');
                    }).catch(function(error) {
                        console.log(error);
                        new Toast('Failed to generate MFA QR Code').show();
                    });
                }
            },
            confirmMfa: function() {
                this.is_loading = true;

                axios.post('/auth/ajaxConfirmMfaCode', {
                    user_id: security_settings.user.id,
                    mfa_code: security_settings.mfa_code
                }).then(function(response) {
                    if (response.statusText == 'OK') {
                        security_settings.user.is_mfa_enabled = true;
                        $('#confirm-mfa-modal').foundation('reveal', 'close');
                        new Toast('MFA Code confirmation successful!').show();
                    }
                    security_settings.is_loading = false;
                }).catch(function(error) {
                    console.log(error);
                    new Toast('Failed to confirm MFA Code').show();
                    security_settings.is_loading = false;
                });
            },
            removeMfa: function() {
                axios.post('/auth/ajaxRemoveMfa', {
                        user_id: security_settings.user.id
                    }).then(function(response) {
                        if (response.statusText == 'OK') {
                            security_settings.user.is_mfa_enabled = false;
                            security_settings.user_passwords.remove_mfa_password = null;
                            $('#remove-mfa-modal').foundation('reveal', 'close');
                            new Toast('MFA successfully removed').show();
                        }
                        security_settings.is_loading = false;
                    }).catch(function(error) {
                        console.log(error);
                        new Toast('Failed to remove MFA').show();
                        security_settings.is_loading = false;
                    });
            },
            changePassword: function() {
                this.is_loading = true;

                if (this.user_passwords.new_password != this.user_passwords.confirm_new_password) {
                    new Toast('Passwords do not match').show();
                    return;
                }

                if (this.user_passwords.new_password.length == 0) {
                    new Toast('Password cannot be empty').show();
                    return;
                }

                axios.post('/auth/ajaxChangePassword', {
                    user_id: security_settings.user.id,
                    user_login: security_settings.user.login,
                    old_password: security_settings.user_passwords.old_password,
                    new_password: security_settings.user_passwords.new_password,
                    confirm_new_password: security_settings.user_passwords.confirm_new_password,
                }).then(function(response) {
                    security_settings.user_passwords.old_password = '';
                    security_settings.user_passwords.new_password = '';
                    security_settings.user_passwords.confirm_new_password = '';
                    security_settings.show_passwords.old_password = false;
                    security_settings.show_passwords.new_password = false;
                    security_settings.show_passwords.confirm_new_password = false;
                    new Toast('Successfully changed password!').show();
                    security_settings.is_loading = false;
                }).catch(function(error) {
                    new Toast(error.response.data).show();
                    security_settings.is_loading = false;
                });
            },
            getPasswordButtonText: function() {
                return this.is_loading ? 'Changing' : 'Change Password';
            },
            getConfirmMfaButtonText: function() {
                return this.is_loading ? 'Confirming' : 'Confirm';
            },
            getMfaConfigurationLabelText: function() {
                return this.user.is_mfa_enabled ? "Configured" : "Not Configured";
            },
            getMfaEditButtonText: function() {
                return this.user.is_mfa_enabled ? "Remove" : "Add";
            }
        }
    })
</script>
<style>
    svg {
        max-height: 1.5rem;
        max-width: 1.5rem;
    }
    .container {
        align-items: center;
        background: #f2f2f2;
        display: flex;
        justify-content: center;
        padding: 0.25rem;
    }
    .container-item {
        max-width: 50%;
    }
</style>