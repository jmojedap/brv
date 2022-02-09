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
                    autofocus
                    v-model="filters.q" v-on:change="get_list"
                    >
                <div class="input-group-append" title="Buscar">
                    <button type="button" class="btn" title="Mostrar filtros para búsqueda avanzada"
                        v-on:click="toggle_filters"
                        v-bind:class="{'btn-primary': display_filters, 'btn-light': !display_filters }"
                        >
                        <i class="fas fa-chevron-down" v-show="!display_filters"></i>
                        <i class="fas fa-chevron-up" v-show="display_filters"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div id="adv_filters" style="<?= $filters_style ?>" class="mb-2">
        <div class="form-group row">
            <div class="col-md-8">
                <select name="role" v-model="filters.role" class="form-control" title="Filtrar por rol">
                    <option v-for="(option_role, key_role) in options_role" v-bind:value="key_role">{{ option_role }}</option>
                </select>
            </div>
            <label for="role" class="col-md-4 col-form-label">Rol</label>
        </div>

        <div class="form-group row">
            <div class="col-md-8">
                <select name="fe2" v-model="filters.fe2" class="form-control">
                    <option v-for="(option_fe2, key_fe2) in options_commercial_plan" v-bind:value="key_fe2">{{ option_fe2 }}</option>
                </select>
            </div>
            <label for="fe2" class="col-md-4 col-form-label">Plan</label>
        </div>

        <div class="form-group row">
            <div class="col-md-8">
                <select name="fe1" v-model="filters.fe1" class="form-control">
                    <option v-for="(option_expiration, key_expiration) in options_expiration" v-bind:value="key_expiration">{{ option_expiration }}</option>
                </select>
            </div>
            <label for="fe1" class="col-md-4 col-form-label">Suscripción</label>
        </div>

        <div class="form-group row">
            <div class="col-md-4">
                <input type="date" name="d1" class="form-control" title="Vencimiento desde" v-model="filters.d1">
            </div>
            <div class="col-md-4">
                <input type="date" name="d2" class="form-control" title="Vencimiento hasta" v-model="filters.d2">
            </div>
            <label for="d1" class="col-md-4 col-form-label">Vencimiento entre</label>
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