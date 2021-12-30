<?php
    $filters_style = ( strlen($str_filters) > 0 ) ? '' : 'display: none;' ;
?>

<form accept-charset="utf-8" method="POST" id="search_form" @submit.prevent="get_list">
    <div class="form-group row">
        <div class="col-md-8">
            <div class="input-group mb-2">
                <input
                    type="text" name="q" class="form-control"
                    placeholder="Buscar" title="Buscar"
                    v-model="filters.q" v-on:change="get_list"
                    >
                <div class="input-group-append" title="Buscar">
                    <button status="button" class="btn btn-light btn-block" v-on:click="toggle_filters" title="Búsqueda avanzada">
                        <i class="fa fa-chevron-up" v-show="display_filters"></i>
                        <i class="fa fa-chevron-down" v-show="!display_filters"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div id="adv_filters" style="<?= $filters_style ?>">
        <div class="form-group row">
            <div class="col-md-8">
                <select name="status" v-model="filters.status" class="form-control">
                    <option v-for="(option_status, key_status) in options_status" v-bind:value="key_status">{{ option_status }}</option>
                </select>
            </div>
            <label for="status" class="col-md-4 control-label col-form-label">Estado pago</label>
        </div>
        <div class="form-group row">
            <div class="col-md-8">
                <select name="fe3" v-model="filters.fe3" class="form-control">
                    <option v-for="(option_payment_channel, key_payment_channel) in options_payment_channel" v-bind:value="key_payment_channel">{{ option_payment_channel }}</option>
                </select>
            </div>
            <label for="fe3" class="col-md-4 col-form-label">Canal</label>
        </div>

        <div class="form-group row">
            <div class="col-md-4">
                <input type="date" name="d1" class="form-control" title="Pagado desde" v-model="filters.d1">
            </div>
            <div class="col-md-4">
                <input type="date" name="d2" class="form-control" title="Pagado hasta" v-model="filters.d2">
            </div>
            <label for="d1" class="col-md-4 col-form-label">Pagado entre</label>
        </div>

        <!-- Botón ejecutar y limpiar filtros -->
        <div class="form-group row">
            <div class="col-md-8 text-right">
                <button class="btn btn-light w120p" v-on:click="remove_filters" type="button" v-show="active_filters">Todos</button>
                <button class="btn btn-primary w120p" type="submit">Buscar</button>
            </div>
        </div>
    </div>
</form>