<?php $this->load->view('assets/bs4_chosen') ?>

<?php
    $options_user = $this->App_model->options_user('role = 21', 'NINGUNO');
    $type_event_name = $this->Item_model->name(13, $row->type_id);
?>

<div id="edit_event_app">
    <div class="center_box_750">
        <div class="card" style="height: 350px;">
            <div class="card-body">
                <fieldset>
                    <div class="form-group row">
                        <label for="user_id" class="col-md-4 col-form-label text-right">Asignada a</label>
                        <div class="col-md-8">
                            <select class="form-control form-control-chosen" id="field-user_id" v-model="form_values.user_id">
                                <option v-for="(option_user, key_user) in options_user" v-bind:value="key_user">{{ option_user }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="start" class="col-md-4 col-form-label text-right">Tipo</label>
                        <div class="col-md-8">
                            <input type="text" readonly class="form-control-plaintext" value="<?= $type_event_name ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="start" class="col-md-4 col-form-label text-right">Inicio</label>
                        <div class="col-md-8">
                            <input type="text" readonly class="form-control-plaintext" v-bind:value="start_format">
                            <small class="form-text text-muted">{{ start_ago }}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-8 offset-md-4">
                            <button class="btn btn-primary w120p" v-on:click="set_user_send">
                                Guardar
                            </button>
                        </div>
                    </div>
                    
                </fieldset>
            </div>
        </div>
    </div>
</div>

<script>
// Filters
//-----------------------------------------------------------------------------
Vue.filter('date_format', function (date) {
    if (!date) return ''
    return moment(date).format('D [de] MMMM, dddd')
});

Vue.filter('ago', function (date) {
    if (!date) return ''
    return moment(date, 'YYYY-MM-DD HH:mm:ss').fromNow()
});

// VueApp
//-----------------------------------------------------------------------------
var edit_event_app = new Vue({
    el: '#edit_event_app',
    data: {
        form_values: {
            id: <?= $row->id ?>,
            user_id: '0<?= $row->user_id ?>',
            start: '<?= $row->start ?>',
        },
        options_user: <?= json_encode($options_user) ?>,
        loading: false,
    },
    methods: {
        // Establecer user_id desde chosen select y enviar formulario
        set_user_send: function(user_key){
            var user_id = 0
            if ( parseInt($('#field-user_id').val()) >0 ) {
                user_id = parseInt($('#field-user_id').val())
            }
            console.log('user_id', user_id)
            this.form_values.user_id = user_id
            this.send_form()
        },
        // Enviar formulario
        send_form: function(){
            this.loading = true
            var form_data = new FormData()
            form_data.append('id', this.form_values.id)
            form_data.append('user_id', this.form_values.user_id)

            axios.post(url_api + 'events/update/', form_data)
            .then(response => {
                if ( response.data.saved_id > 0 ) {
                    toastr['success']('Cita actualizada')
                }
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
    },
    computed: {
        start_format: function(){
            return moment(this.form_values.start).format('dddd, D [de] MMMM, h:mm a')
        },
        start_ago: function(){
            return moment(this.form_values.start, 'YYYY-MM-DD HH:mm:ss').fromNow()
        }
    }
})
</script>