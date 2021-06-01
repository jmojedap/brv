<div id="product_images">
    <div class="card center_box_750">
        <div class="card-body">
            <?php $this->load->view('common/upload_file_form_v') ?>
        </div>
    </div>
    <hr>
    <div id="image_gallery" >
        <div class="card float-left mr-3" style="width: 18rem;" v-for="(image, image_key) in images">
            <img class="card-img-top" v-bind:alt="image.title" v-bind:src="image.url_thumbnail">
            <div class="card-body">
                <button class="btn btn-sm" v-on:click="set_main_image(image_key)" v-bind:class="{'btn-primary': image.main == 1, 'btn-light': image.main == 0 }">
                    <i class="far fa-check-circle" v-show="image.main == 1"></i>
                    <i class="far fa-circle" v-show="image.main == 0"></i>
                    Principal

                </button>
                <button class="btn btn-sm btn-light" v-on:click="set_current(image_key)" data-toggle="modal" data-target="#delete_modal">Eliminar</button>
            </div>
        </div>
    </div>
    <?php $this->load->view('common/modal_single_delete_v') ?>
</div>

<script>
    new Vue({
        el: '#product_images',
        created: function(){
            this.get_list();
        },
        data: {
            file: '',
            product_id: '<?= $row->id ?>',
            images: <?= json_encode($images->result()); ?>,
            current_image: {}
        },
        methods: {
            get_list: function(){
                axios.get(url_app + 'products/get_images/' + this.product_id)
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

                axios.post(url_app + 'products/add_image/' + this.product_id, form_data, {headers: {'Content-Type': 'multipart/form-data'}})
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
                this.current_image = this.images[key];
            },
            delete_element: function(){
                var meta_id = this.current_image.meta_id;
                axios.get(url_app + 'products/delete_image/' + this.product_id + '/' + meta_id)
                .then(response => {
                    this.get_list();
                })
                .catch(function (error) {
                    console.log(error);
                });  
            },
            set_main_image: function(key){
                this.set_current(key);
                var meta_id = this.current_image.meta_id;
                axios.get(url_app + 'products/set_main_image/' + this.product_id + '/' + meta_id)
                .then(response => {
                    if ( response.data.status == 1 ) {
                        this.get_list();
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
        }
    });
</script>