<script>
    /*var random = '16073' + Math.floor(Math.random() * 100000);
    var form_values = {};
    var form_values = {
        first_name: 'Henry',
        last_name: 'Jones',
        document_number: random,
        document_type: '01',
        email: random + 'jairo@gmail.com',
        username: 'jairo' + random,
        password: 'contrasena7987987',
        //city_id: '0614',
        city_id: '',
        birth_date: '1982-12-31',
        gender: '01'
    };*/
    
    var form_values = {
        post_name: '',
        type_id: ''
    };
            
    new Vue({
        el: '#add_post',
        data: {
            form_values: form_values,
            row_id: 0
        },
        methods: {
            send_form: function() {
                axios.post(url_app + 'posts/save/', $('#add_form').serialize())
                .then(response => {
                    console.log('status: ' + response.data.message);
                    if ( response.data.status == 1 )
                    {
                        this.row_id = response.data.saved_id;
                        this.clean_form();
                        $('#modal_created').modal();
                    }
                })
                .catch(function (error) { console.log(error) })
            },
            clean_form: function() {
                for ( key in form_values ) {
                    this.form_values[key] = '';
                }
            },
            go_created: function() {
                window.location = url_app + 'posts/edit/' + this.row_id;
            }
        }
    });
</script>