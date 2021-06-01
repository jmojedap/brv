<div id="add_comment_app">
    <div class="center_box_750">
        <div class="card">
            <div class="card-body">
                <form accept-charset="utf-8" method="POST" id="form_id" @submit.prevent="send_form">
                    <div class="form-group row">
                        <label for="table_id" class="col-md-4 col-form-label text-right">ID Tabla</label>
                        <div class="col-md-8">
                            <select v-model="table_id" class="form-control">
                                <option v-for="(option_table, key_table) in options_table" v-bind:value="key_table">{{ option_table }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="element_id" class="col-md-4 col-form-label text-right">ID Elemento</label>
                        <div class="col-md-8">
                            <input
                                type="number" class="form-control" required
                                v-model="element_id"
                            >
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="comment_text" class="col-md-4 col-form-label text-right">Comentario</label>
                        <div class="col-md-8">
                            <textarea
                                name="comment_text" type="text" class="form-control" maxlength=280 rows="5" required
                                v-model="form_values.comment_text"
                            ></textarea>
                            <small class="form-text text-muted">Disponibles: {{ 280 - form_values.comment_text.length }}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="parent_id" class="col-md-4 col-form-label text-right">ID Padre</label>
                        <div class="col-md-8">
                            <input
                                name="parent_id" type="text" class="form-control"
                                title="ID Padre" placeholder="ID Padre"
                                v-model="form_values.parent_id"
                            >
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-8 offset-md-4">
                            <button class="btn btn-primary w120p" type="submit">
                                Guardar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php $this->load->view('common/modal_created_v') ?>
</div>

<script>
var add_comment_app = new Vue({
    el: '#add_comment_app',
    created: function(){
        //this.get_list()
    },
    data: {
        row_id: 0,
        table_id: 2000,
        element_id: 1644,
        form_values: {
            parent_id: 0,
            comment_text: 'Pruebas de comentario <?= date('Y-m-d H:i:s') ?>'
        },
        options_table: <?= json_encode($options_table) ?>
    },
    methods: {
        send_form: function(){
            axios.post(url_api + 'comments/save/' + this.table_id + '/' + this.element_id, $('#form_id').serialize())
            .then(response => {
                console.log(response.data)
                if ( response.data.saved_id > 0 )
                {
                    this.row_id = response.data.saved_id
                    this.clean_form()
                    $('#modal_created').modal()
                }
            })
            .catch(function(error) {console.log(error)})  
        },
        clean_form: function() {
            this.form_values.parent_id = 0
            this.form_values.comment_text = ''
        },
        go_created: function() {
            window.location = url_app + 'comments/info/' + this.row_id;
        }
    }
})
</script>