<?php $this->load->view('assets/summernote') ?>

<div id="edit_post" class="container">
    <form accept-charset="utf-8" method="POST" id="edit_form" @submit.prevent="send_form">
        <input type="hidden" name="id" value="<?= $row->id ?>">
        <fieldset v-bind:disabled="loading">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="post_name">Título</label>
                        <input
                            name="post_name" type="text" class="form-control"
                            required
                            title="Título" placeholder="Título"
                            v-model="form_values.post_name"
                        >
                    </div>
        
                    <div class="form-group">
                        <label for="content">Resumen</label>
                        <textarea
                            name="excerpt" class="form-control" rows="2" maxlength="280"
                            title="Resumen" placeholder="Resumen"
                            v-model="form_values.excerpt"
                        ></textarea>
                    </div>

                    <div class="form-group">
                        <label for="content">Contenido</label>
                        <textarea
                            name="content" class="summernote"
                            title="Resumen" placeholder="Resumen"
                            v-model="form_values.content"
                        ></textarea>
                    </div>
                    
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <button class="btn btn-success btn-block" type="submit">Guardar</button>
                    </div>
                    <div class="form-group">
                        <label for="text_1">Módulo</label>
                        <select name="text_1" v-model="form_values.text_1" class="form-control" required>
                            <option v-for="(option_text_1, key_text_1) in modules" v-bind:value="key_text_1">{{ option_text_1 }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="text_2">Sección</label>
                        <input
                            name="text_2" type="text" class="form-control"
                            required
                            title="Sección" placeholder="Sección"
                            v-model="form_values.text_2"
                        >
                    </div>
                    <div class="form-group">
                        <label for="keywords">Palabras clave</label>
                        <input
                            name="keywords" type="text" class="form-control"
                            required
                            title="Palabras clave" placeholder="Palabras clave"
                            v-model="form_values.keywords"
                        >
                    </div>
                </div>
            </div>

            
        <fieldset>
    </form>
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
        modules: {
            'Usuarios': 'Usuarios',
            'Entrenamientos': 'Entrenamientos',
            'InBody': 'InBody',
            'Calendario': 'Calendario',
            'Comercial': 'Comercial',
            'Pagos': 'Pagos',
            'Publicaciones': 'Publicaciones',
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