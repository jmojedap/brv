<?php
    $filters_style = ( strlen($str_filters) > 0 ) ? '' : 'display: none;' ;
?>

<form accept-charset="utf-8" method="POST" id="search_form" @submit.prevent="get_list">
    <div class="form-group row">
        <div class="col-md-9">
            <div class="input-group mb-2">
                <input
                    type="text" name="q"
                    class="form-control"
                    placeholder="Buscar" autofocus
                    title="Buscar"
                    v-model="filters.q" v-on:change="get_list"
                    >
                <div class="input-group-append" title="Buscar">
                    <button type="button" class="btn btn-light btn-block" v-on:click="toggle_filters" title="Búsqueda avanzada">
                        <i class="fa fa-chevron-up" v-show="showing_filters"></i>
                        <i class="fa fa-chevron-down" v-show="!showing_filters"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary btn-block">
                <i class="fa fa-search"></i>
                Buscar
            </button>
        </div>
    </div>
    <div id="adv_filters" style="<?= $filters_style ?>">
        <div class="form-group row">
            <div class="col-md-9">
                <?= form_dropdown('cat', $options_cat_clothes, $filters['cat'], 'class="form-control" title="Filtrar por categoría ropa" v-model="filters.cat"'); ?>
            </div>
            <label for="cat" class="col-md-3 col-form-label">Categoría</label>
        </div>
        <div class="form-group row">
            <div class="col-md-9">
                <?= form_dropdown('u', $options_girl, $filters['u'], 'class="form-control" title="Filtrar por Bonita" v-model="filters.u"'); ?>
            </div>
            <label for="u" class="col-md-3 col-form-label">Bonita</label>
        </div>
    </div>
</form>