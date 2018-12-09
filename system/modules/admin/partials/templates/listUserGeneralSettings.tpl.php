<div v-cloak id="general_settings">
    <div class="small-12 columns">
        <h3>Contact Details</h3>
    </div>
    <form method="post" @submit.prevent="updateGeneralSettings">
        <div class="small-3 columns">
            <label>First Name</label>
            <input type="text" v-model="contact.first_name" required>
            <label>Last Name</label>
            <input type="text" v-model="contact.last_name" required>
        </div>
        <div class="small-3 columns">
            <label>Home Phone</label>
            <input type="number" v-model="contact.home_phone">
            <label>Work Phone</label>
            <input type="number" v-model="contact.work_phone">
        </div>
        <div class="small-3 columns">
            <label>Private Mobile</label>
            <input type="number" v-model="contact.private_mobile">
            <label>Work Mobile</label>
            <input type="number" v-model="contact.work_mobile">
        </div>
        <div class="small-3 columns">
            <label>Email</label>
            <input type="email" v-model="contact.email">
            <label>Fax</label>
            <input type="number" v-model="contact.fax">
        </div>
        <div class="small-12 columns" style="padding-top: 1rem">
            <input type="submit" class="button small" v-model="updateButtonValue()" :disabled="is_loading">
        </div>
    </form>
</div>
<script>
    var general_settings = new Vue({
        el: '#general_settings',
        data: function() {
            return {
                user: <?php echo json_encode($user_array); ?>,
                contact: <?php echo json_encode($contact_array); ?>,
                is_loading: false,
            }
        },
        methods: {
            updateGeneralSettings: function() {
                this.is_loading = true;
                new Toast('Updating...').show();

                axios.post('/auth/ajaxUpdateGeneralSettings', {
                    contact: this.contact
                }).then(function(response) {
                    if (response.statusText == 'OK') {
                        new Toast("Update successful!").show();
                        general_settings.is_loading = false;
                    }
                }).catch(function(error) {
                    new Toast("Failed to update").show();
                    general_settings.is_loading = false;
                });
            },
            updateButtonValue: function() {
                return this.is_loading ? 'Updating' : 'Update';
            }
        }
    })
</script>