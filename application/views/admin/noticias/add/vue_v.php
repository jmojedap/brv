<script>
    /*var random = 'HB' + Math.floor(Math.random() * 100000);
    var form_values = {};
    var form_values = {
        post_name: 'Noticia ' + random,
        cat_1: '05'
    };*/
    var form_values = {
        post_name: '',
        related_1: '',
        integer_1: ''
    };
            
    new Vue({
        el: '#add_noticia',
        data: {
            form_values: form_values,
            row_id: 0
        },
        methods: {
            send_form: function() {
                axios.post(url_app + 'noticias/insert/', $('#noticia_form').serialize())
                .then(response => {
                    console.log('status: ' + response.data.message);
                    if ( response.data.status == 1 )
                    {
                        this.row_id = response.data.row_id;
                        this.clean_form();
                        $('#modal_created').modal();
                    }
                })
                .catch(function (error) {
                     console.log(error);
                });
            },
            clean_form: function() {
                for ( key in form_values ) {
                    this.form_values[key] = '';
                }
            },
            go_created: function() {
                window.location = url_app + 'noticias/edit/' + this.row_id;
            }
        }
    });
</script>