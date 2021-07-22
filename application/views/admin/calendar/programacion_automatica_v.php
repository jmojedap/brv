<div id="programacion_automatica_app">
    <div class="center_box_750">
        <div class="card">
            <div class="card-body">
                <form accept-charset="utf-8" method="POST" id="programacion_form" @submit.prevent="send_form">
                    <fieldset v-bind:disabled="loading">
                        <div class="form-group row">
                            <label for="date_start" class="col-md-4 col-form-label text-right">Fecha desde</label>
                            <div class="col-md-8">
                                <input
                                    name="date_start" type="date" class="form-control"
                                    required
                                    v-model="form_values.date_start"
                                >
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="date_end" class="col-md-4 col-form-label text-right">Fecha hasta</label>
                            <div class="col-md-8">
                                <input
                                    name="date_end" type="date" class="form-control"
                                    required
                                    v-model="form_values.date_end"
                                >
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-md-8 offset-md-4">
                                <button class="btn btn-primary w120p" type="submit">Guardar</button>
                            </div>
                        </div>
                    <fieldset>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
var programacion_automatica_app = new Vue({
    el: '#programacion_automatica_app',
    created: function(){
        //this.get_list()
    },
    data: {
        form_values: {
            date_start: '<?= date('Y-m-d') ?>',
            date_end: '<?= date('Y-m-d') ?>',
        },
        loading: false,
    },
    methods: {
        send_form: function(){
            this.loading = true
            var form_data = new FormData(document.getElementById('programacion_form'))
            axios.post(url_api + 'calendar/programar_sesiones/', form_data)
            .then(response => {
                console.log(response.data)
                toastr['info'](response.data.message)
                /*if ( response.data.saved_id > 0 ) {
                    toastr['success']('Guardado')
                }*/
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
    }
})
</script>