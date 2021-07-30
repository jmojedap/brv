<div id="training_app">
    <div class="row">
        <div class="col-md-4">
            <table class="table bg-white">
                <tbody>
                    <tr>
                        <td class="td-title">ID Sesión</td>
                        <td>{{ training.id }}</td>
                    </tr>
                    <tr>
                        <td class="td-title">Fecha</td>
                        <td>{{ training.start }}</td>
                    </tr>
                    <tr>
                        <td class="td-title">Inicia</td>
                        <td>{{ training.start | ago }}</td>
                    </tr>
                    <tr>
                        <td class="td-title">Zona</td>
                        <td>{{ training.room_name }}</td>
                    </tr>
                    <tr>
                        <td class="td-title">Total cupos</td>
                        <td>{{ training.total_spots }}</td>
                    </tr>
                    <tr>
                        <td class="td-title">Cupos disponibles</td>
                        <td>{{ training.available_spots }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-8">
            <div class="text-center" v-show="loading">
                <i class="fa fa-spin fa-spinner fa-3x text-muted"></i>
            </div>
            <div>
                <table class="table bg-white">
                    <thead>
                        <th width="40px"></th>
                        <th>Usuario</th>
                    </thead>
                    <tbody>
                        <tr v-for="(reservation, key_reservation) in reservations">
                            <td>
                                <a v-bind:href="`<?= URL_ADMIN . "users/profile/" ?>` + reservation.user_id" class="">
                                    <img
                                        v-bind:src="reservation.user_thumbnail"
                                        class="rounded rounded-circle w40p"
                                        alt="Imagen de usuario"
                                        onerror="this.src='<?= URL_IMG ?>users/sm_user.png'"
                                    >
                                </a>
                            </td>
                            <td>
                                <a v-bind:href="`<?= URL_ADMIN . "users/profile/" ?>` + reservation.user_id" class="">
                                    {{ reservation.user_display_name }}
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>    
</div>

<script>
// Filters
//-----------------------------------------------------------------------------
Vue.filter('ago', function (date) {
    if (!date) return ''
    return moment(date, 'YYYY-MM-DD HH:mm:ss').fromNow()
});

// VueApp
//-----------------------------------------------------------------------------
var training_app = new Vue({
    el: '#training_app',
    created: function(){
        this.get_list()
    },
    data: {
        training: <?= json_encode($training) ?>,
        reservations: [],
        loading: true,
    },
    methods: {
        get_list: function(){
            axios.get(url_api + 'calendar/get_training_reservations/' + this.training.id)
            .then(response => {
                this.reservations = response.data.reservations
                this.loading = false
            })
            .catch(function(error) { console.log(error) })
        },
    }
})
</script>

