<form method="POST" action="/auth/login">
    <input type="hidden" name="<?php echo CSRF::getTokenID(); ?>" value="<?php echo CSRF::getTokenValue(); ?>" />

    <label for="login">Login</label>
    <input id="login" name="login" type="text" placeholder="Your login" />
    <label for="password">Password</label>
    <input id="password" name="password" type="password" placeholder="Your password" />
    <button type="submit" class="button large-5 small-12">Login</button>
    <button type="button" onclick="window.location.href='/auth/forgotpassword';" class="button alert large-5 small-12 right">Forgot Password</button>
</form>

<div v-cloak id="app">
    <div v-if="!user_is_mfa_enabled">
        <form method="post" @submit.prevent="isMfaEnabled">
            <label>Login</label>
            <input type="text" v-model="user_login" required>
            <label>Password</label>
            <input type="password" v-model="user_password" required>
            <input type="submit" class="button large-5 small-12" value="Login" :disabled="is_loading">
            <button type="button" class="button alert large-5 small-12 right" @click="" >Forgot Password</button>
        </form>
    </div>
    <div v-if="user_is_mfa_enabled">
        <form method="post" @submit.prevent="login">
            <label>MFA Code</label>
            <input type="text" v-model="user_mfa_code" required>
            <input type="submit" class="button large-5 small-12" value="Confirm" :disable="is_loading">
        </form>
    </div>
</div>
<script>
    var app = new Vue({
        el: '#app',
        data: function() {
            return {
                user_login: null,
                user_password: null,
                user_mfa_code: null,
                user_is_mfa_enabled: false
                is_loading: false
            }
        },
        methods: {
            isMfaEnabled: function {
                this.is_loading = true;

                axios.post('/auth/ajaxIsMfaEnabled', {
                    user_login: user_login,
                    user_password: user_password
                }).then(function(response) {
                    if (response.statusText == 'OK') {

                    }
                    app.is_loading = false;
                }).catch(function(error) {
                    console.log(error);
                    new Toast('Failed to login').show();
                });
            },
            login: function() {

            }
        }
    })
</script>