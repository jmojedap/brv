<script>
// Vue APP
//-----------------------------------------------------------------------------

    new Vue({
        el: '#pictures_app',
        created: function(){
            this.get_list();
        },
        data: {
            album_id: '<?= $row->id ?>',
            folder_galleries: '<?= URL_CONTENT . 'galleries/' . $row_user->id . '/thumbnails/' ?>',
            pictures: [],
            picture_id: 0,
            picture_key: 0,
        },
        methods: {
            get_list: function(){
                axios.get(url_app + 'albums/get_pictures/' + this.album_id)
                .then(response => {
                    this.pictures = response.data.list;
                })
                .catch(function (error) {
                    console.log(error);
                });  
            },
            set_private_picture: function(key){
                this.picture_id = this.pictures[key].id;
                axios.get(url_app + 'albums/set_private_picture/' + this.picture_id)
                .then(response => {
                    console.log(response.data.status)
                    this.get_list();
                })
                .catch(function (error) {
                    console.log(error);
                });  
            },
            import_pictures: function(){
                axios.get(url_app + 'albums/import_pictures/' + this.album_id)
                .then(response => {
                    toastr['info'](response.data.message)
                    this.get_list();
                })
                .catch(function (error) {
                    console.log(error);
                });  
            },
            set_current: function(key){
                this.picture_key = key;
                this.picture_id = this.pictures[key].id;
            },
            delete_element: function(){
                axios.get(url_app + 'albums/delete_picture/' + this.picture_id)
                .then(response => {
                    if ( response.data.status == 1 )
                    {
                        this.pictures.splice(this.picture_key, 1)
                        toastr['info']('La imagen fue eliminada');
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            send_file_form: function(){
                let form_data = new FormData();
                form_data.append('file_field', this.file);

                axios.post(url_api + 'albums/upload_picture/' + this.album_id, form_data, {headers: {'Content-Type': 'multipart/form-data'}})
                .then(response => {
                    //Cargar imagen
                    if ( response.data.status == 1 )
                    { 
                        /*this.post.image_id = response.data.image_id;
                        this.post.url_image = response.data.url_image;
                        window.location = url_app + 'posts/cropping/'+ this.posts.id;*/
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