<div id="appointments_app">
    <div class="center_box_750">
        <div class="text-center" v-show="loading">
            <i class="fa fa-spin fa-spinner fa-3x text-muted"></i>
        </div>
        <table class="table bg-white">
            <thead>
                <th width="40px"></th>
                <th>Tipo cita</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th></th>
                <th width="10px"></th>
            </thead>
            <tbody>
                <tr v-for="(appointment, key_appointment) in appointments">
                    <td>
                        <i class="fa fa-circle" v-bind:class="`text_evt` + appointment.type_id"></i>
                    </td>
                    <td>
                        <a v-bind:href="`<?= URL_ADMIN . "events/edit/" ?>` + appointment.id">
                            {{ appointment.type_id | type_name }}
                        </a>
                    </td>
                    <td>
                        <a v-bind:href="`<?= URL_ADMIN . "calendar/calendar/" ?>` + appointment.period_id + `/appointments`">
                            {{ appointment.start | day }}
                        </a>
                    </td>
                    <td>
                        {{ appointment.start | hour }}
                    </td>
                    <td>
                        {{ appointment.start | ago }}
                    </td>
                    <td>
                        <a class="a4" v-bind:href="`<?= URL_ADMIN . "events/edit/" ?>` + appointment.id">
                            <i class="fa fa-pencil-alt"></i>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
// Variables
//-----------------------------------------------------------------------------
    var arr_types = <?= json_encode($arr_types) ?>;

// Filters
//-----------------------------------------------------------------------------
Vue.filter('type_name', function (value) {
    if (!value) return ''
    value = arr_types[value]
    return value
})

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
    return moment(date).format('D MMM - dddd')
})

// VueApp
//-----------------------------------------------------------------------------
var appointments_app = new Vue({
    el: '#appointments_app',
    created: function(){
        this.get_list()
    },
    data: {
        user: {id: <?= $row->id ?>,display_name: '<?= $row->display_name ?>'},
        appointments: [],
        loading: true,
        appointments: [],
        current_key: -1,
    },
    methods: {
        get_list: function(){
            form_data = new FormData()
            form_data.append('u', this.user.id)
            form_data.append('condition', 'events.type_id IN (221)')
            axios.post(url_api + 'events/get/', form_data)
            .then(response => {
                this.appointments = response.data.list
                this.loading = false
            })
            .catch(function(error) { console.log(error) })
        },
        set_element: function(key){
            this.current_key = key
        },
    }
})
</script>

