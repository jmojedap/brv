<script>
// Variables
//-----------------------------------------------------------------------------
var rooms = <?= json_encode($rooms->result()) ?>;

// Filters
//-----------------------------------------------------------------------------
Vue.filter('month_name', function (date) {
    if (!date) return ''
    return moment(date).format('MMM')
});

Vue.filter('date_format', function (date) {
    if (!date) return ''
    return moment(date).format('dddd, D [de] MMMM / YYYY')
});

Vue.filter('ago', function (date) {
    if (!date) return ''
    return moment(date, 'YYYY-MM-DD HH:mm:ss').fromNow()
});

Vue.filter('hour', function (date) {
    if (!date) return ''
    return moment(date).format('hh:mm a')
});

Vue.filter('day', function (date) {
    if (!date) return ''
    return moment(date).format('dddd, D / MMM')
});

Vue.filter('room_name', function (value) {
    if (!value) return 'ND'
    const room = rooms.find(room => room.room_id == value)
    name = room.name
    return name
});

// VueApp
//-----------------------------------------------------------------------------

var calendar_app = new Vue({
    el: '#calendar_app',
    created: function(){
        this.get_trainings()
    },
    data: {
        weeks: <?= json_encode($weeks) ?>,
        active_day: <?= json_encode($day) ?>,
        loading: false,
        options_year: <?= json_encode($options_year) ?>,
        options_months: [
            { month: '01', month_name: 'Ene' },
            { month: '02', month_name: 'Feb' },
            { month: '03', month_name: 'Mar' },
            { month: '04', month_name: 'Abr' },
            { month: '05', month_name: 'May' },
            { month: '06', month_name: 'Jun' },
            { month: '07', month_name: 'Jul' },
            { month: '08', month_name: 'Ago' },
            { month: '09', month_name: 'Sep' },
            { month: '10', month_name: 'Oct' },
            { month: '11', month_name: 'Nov' },
            { month: '12', month_name: 'Dic' },
        ],
        year: <?= $day->year ?>,
        month: <?= $day->month ?>,
        day_start: '<?= $day_start ?>',
        rooms: <?= json_encode($rooms->result()) ?>,
        room_id: <?= $room_id ?>,
        trainings: []
    },
    methods: {
        set_day: function(day){
            this.active_day = day
            console.log(day)
            this.get_trainings()
            history.pushState(null, null, url_admin + 'calendar/calendar/' + this.active_day.id)
        },
        //Clase HTML de la casilla día
        day_class: function(day){
            var day_class = 'day'
            if ( day.id == this.active_day.id ) day_class += ' active'
            if ( day.day == 1 ) day_class += ' first_month_day'
            if ( day.qty_business_days == 0 ) day_class += ' holyday'
            if ( day.start == '<?= date('Y-m-d') ?>' ) day_class += ' today'
            day_class += ' wd_' + day.week_day
            return day_class
        },
        set_month: function(){
            var day_id = this.year + (this.month + '01')
            if (this.month < 10) day_id = this.year + '0' + this.month + '01'
            window.location = url_app + 'calendar/calendar/' + day_id
        },
        sum_month: function(month){
            this.month = month
            if ( month < 1 ) {
                this.year = this.year - 1
                this.month = 12
            }
            if ( month > 12 ) {
                this.year = this.year + 1
                this.month = 1
            }
            //console.log(this.year, this.month)
            this.set_month()
        },
        set_room: function(room_id){
            this.room_id = room_id
            this.get_trainings()
        },
        get_trainings: function(){
            axios.get(url_api + 'calendar/get_trainings/' + this.active_day.id + '/' + this.room_id)
            .then(response => {
                this.trainings = response.data.list
            })
            .catch(function(error) { console.log(error) })
        },
    },
})
</script>