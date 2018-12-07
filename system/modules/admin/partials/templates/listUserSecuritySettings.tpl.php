<div v-cloak id="app">
    <div class="small-6 columns">
        <h3>Change Password</h3>
    </div>
    <div class="small-6 columns">
        <h3>Multi Factor Authentication</h3>
    </div>
    <div class="small-6 columns">
        <label>Old Password</label>
        <input type="password">
        <label>New Password</label>
        <input type="password">
        <label>Confirm New Password</label>
        <input type="password">
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
                <button class="small" style="float: right; margin: 0;" @click="">Add</button>
            </div>
        </div>
    </div>
    <div class="small-12 columns" style="padding-top: 1rem">
        <button class="small">Change Password</button>
        <button class="small warning">Forgot Password</button>
    </div>
</div>
<script>
    var app = new Vue({
        el: '#app',
        data: function() {
            return {
                user: <?php echo json_encode($user_array); ?>,
            }
        },
        methods: {
            editMfa: function {
                if (user.is_mfa_enabled) {
                    axios.post('/auth/removeMfa', {
                        user_id: app.user.id
                    }).then(function(response) {
                        app.user.is_mfa_enabled = false;
                    }).catch(function(error) {
                        new Toast("Failed to remove MFA").show();
                    });
                } else {
                    axios.post('/auth/generateMfa', {
                        user_id: app.user.id
                    }).then(function(response) {
                        app.user.is_mfa_enabled = true;
                    }).catch(function(error) {
                        new Toast("Failed to generate MFA QR Code").show();
                    });
                }
            }
        }
    })
</script>
<style>
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