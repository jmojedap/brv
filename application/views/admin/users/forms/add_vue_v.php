<script>
    /*var random = '16073' + Math.floor(Math.random() * 100000);
    var form_values = {};
    var form_values = {
        role: '021',
        first_name: 'Henry',
        last_name: 'Jones',
        document_number: random,
        document_type: '01',
        //email: random + 'jairo@gmail.com',
        email: '',
        //username: 'jairo' + random,
        username: '',
        password: 'contrasena7987987',
        city_id: '0909',
        city_id: '',
        birth_date: '1982-12-31',
        gender: '01'
    };*/
    
    var form_values = {
        role: '021',
        last_name: '',
        firts_name: '',
        document_number: '',
        document_type: '01',
        email: '',
        username: '',
        password: '',
        city_id: '0909',
        birth_date: '',
        gender: ''
    };
            
    new Vue({
        el: '#add_user',
        data: {
            form_values: form_values,
            validation: {
                document_number_unique: -1,
                username_unique: -1,
                email_unique: -1,
            },
            row_id: 0,
            options_role: <?= json_encode($options_role) ?>,
            options_city: <?= json_encode($options_city) ?>,
            options_gender: <?= json_encode($options_gender) ?>,
            options_document_type: <?= json_encode($options_document_type) ?>
        },
        methods: {
            validate_send: function () {
                axios.post(url_app + 'users/validate/', $('#add_form').serialize())
                .then(response => {
                    if ( response.data.status == 1 ) {
                        this.send_form();
                    } else {
                        toastr['error']('Revise las casillas en rojo');
                    }
                }).catch(function (error) { console.log(error) })
            },
            send_form: function() {
                axios.post(url_app + 'users/save/', $('#add_form').serialize())
                .then(response => {
                    if ( response.data.status == 1 )
                    {
                        this.row_id = response.data.saved_id;
                        this.clean_form()
                        $('#modal_created').modal()
                    }
                }).catch(function (error) {console.log(error)})
            },
            generate_username: function() {
                const params = new URLSearchParams();
                params.append('first_name', this.form_values.first_name);
                params.append('last_name', this.form_values.last_name);
                
                axios.post(url_app + 'users/username/', params)
                .then(response => {
                    this.form_values.username = response.data;
                }).catch(function(error) {console.log(error)})
            },
            validate_form: function() {
                axios.post(url_app + 'users/validate/', $('#add_form').serialize())
                .then(response => {
                    this.validation = response.data.validation
                }).catch(function (error) { console.log(error)})
            },
            clean_form: function() {
                for ( key in form_values ) this.form_values[key] = ''
                this.validation.document_number_unique = -1
                this.validation.email_unique = -1
                this.validation.username_unique = -1
            },
            go_created: function() {
                window.location = url_app + 'users/profile/' + this.row_id
            }
        }
    });
</script>