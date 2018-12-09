<div v-cloak id="security_settings">
    <div class="small-6 columns">
        <h3>Change Password</h3>
    </div>
    <div class="small-6 columns">
        <h3>Multi Factor Authentication</h3>
    </div>
    <form method="post" @submit.prevent="changePassword">
        <div class="small-6 columns">
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
        <div class="small-6 columns" style="padding-top: 1.25rem">
            <div class="container">
                <div class="small-4 columns container-item">
                    <label style="font-weight: bold;">Google Authenticator</label>
                </div>
                <div class="small-6 columns container-item">
                    <label style="font-weight: bold; float: right;">Not Configured</label>
                </div>
                <div class="small-2 columns container-item">
                    <button class="small" style="float: right; margin: 0;" @click="editMfa()">Add</button>
                </div>
            </div>
        </div>
        <div class="small-12 columns" style="padding-top: 1rem">
            <input type="submit" class="button small" v-model="changePasswordButtonValue()" :disabled="is_loading">
        </div>
    </form>
</div>
<script>
    var security_settings = new Vue({
        el: '#security_settings',
        data: function() {
            return {
                user: <?php echo json_encode($user_array); ?>,
                user_passwords: {old_password: '', new_password: '', confirm_new_password: ''},
                show_passwords: {old_password: false, new_password: false, confirm_new_password: false},
                show_mfa_modal: false,
                mfa_qr_code: null,
                is_loading: false,
            }
        },
        methods: {
            editMfa: function() {
                if (this.user.is_mfa_enabled) {
                    axios.post('/auth/removeMfa', {
                        user_id: security_settings.user.id
                    }).then(function(response) {
                        security_settings.user.is_mfa_enabled = false;
                    }).catch(function(error) {
                        new Toast("Failed to remove MFA").show();
                    });
                } else {
                    axios.post('/auth/ajaxGetMfaQrCode', {
                        user_id: security_settings.user.id
                    }).then(function(response) {
                        security_settings.mfa_qr_code = response.data.mfa_qr_code;
                        security_settings.show_mfa_modal = true;
                    }).catch(function(error) {
                        new Toast("Failed to generate MFA QR Code").show();
                    });
                }
            },
            changePassword: function() {
                this.is_loading = true;

                if (this.user_passwords.new_password != this.user_passwords.confirm_new_password) {
                    new Toast("Passwords do not match").show();
                    return;
                }

                if (this.user_passwords.new_password.length == 0) {
                    new Toast("Password cannot be empty").show();
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
                    new Toast("Successfully changed password!").show();
                    security_settings.is_loading = false;
                }).catch(function(error) {
                    new Toast(error.response.data).show();
                    security_settings.is_loading = false;
                });
            },
            changePasswordButtonValue: function() {
                return this.is_loading ? "Changing" : "Change Password";
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