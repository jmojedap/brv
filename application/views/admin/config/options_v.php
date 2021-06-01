
<div id="options_app">
    <form accept-charset="utf-8" method="POST" id="option_form" @submit.prevent="save_option">
        <input type="hidden" name="id" v-bind:value="form_values.id">
        <table class="table bg-white">
            <thead>
                <th width="100px">ID</th>
                <th>Opciones</th>
                <th>Valor</th>
                <th width="50px">
                    <button class="btn btn-success btn-block" type="button" title="Nueva optiona" v-on:click="new_option">
                        Nueva
                    </button>
                </th>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <input
                            type="text"
                            id="field-id"
                            name="id"
                            required
                            class="form-control"
                            placeholder="ID"
                            title="ID"
                            v-bind:value="form_values.id"
                            >
                    </td>
                    <td>
                        <input
                            id="field-option_name"
                            type="text"
                            name="option_name"
                            class="form-control"
                            placeholder="Nombre"
                            title="Nombre de la opción"
                            required
                            v-bind:value="form_values.option_name"
                            >
                    </td>
                    <td>
                        <input
                            type="text"
                            id="field-option_value"
                            name="option_value"
                            required
                            class="form-control"
                            placeholder="Valor"
                            title="Valor"
                            v-bind:value="form_values.option_value"
                            >
                    </td>
                    <td>
                        <button class="btn btn-primary" type="submit">
                            Guardar
                        </button>
                    </td>
                </tr>
                <tr v-for="(option, key) in list" v-bind:class="{'table-success': key == option_key}">
                    <td>
                        {{ option.id }}
                    </td>
                    <td>
                        {{ option.option_name }}
                    </td>
                    <td>
                        {{ option.option_value }}
                    </td>
                    <td>
                        <button class="btn btn-light btn-sm" type="button" v-on:click="set_current(key)">
                            <i class="fa fa-pencil-alt"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" type="button" data-toggle="modal"
                            data-target="#delete_modal" v-on:click="set_current(key)">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
    <?php $this->load->view('common/modal_single_delete_v') ?>
</div>

<script>
new Vue({
    el: '#options_app',
    created: function() {
        this.get_list();
    },
    data: {
        list: [],
        option: {},
        form_values: {
            id: 0,
            option_name: '',
            option_value: ''
        },
        form_values_new: {
            id: 0,
            option_name: '',
            option_value: ''
        },
        option_key: -1,
        option_id: 0
    },
    methods: {
        get_list: function() {
            axios.get(url_app + 'config/get_options/')
                .then(response => {
                    this.list = response.data.options;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        new_option: function() {
            this.option_key = -1;
            this.option_id = 0;
            this.form_values = this.form_values_new;
            $('#field-id').focus();
        },
        set_current: function(key) {
            this.option_key = key;
            this.option_id = this.list[key].id;
            this.form_values = this.list[key];
        },
        save_option: function() {
            axios.post(url_app + 'config/save_option/' + this.option_id, $('#option_form').serialize())
                .then(response => {
                    toastr["success"](response.data.message);
                    this.get_list();
                    this.form_values = this.form_values_new;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        delete_element: function() {
            axios.get(url_app + 'config/delete_option/' + this.option_id)
                .then(response => {
                    toastr['info'](response.data.message);
                    this.get_list();
                    this.new_option();
                })
                .catch(function(error) {
                    console.log(error);
                });
        }
    }
});
</script>