<div id="items_list">
    <div class="row">
        <div class="col-md-3">
            <table class="table bg-white">
                <tbody>
                    <tr v-for="(category, category_key) in categories" v-bind:class="{'table-info': category_key == category_id}">
                        <td width="50px" class="text-center">{{ category_key }}</td>
                        <td>{{ category }}</td>
                        <td>
                            <button
                                class="a4"
                                v-on:click="set_category(category_key)"
                                >
                                <i class="fa fa-arrow-right"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col col-md-4">

            <div class="card mb-2">
                <table class="table table-condensed bg-white">
                    <thead>
                        <th width="50px">Cód.</th>
                        <th>Nombre ítem</th>
                        <th width="91px"></th>
                    </thead>
                    <tbody>
                        <tr v-for="(row, key) in list" v-bind:class="{'table-info':row_id == row.id}">
                            <td class="text-center">{{ row.cod }}</td>
                            <td v-bind:class="`td_level_` + row.level">
                                <span v-bind:class="`item_level_` + row.level">
                                    {{ row.item_name }}
                                </span>
                            </td>
                            <td>
                                <button class="a4" v-on:click="set_form(key)">
                                    <i class="fa fa-pencil-alt"></i>
                                </button>
                                <button class="a4" data-toggle="modal" data-target="#delete_modal" v-on:click="current_element(key)">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

        <div class="col-md-5">
            <div class="mb-2">
                <button class="btn btn-primary w120p" v-on:click="clean_form">
                    <i class="fa fa-plus"></i>
                    Nuevo
                </button> 
                <button type="button" class="btn btn-secondary w120p" v-on:click="autocomplete" title="Completar automáticamente los campos secundarios faltantes">
                    <i class="fa fa-magic"></i>
                    Completar
                </button>
            </div>

            <div class="card">
                <div class="card-header">
                    {{ config_form.title }}
                </div>
                <div class="card-body">
                    <?php $this->load->view($this->views_folder . 'manage/form_v'); ?>
                </div>
            </div>
        </div>
    </div>

    <?php $this->load->view('common/modal_single_delete_v'); ?>
</div>

<?php $this->load->view($this->views_folder . 'manage/vue_v'); ?>