<?php
    $filters_style = ( strlen($str_filters) > 0 ) ? '' : 'display: none;' ;
?>

<form accept-charset="utf-8" method="POST" id="search_form" @submit.prevent="get_list">
    <div class="form-group row">
        <div class="col-md-9">
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
            <div class="col-md-9">
                <select name="fe1" v-model="filters.fe1" class="form-control" title="Filtrar por tabla comentada">
                    <option v-for="(option_table, key_table) in options_table" v-bind:value="key_table">{{ option_table }}</option>
                </select>
            </div>
            <label for="fe1" class="col-md-3 col-form-label">Tabla</label>
        </div>

        <div class="form-group row">
            <div class="col-md-9">
                <input name="fe2" type="number" class="form-control" v-model="filters.fe2" title="ID elemento comentado">
            </div>
            <label for="fe1" class="col-md-3 col-form-label">ID Elemento</label>
        </div>

        <div class="form-group row">
            <div class="col-md-9">
                <input name="prnt" type="number" class="form-control" v-model="filters.prnt" title="ID comentario padre">
            </div>
            <label for="prnt" class="col-md-3 col-form-label">ID Padre</label>
        </div>

        <!-- Botón ejecutar y limpiar filtros -->
        <div class="form-group row">
            <div class="col-md-9 text-right">
                <button class="btn btn-light w120p" v-on:click="remove_filters" type="button" v-show="active_filters">Todos</button>
                <button class="btn btn-primary w120p" type="submit">Buscar</button>
            </div>
        </div>
    </div>
</form>