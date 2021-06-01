<div id="post_images">
    <div class="card center_box_750">
        <div class="card-body">
            <?php $this->load->view('common/upload_file_form_v') ?>
        </div>
    </div>
    <hr>
    <div id="image_gallery" >
        <div class="row">
            <div class="col-md-3" v-for="(image, image_key) in images">
                <div class="card mb-3 w100pc">
                    <img class="card-img-top" v-bind:alt="image.title" v-bind:src="image.url_thumbnail">
                    <div class="card-body">
                        <button class="btn btn-sm" v-on:click="set_main_image(image_key)" v-bind:class="{'btn-primary': image.main == 1, 'btn-light': image.main == 0 }">
                            <i class="far fa-check-circle" v-show="image.main == 1"></i>
                            <i class="far fa-circle" v-show="image.main == 0"></i>
                            Principal
                        </button>
                        <button class="btn btn-sm btn-light" v-on:click="set_current(image_key)" data-toggle="modal" data-target="#delete_modal" title="Eliminar imagen"><i class="fa fa-trash"></i></button>
                        <a v-bind:href="`<?= URL_ADMIN . "files/edit/" ?>` + image.id" class="btn btn-sm btn-light" target="_blank" title="Editar imagen">
                            <i class="fa fa-pencil-alt"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view('common/modal_single_delete_v') ?>
</div>

<script>
    new Vue({
        el: '#post_images',
        created: function(){
            this.get_list();
        },
        data: {
            file: '',
            post_id: '<?= $row->id ?>',
            images: <?= json_encode($images->result()); ?>,
            curr_image: {}
        },
        methods: {
            get_list: function(){
                axios.get(url_api + 'posts/get_images/' + this.post_id)
                .then(response => {
                    this.images = response.data.images;
                })
                .catch(function (error) {
                    console.log(error);
                });  
            },
            send_file_form: function(){
                let form_data = new FormData();
                form_data.append('file_field', this.file);
                form_data.append('table_id', '2000');
                form_data.append('related_1', this.post_id);

                axios.post(url_api + 'files/upload/', form_data, {headers: {'Content-Type': 'multipart/form-data'}})
                .then(response => {
                    //Cargar imágenes
                    if ( response.data.status == 1 ) { this.get_list(); }
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
            set_current: function(key){
                this.curr_image = this.images[key];
            },
            delete_element: function(){
                var file_id = this.curr_image.id;
                axios.get(url_api + 'files/delete/' + file_id)
                .then(response => {
                    this.get_list();
                })
                .catch(function (error) {
                    console.log(error);
                });  
            },
            set_main_image: function(key){
                this.set_current(key);
                var file_id = this.curr_image.id;
                console.log(this.curr_image);
                axios.get(url_api + 'posts/set_main_image/' + this.post_id + '/' + file_id)
                .then(response => {
                    if ( response.data.status == 1 ) this.get_list()
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
        }
    });
</script>