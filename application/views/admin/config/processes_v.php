<div id="processes_app">
    <div class="">
        <div class="row">
            <div class="col-md-4">
                <table class="table bg-white">
                    <thead>
                        <th width="10px"></th>
                        <th>Proceso</th>
                    </thead>
                    <tbody>
                        <tr v-for="(process, key) in processes" v-bind:class="{'table-info': key == curr_key }">
                            <td>
                                <button class="btn btn-sm btn-light" v-on:click="set_process(key)">
                                    <span v-show="key == curr_key"><i class="fa fa-check-square"></i></span>
                                    <span v-show="key != curr_key"><i class="far fa-square"></i></span>
                                </button>
                            </td>
                            <td>{{ process.process_name }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-8">
                <div class="card mw750p">
                    <div class="card-body">
                        <h3>{{ curr_process.process_name }}</h3>
                        <div class="mb-2" v-html="curr_process.description"></div>
                        <div class="mb-2">
                            <button class="btn btn-primary btn-lg" v-on:click="run_process">
                                EJECUTAR
                            </button>
                        </div>
                        <div class="alert" v-bind:class="result.class" v-show="result.message.length > 0">
                            <i class="fa fa-spin fa-spinner mr-1" v-show="loading"></i>
                            <i class="fa fa-check mr-1" v-show="result.status == 1"></i>
                            <span v-html="result.message"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var processes_app = new Vue({
    el: '#processes_app',
    created: function(){
        this.set_process(0)
    },
    data: {
        processes: <?= json_encode($processes) ?>,
        curr_process: [],
        curr_key: 0,
        loading: false,
        result: {
            status: -1,
            class: 'alert-info',
            message: ''
        },
    },
    methods: {
        set_process: function(key_process){
            this.curr_key = key_process
            this.curr_process = this.processes[key_process]
            this.restart_result()
        },
        run_process: function(){
            this.loading = true
            this.restart_result()
            this.result.message = 'Ejecutando'
            var url_process = url_app + this.curr_process.process_link
            console.log(url_process)
            axios.get(url_process)
            .then(response => {
                console.log(response.data)
                if ( response.data.status == 1 ) {
                    this.result.class = 'alert-success'
                    this.result.status = 1
                }
                this.result.message = response.data.message
                this.loading = false
            }).catch(function(error) { console.log(error) })
        },
        restart_result: function(){
            this.result = {
                status: -1,
                class: 'alert-info',
                message: ''
            }
        },
    }
})
</script>