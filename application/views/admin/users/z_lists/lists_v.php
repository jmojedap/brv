<div id="lists_app">
    <form accept-charset="utf-8" method="POST" id="list_form" @submit.prevent="send_form">
        <fieldset v-bind:disabled="loading">
            <div class="form-group row">
                <label for="post_name" class="col-md-4 col-form-label text-right">Nombre</label>
                <div class="col-md-8">
                    <input
                        name="post_name" type="text" class="form-control"
                        required
                        title="Nombre de la lista"
                        v-model="form_values.post_name"
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
    <div class="center_box_920">
        <table class="table bg-white">
            <thead>
                <th>Nombre</th>
                <th width="10px"></th>
            </thead>
            <tbody>
                <tr v-for="(list, key) in lists" v-bind:class="{'table-info': list.id == current_id }" v-bind:id="`list_` + list.id">
                    <td>{{ list.name }}</td>
                    <td>
                        <button class="a4" v-on:click="set_current(key)" type="button" data-toggle="modal" data-target="#delete_modal">
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
var lists_app = new Vue({
    el: '#lists_app',
    created: function(){
        this.get_list()
    },
    data: {
        lists: [],
        form_values: {
            id: 0, 'post_name': ''
        },
        current_id: 0,
        loading: false,
    },
    methods: {
        get_list: function(){
            this.loading = true
            var form_data = new FormData()
            form_data.append('sf', '22_list')
            form_data.append('type', 22) // Lista de elementos
            form_data.append('condition', 'related_1=1000') 
            axios.post(url_api + 'posts/get/1/100', form_data)
            .then(response => {
                this.lists = response.data.list
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
        set_current: function(key){
            this.current_id = this.lists[key].id
        },
        list_form_data: function(){
            var form_data = new FormData
            form_data.append('post_name', this.form_values.post_name)
            if ( this.form_values.id > 0) {
                form_data.append('id', this.form_values.id)
            } else {
                form_data.append('type_id', 22)
                form_data.append('related_1', 1000)
            }

            return form_data
        },
        send_form: function(){
            this.loading = true
            var form_data = this.list_form_data()
            axios.post(url_api + 'posts/save/', form_data)
            .then(response => {
                if ( response.data.saved_id > 0 ) {
                    this.current_id = response.data.saved_id
                    toastr['success']('Guardado')
                    this.get_list()
                }
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
        delete_element: function(){
            this.loading = true
            var form_data = new FormData()
            form_data.append('selected', this.current_id)
            axios.post(url_api + 'posts/delete_selected/', form_data)
            .then(response => {
                if ( response.data.qty_deleted > 0 ) {
                    toastr['success']('Lista eliminada')
                    $('#list_' + this.current_id).removeClass('table-info')
                    $('#list_' + this.current_id).addClass('table-danger')
                    $('#list_' + this.current_id).hide('slow')
                }
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
    }
})
</script>