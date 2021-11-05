<div id="reservations_app">
    <div class="center_box_750">
        <div class="text-center" v-show="loading">
            <i class="fa fa-spin fa-spinner fa-3x text-muted"></i>
        </div>
        <table class="table bg-white">
            <thead>
                <th width="40px"></th>
                <th>Zona</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th></th>
                <th v-if="app_rid <= 2"></th>
            </thead>
            <tbody>
                <tr v-for="(reservation, key_reservation) in reservations">
                    <td>
                        <i class="fa fa-circle" v-bind:class="`text_z` + reservation.room_id"></i>
                    </td>
                    <td>
                        <a v-bind:href="`<?= URL_ADMIN . "trainings/info/" ?>` + reservation.training_id" class="">
                            {{ reservation.title }}
                        </a>
                    </td>
                    <td>
                        {{ reservation.start | day }}
                    </td>
                    <td>
                        {{ reservation.start | hour }}
                    </td>
                    <td>
                        {{ reservation.start | ago }}
                    </td>
                    <td v-if="app_rid <= 2">
                        <button class="a4" data-toggle="modal" data-target="#delete_modal" v-on:click="set_element(key_reservation)">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php $this->load->view('common/modal_single_delete_v') ?>
</div>

<script>
// Filters
//-----------------------------------------------------------------------------
Vue.filter('ago', function (date) {
    if (!date) return ''
    return moment(date, 'YYYY-MM-DD HH:mm:ss').fromNow()
});

Vue.filter('hour', function (date) {
    if (!date) return ''
    return moment(date).format('hh:mm a')
})

Vue.filter('day', function (date) {
    if (!date) return ''
    return moment(date).format('dddd, D / MMM')
})

// VueApp
//-----------------------------------------------------------------------------
var reservations_app = new Vue({
    el: '#reservations_app',
    created: function(){
        this.get_list()
    },
    data: {
        user: <?= json_encode($row) ?>,
        reservations: [],
        loading: true,
        reservations: [],
        current_key: -1,
    },
    methods: {
        get_list: function(){
            axios.get(url_eapi + 'reservations/user_reservations/' + this.user.id)
            .then(response => {
                this.reservations = response.data.reservations
                this.loading = false
            })
            .catch(function(error) { console.log(error) })
        },
        set_element: function(key){
            this.current_key = key
        },
        delete_element: function(){
            var reservation = this.reservations[this.current_key];
            axios.get(url_eapi + 'reservations/delete/' + reservation.id + '/' + reservation.training_id)
            .then(response => {
                if ( response.data.qty_deleted > 0 ) {
                    this.reservations.splice(this.current_key,1)
                    this.current_key = -1
                    toastr['info']('Reservación eliminada')
                } else {
                    toastr['error'](response.data.error)
                }
            })
            .catch(function(error) { console.log(error) })
        },
    }
})
</script>

