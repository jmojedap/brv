<div id="edit_post">
    <div class="center_box_750">
        <div class="card">
            <div class="card-body">
                <form accept-charset="utf-8" method="POST" id="edit_form" @submit.prevent="send_form">
                    <fieldset v-bind:disabled="loading">
                        <input type="hidden" name="id" value="<?= $row->id ?>">

                        <div class="form-group row">
                            <label for="post_name" class="col-md-4 col-form-label text-right">Título</label>
                            <div class="col-md-8">
                                <input
                                    name="post_name" type="text" class="form-control"
                                    required
                                    title="Título" placeholder="Título"
                                    v-model="form_values.post_name"
                                >
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="content" class="col-md-4 col-form-label text-right">Texto publicación</label>
                            <div class="col-md-8">
                                <textarea
                                    name="content" class="form-control" rows="3" maxlength="280"
                                    title="Descripción" placeholder="Descripción"
                                    v-model="form_values.content"
                                ></textarea>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-md-8 offset-md-4">
                                <button class="btn btn-primary w120p" type="submit">Guardar</button>
                            </div>
                        </div>
                    <fieldset>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Variables
//-----------------------------------------------------------------------------

var row = <?= json_encode($row) ?>;

// VueApp
//-----------------------------------------------------------------------------
var edit_post = new Vue({
    el: '#edit_post',
    created: function(){
        //this.get_list()
    },
    data: {
        form_values: row,
        loading: false,
    },
    methods: {
        send_form: function(){
            this.loading = true
            var form_data = new FormData(document.getElementById('edit_form'))
            axios.post(url_api + 'posts/save/', form_data)
            .then(response => {
                if ( response.data.saved_id > 0 ) {
                    toastr['success']('Guardado')
                }
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
    }
})
</script>