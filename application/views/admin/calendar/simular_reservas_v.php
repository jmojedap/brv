<div id="simular_reservas_app">
    <div class="row">
        <div class="col-md-4">
            <button class="btn btn-success" v-on:click="iniciar_simulacion" v-bind:disabled="loading">
                Ejecutar
            </button>
            <hr>
            <p>
                {{ num_intento }} de {{ total_intentos }}
            </p>
        </div>
        <div class="col-md-8">
            <table class="table bg-white">
                <thead>
                    <th>Index</th>
                    <th>Sesión</th>
                    <th>User ID</th>
                    <th>Reservation ID</th>
                    <th>Error</th>
                </thead>
                <tbody>
                    <tr v-for="(intento, key) in intentos">
                        <td>{{ intento.index }}</td>
                        <td>{{ intento.training_id }}</td>
                        <td>{{ intento.user_id }}</td>
                        <td>{{ intento.reservation_id }}</td>
                        <td>{{ intento.error }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
var simular_reservas_app = new Vue({
    el: '#simular_reservas_app',
    created: function(){
        //this.get_list()
    },
    data: {
        users: <?= json_encode($users->result()) ?>,
        trainings: <?= json_encode($trainings->result()) ?>,
        intentos: [],
        total_intentos: 500,
        num_intento: 0,
        loading: false
    },
    methods: {
        iniciar_simulacion: function(){
            this.loading = true
            this.save_reservation()
        },
        random_intento: function(){
            var training_index = Math.floor(Math.random()*this.trainings.length)
            var training = this.trainings[training_index]

            var user_index = Math.floor(Math.random()*this.users.length)
            var user = this.users[user_index]

            var intento = {
                index: this.num_intento,
                training_id: training.id,
                user_id: user.id,
                reservation_id: 0,
                error: '',
            }

            return intento
        },
        save_reservation: function(){
            var intento = this.random_intento()

            axios.get(url_eapi + 'reservations/save/' + intento.training_id + '/' + intento.user_id)
            .then(response => {
                intento.reservation_id = response.data.saved_id
                intento.error = response.data.error
                this.intentos.push(intento)
                this.siguiente_intento()
            })
            .catch(function(error) { console.log(error) })
        },
        siguiente_intento: function(){
            this.num_intento++
            if ( this.num_intento < this.total_intentos ) {
                this.save_reservation()
            } else {
                toastr['info']('Simulación finalizada')
                this.loading = false
            }
        },
    }
})
</script>