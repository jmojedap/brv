<div id="schedule_generator_app">
    <div class="center_box_750">
        <div class="card">
            <div class="card-body">
                <form accept-charset="utf-8" method="POST" id="programacion_form" @submit.prevent="send_form">
                    <fieldset v-bind:disabled="loading">
                        <div class="form-group row">
                            <label for="date_start" class="col-md-4 col-form-label text-right">Fecha</label>
                            <div class="col-md-8">
                                <input
                                    name="date_start" type="date" class="form-control"
                                    required
                                    v-model="form_values.date_start"
                                >
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="room_id" class="col-md-4 col-form-label text-right">Zona</label>
                            <div class="col-md-8">
                                <select name="room_id" v-model="form_values.room_id" class="form-control" required>
                                    <option v-for="(option_room, key_room) in rooms" v-bind:value="`0` + option_room.room_id">{{ option_room.name }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="total_spots" class="col-md-4 col-form-label text-right">Total cupos</label>
                            <div class="col-md-8">
                                <input
                                    name="total_spots" type="number" class="form-control" min="1" max="60" value="40"
                                    required
                                    title="Cupo máximo" placeholder="Cupo máximo"
                                >
                            </div>
                        </div>


                        <!-- <div class="form-group row">
                            <label for="date_end" class="col-md-4 col-form-label text-right">Fecha hasta</label>
                            <div class="col-md-8">
                                <input
                                    name="date_end" type="date" class="form-control"
                                    required
                                    v-model="form_values.date_end"
                                >
                            </div>
                        </div> -->
                        
                        <div class="form-group row">
                            <div class="col-md-8 offset-md-4">
                                <button class="btn btn-primary w120p" type="submit">Programar</button>
                            </div>
                        </div>
                    <fieldset>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
var schedule_generator_app = new Vue({
    el: '#schedule_generator_app',
    data: {
        form_values: {
            date_start: '<?= date('Y-m-d') ?>',
            date_end: '<?= date('Y-m-d') ?>',
        },
        rooms: <?= json_encode($rooms->result()) ?>,
        loading: false,
    },
    methods: {
        send_form: function(){
            this.loading = true
            var form_data = new FormData(document.getElementById('programacion_form'))
            axios.post(url_api + 'trainings/schedule/', form_data)
            .then(response => {
                console.log(response.data)
                toastr['info'](response.data.message)
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
    }
})
</script>