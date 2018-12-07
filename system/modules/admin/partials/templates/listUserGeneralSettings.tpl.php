<div v-cloak id="app">
    <div class="small-12 columns">
        <h3>Contact Details</h3>
    </div>
    <div class="small-3 columns">
        <label>First Name</label>
        <input type="text">
        <label>Last Name</label>
        <input type="text">
    </div>
    <div class="small-3 columns">
        <label>Home Phone</label>
        <input type="number">
        <label>Work Phone</label>
        <input type="number">
    </div>
    <div class="small-3 columns">
        <label>Private Mobile</label>
        <input type="number">
        <label>Work Mobile</label>
        <input type="number">
    </div>
    <div class="small-3 columns">
        <label>Email</label>
        <input type="email">
        <label>Fax</label>
        <input type="number">
    </div>
    <div class="small-12 columns" style="padding-top: 1rem">
        <button class="small">Update</button>
    </div>
</div>
<script>
    var app = new Vue({
        el: '#app',
        data: function() {
            return {
                user: <?php echo json_encode($user_array); ?>,
            }
        }
    })
</script>