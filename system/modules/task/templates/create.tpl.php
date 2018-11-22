<script src='/system/templates/vue-components/form/elements/vue-search-select/vue-search-select.min.js'></script>
<script src='/system/templates/vue-components/vue-resource.min.js'></script>
<script src='/system/templates/vue-components/flatpickr/flatpickr.min.js'></script>
<script src='/system/templates/vue-components/vue-flatpickr.min.js'></script>
<link rel="stylesheet" type="text/css" href="/system/templates/vue-components/flatpickr/flatpickr.min.css">
<script src='/system/templates/vue-components/quill/quill.min.js'></script>
<script src='/system/templates/vue-components/quill/vue2-editor.js'></script>
<link rel="stylesheet" type="text/css" href="/system/templates/vue-components/quill/quill.snow.css">

<div id="task_edit">
    
    <div id="taskmodal" class="reveal-modal small" data-reveal data-closable>
        Are you sure you want to remove this subscriber?<br><br>
        <button class="button radius tiny success" v-on:click="delete_subscriber">Yes</button>
        <button class="button radius tiny alert" data-close>No</button>
    </div>

    <div id="save-modal" class="reveal-modal small" data-reveal data-closable>
        The task was saved successfully<br><br>
        <button class="button tiny radius success" data-close>OK</button>
    </div>
    
    <div class='row-fluid'>
        <div class='small-12 columns'>
            <h3>Create New Task</h3>
        </div>
    </div>

    <div class="row-fluid">
        <div class="medium-12 large-8 columns">
            Task title 
            <input name="title" id="title" required="required" type="text" v-model="title">
        </div>

        <div class="medium-12 large-4 columns">
            Due date
            <datepicker v-model="date" :config="dateconfig" placeholder="Due date"></datepicker>
        </div>
    </div>

    <div class="row-fluid">
        <div class="medium-12 large-4 columns">
            Assigned
            <model-list-select v-model="assignee_id" :list="assignee_list" placeholder="select item" option-value="value" option-text="text"></model-list-select>
        </div>
        <div class="medium-12 large-4 columns">
            Status
            <model-list-select v-model="status" :list="status_list" placeholder="select item" option-value="value" option-text="text"></model-list-select>
        </div>
        <div class="medium-12 large-4 columns">
            Priority
            <model-list-select v-model="priority" :list="priority_list" placeholder="select item" option-value="value" option-text="text"></model-list-select>
        </div>
    </div>
    
    <div class="row-fluid">
        <div class="medium-12 large-6 columns">
            Group 
            <model-list-select v-model="taskgroup_id" :list="taskgroup_list" placeholder="select item" option-value="value" option-text="text"></model-list-select>
        </div>
        <div class="medium-12 large-6 columns">
            Type 
            <model-list-select v-model="type" :list="type_list" placeholder="select item" option-value="value" option-text="text"></model-list-select>
        </div>
    </div>
        
    <div class="row-fluid columns">
        Description
        <vue-editor v-model="description"></vue-editor>
    </div>
        
    <div class="row-fluid">
        <div class="medium-12 large-6 columns">
            Estimated hours
            <input name="estimate_hours" id="estimate_hours" type="text" v-model="estimate_hours">
        </div>
        <div class="medium-12 large-6 columns">
            Effort
            <input name="effort" id="effort" type="text" v-model="effort">
        </div>
    </div>

    <div class="row-fluid columns">
        <br>
        <button class="tiny button radius" style="background-color: #59BC3B;" @click="save">Save</button>
        <button data-close class="tiny button radius" style="background-color: #FF7A13;">Cancel</button>
    </div>
</div>

<script>
    new Vue({
        el: '#task_edit',
        
        components: {
            "model-list-select": VueSearchSelect.ModelListSelect,
            "datepicker": VueFlatpickr
        },
        
        data: {
            taskgroup_id: null,
            type: null,
            title: null,
            id: null,
            status: null,
            priority: null,
            assignee_id: null,
            estimate_hours: null,
            effort: null,
            description: null,
            can_i_assign: null,
            subscribers: null,
            
            taskgroup_list: <?php echo $taskgroup_list; ?>,
            type_list: <?php echo $type_list; ?>,
            status_list: <?php echo $status_list; ?>,
            priority_list: <?php echo $priority_list; ?>,
            assignee_list: <?php echo $assignee_list; ?>,

            date: null,
            dateconfig: {
                altFormat: "j F Y",
                altInput: true
            }
        },
                
        methods: {
            save: function() {
                var params = {
                    id: this.id,
                    title: this.title,
                    dt_due: this.date,
                    assignee_id: this.assignee_id,
                    status: this.status,
                    priority: this.priority,
                    task_group_id: this.taskgroup_id,
                    type: this.type,
                    description: this.description,
                    estimate_hours: this.estimate_hours,
                    effort: this.effort
                };
                Vue.http.get('/task-ajax/save', {params: params}).then(function (response) {
                    console.log(response);
                    if (response.body.data === "updated") {
                        $('#save-modal').foundation('reveal','open');
                    }
                },
                function (error) {

                });
            }
        },

        created: function() {
            
        }
    });
</script>