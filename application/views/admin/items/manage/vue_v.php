<script>
    var app_states = {
        add: {
            button_text: 'Agregar',
            button_class: 'btn-success'
        },
        edit: {
            button_text: 'Actualizar',
            button_class: 'btn-info'
        },
        saved: {
            button_text: 'Guardado',
            button_class: 'btn-success'
        },
        inserted: {
            button_text: 'Guardado',
            button_class: 'btn-success'
        },
        updated: {
            button_text: 'Actualizado',
            button_class: 'btn-success'
        }
    }
    
    new Vue({
        el: '#items_list',
        created: function(){
            this.get_list();
        },
        data: {
            categories: <?= json_encode($arr_categories) ?>,
            category_id: '<?= $category_id ?>',
            row_key: 0,
            row_id: 0,
            list: [],
            form_values: {
                item_name: '',
                cod: '',
                abbreviation: '',
                filter: '',
                parent_id: '',
                slug: '',
                description: '',
                long_name: '',
                short_name: '',
            },
            config_form: {
                title: 'Nuevo elemento',
                button_text: 'Agregar',
                button_class: 'btn-primary'
            },
            app_state: app_states.add
        },
        methods: {
            get_list: function (){
                axios.get(url_api + 'items/get_list/' + this.category_id)
                .then(response => {
                    this.list = response.data;
                    history.pushState(null, null, url_api + 'items/manage/' + this.category_id);
                    console.log(this.list[0].id);
                })
                .catch(function (error) {
                     console.log(error);
                });
            },
            //Cargar el formulario con datos de un elemento (key) de la list
            set_form: function (key){
                this.app_state = app_states.edit;
                this.row_id = this.list[key].id;
                //this.form_values = this.list[key];
                for ( field in this.form_values ) { this.form_values[field] = this.list[key][field]; }
                this.form_values.parent_id = '0' + this.list[key].parent_id;
                this.config_form.title = 'Editar: ' + this.list[key].item_name;
                console.log('row_id: ' + this.row_id);
                this.$refs.field_item_name.focus();
            },
            set_form_values: function(key){
                
            },
            send_form: function(){
                axios.post(url_api + 'items/save/' + this.row_id, $('#item_form').serialize())
                .then(response => {
                    console.log(response.data.status);
                    
                    if ( response.data.status == 1 ) 
                    {
                        app_state = app_states.saved;
                        toastr['success']('Registro guardado');
                        
                        if ( this.row_id > 0 ) {
                            this.app_state = app_states.updated;
                        } else {
                            this.app_state = app_states.inserted;
                            for ( key in this.form_values ) { this.form_values[key] = '';}
                        }
                        
                        this.get_list();
                        this.row_id = response.data.saved_id;
                    }
                    
                })
                .catch(function (error) {
                     console.log(error);
                });
            },
            //Establece un elemento como el actual
            current_element: function(key) {
                this.row_id = this.list[key].id;
                this.row_key = key;
                console.log(this.row_id);
            },
            delete_element: function() {
                axios.get(url_api + 'items/delete/' + this.row_id + '/' + this.category_id)
                .then(response => {
                    console.log(response.data);
                    if ( response.data.status == 1 )
                    {
                        this.list.splice(this.row_key, 1);
                        toastr['info']('Elemento eliminado');
                    }
                })
                .catch(function (error) {
                     console.log(error);
                });
            },
            clean_form: function() {
                this.row_id = 0;
                this.row_key = 0;
                for ( key in this.form_values ) { this.form_values[key] = ''; }
                this.config_form.title = 'Nuevo elemento';
                this.app_state = app_states.add;
                this.$refs.field_cod.focus();
            },
            set_category: function(category_id){
                console.log(category_id);
                this.category_id = category_id;
                this.get_list();
                this.clean_form();
            },
            //COMPLEMENTARY FUNCTIONS
            autocomplete: function() {
                console.log(this.form_values.item_name);
                this.set_names();
                if ( this.form_values.abbreviation.length == 0 ) { this.set_abbreviation(); }
                if ( this.form_values.slug.length == 0 ) { this.set_slug(); }
            },
            set_abbreviation: function() {
                console.log('Completando abreviatura');
                var abbreviation = '';
                abbreviation = this.form_values.item_name.substr(0,3);
                abbreviation = abbreviation.toLowerCase();
                console.log(abbreviation);
                this.form_values.abbreviation = abbreviation;
            },
            set_names: function() {
                if ( this.form_values.description.length == 0 ) { this.form_values.description = this.categories[this.category_id] + ', ' + this.form_values.item_name; }
                if ( this.form_values.long_name.length == 0 ) { this.form_values.long_name = this.form_values.item_name; }
                if ( this.form_values.short_name.length == 0 ) { this.form_values.short_name = this.form_values.item_name; }
            },
            //Establecer item.slug
            set_slug: function() {
                const params = new URLSearchParams();
                params.append('text', this.form_values.item_name);
                params.append('table', 'items');
                params.append('field', 'slug');
                
                axios.post(url_base + 'tools/unique_slug/', params)
                .then(response => {
                    console.log(response.data);
                    this.form_values.slug = response.data
                })
                .catch(function (error) { console.log(error) })
            }
        }
    });
</script>