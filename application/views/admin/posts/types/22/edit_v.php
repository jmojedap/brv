<?php
    $options_table = $this->Item_model->options('category_id = 30 AND item_group = 1');
?>

<div id="edit_post">
    <div class="card center_box_750">
        <div class="card-body">
            
            <form accept-charset="utf-8" method="POST" id="edit_form" @submit.prevent="send_form">
                <input type="hidden" name="id" value="<?= $row->id ?>">
                <fieldset v-bind:disabled="loading">
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
                        <label for="content" class="col-md-4 col-form-label text-right">Descripción</label>
                        <div class="col-md-8">
                            <textarea
                                name="excerpt" class="form-control" rows="2" maxlength="280"
                                title="Descripción"
                                v-model="form_values.excerpt"
                            ></textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="related_1" class="col-md-4 col-form-label text-right">Tabla</label>
                        <div class="col-md-8">
                            <select name="related_1" v-model="form_values.related_1" class="form-control" required>
                                <option v-for="(option_related_1, key_related_1) in options_table" v-bind:value="key_related_1">{{ option_related_1 }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="cat_1" class="col-md-4 col-form-label text-right">Ámbito de la lista</label>
                        <div class="col-md-8">
                            <select name="cat_1" v-model="form_values.cat_1" class="form-control" required>
                                <option v-for="(option_cat_1, key_cat_1) in categories" v-bind:value="key_cat_1">{{ option_cat_1 }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-8 offset-md-4">
                            <button class="btn btn-success w120p" type="submit">Guardar</button>
                        </div>
                    </div>
                <fieldset>
            </form>
        </div>
    </div>
</div>

<script>
// Variables
//-----------------------------------------------------------------------------

var row = <?= json_encode($row) ?>;
row.related_1 = '0<?= $row->related_1 ?>';
row.cat_1 = '0<?= $row->cat_1 ?>';

// VueApp
//-----------------------------------------------------------------------------
var edit_post = new Vue({
    el: '#edit_post',
    created: function(){
        //this.get_list()
    },
    data: {
        form_values: row,
        options_table: <?= json_encode($options_table) ?>,
        categories: {
            '010': 'Plan comercial',
            '099': 'Otro',
        },
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