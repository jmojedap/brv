<div id="process_app">
    <div class="row">
        <div class="col-md-4">
            Filtros
        </div>
        <div class="col-md-4">
            <table class="table bg-white">
                <thead>
                    <th>Proceso</th>
                    <th></th>
                </thead>
                <tbody>
                    <tr v-for="(element, key) in process">
                        <td>{{ element.title }}</td>
                        <td>
                            <button class="btn btn-primary" v-on:click="send_form(key)">
                                Ejecutar
                            </button> 
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Resultado</h3>
                    <pre style="border: 1px solid #CCC;" class="p-2">{{ result }}</pre>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
var process_app = new Vue({
    el: '#process_app',
    data: {
        process: [
            {
                title: 'Actualizar URL',
                cf: 'files/update_url',
                description: 'Actualizar URL de tabla files con versión actual'
            }
        ],
        result: {}
    },
    methods: {
        send_form: function(process_key){

            axios.get(url_api + this.process[process_key].cf)
            .then(response => {
                this.result = response.data
                if (response.data.status == 1) { toastr['success']('Ejecutado') }
                if (response.data.qty_affected >= 0) { toastr['info']('Actualizados: ' + response.data.qty_affected) }
                if (response.data.status == 0) { toastr['warning']('El proceso no se ejecutó') }
            }).catch(function(error) {console.log(error)})
        },
    }
})
</script>