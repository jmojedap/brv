<div id="add_file_app">
    <div class="card center_box_750">
        <div class="card-body">
            <?php $this->load->view('common/upload_file_form_v') ?>
        </div>
    </div>
</div>

<script>
    new Vue({
        el: '#add_file_app',
        created: function(){
            //this.get_list();
        },
        data: {
            file: '',
        },
        methods: {
            send_file_form: function(){
                let form_data = new FormData();
                form_data.append('file_field', this.file);

                axios.post(url_api + 'files/upload/', form_data, {headers: {'Content-Type': 'multipart/form-data'}})
                .then(response => {
                    console.log(response.data);
                    //Ir a la vista de la imagen
                    if ( response.data.status == 1 ) {
                        window.location = url_app + 'files/info/' + response.data.row.id;
                    }
                    //Mostrar respuesta html, si existe
                    if ( response.data.html ) { $('#upload_response').html(response.data.html); }
                    //Limpiar formulario
                    $('#field-file').val(''); 
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            handle_file_upload(){
                this.file = this.$refs.file_field.files[0];
            },
        }
    });
</script>