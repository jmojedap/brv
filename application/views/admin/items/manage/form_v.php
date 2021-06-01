<?php
    //$options_parent = $this->Item_model->options_id("category_id = {$category_id}", 'Seleccione el padre');
    $options_parent = array();
?>

<form accept-charset="utf-8" id="item_form" @submit.prevent="send_form">
    <input id="field-categoria_id" name="category_id" type="hidden" v-model="category_id">

    <div class="form-group row">
        <div class="col-sm-8 offset-4">
            <button class="btn w120p" v-bind:class="app_state.button_class" type="submit">
                {{ app_state.button_text }}
            </button>
        </div>
    </div>

    <div class="form-group row">
        <label for="cod" class="col-md-4 col-form-label text-right">
            <span class="float-rigth">Código</span>
        </label>
        <div class="col-md-8">
            <input
                id="field-cod"
                name="cod"
                ref="field_cod"
                class="form-control"
                placeholder="Código numérico"
                title="Código numérico"
                required
                v-model="form_values.cod"
                >
        </div>
    </div>

    <div class="form-group row">
        <label for="item_name" class="col-md-4 col-form-label text-right">
            <span class="float-rigth">Nombre</span>
        </label>
        <div class="col-md-8">
            <input
                id="field-item_name"
                name="item_name"
                ref="field_item_name"
                class="form-control"
                placeholder=""
                title="Nombre del ítem"
                required
                v-model="form_values.item_name"
                v-on:change="autocomplete"
                >
        </div>
    </div>

    <div class="form-group row">
        <label for="description" class="col-md-4 col-form-label text-right">Descripción</label>
        <div class="col-md-8">
            <textarea
                id="field-description"
                name="description"
                class="form-control"
                placeholder=""
                title="Descripción del ítem"
                required
                v-model="form_values.description"
                rows="3"
                ></textarea>
        </div>
    </div>

    <div class="form-group row">
        <label for="item_group" class="col-md-4 col-form-label text-right">Grupo</label>
        <div class="col-md-8">
            <input
                id="field-item_group"
                name="item_group"
                class="form-control"
                placeholder=""
                title="Grupo item"
                v-model="form_values.item_group"
                >
        </div>
    </div>

    <div class="form-group row">
        <label for="filters" class="col-md-4 col-form-label text-right">Filtros</label>
        <div class="col-md-8">
            <input
                id="field-filtro"
                name="filters"
                class="form-control"
                placeholder=""
                title="Filtros del ítem"
                v-model="form_values.filters"
                >
        </div>
    </div>

    <div class="form-group row">
        <label for="parent_id" class="col-sm-4 col-form-label text-right">Padre</label>
        <div class="col-sm-8">
            <select name="parent_id" id="field-parent_id" v-model="form_values.parent_id" class="form-control">
                <option value="">[ Ninguno ]</option>
                <option v-for="(item, item_key) in list" v-bind:value="`0` + item.cod" v-show="item.id != row_id">
                    {{ item.item_name }}
                </option>
            </select>
        </div>
    </div>

    <div class="form-group row">
        <label for="slug" class="col-md-4 col-form-label text-right">
            <span class="float-rigth">Slug</span>
        </label>
        <div class="col-md-8">
            <input
                id="field-slug"
                name="slug"
                class="form-control"
                placeholder=""
                title="Sin espacios y acentos"
                required
                v-model="form_values.slug"

                >
        </div>
    </div>

    <div class="form-group row">
        <label for="abbreviation" class="col-md-4 col-form-label text-right">Abreviatura</label>
        <div class="col-md-8">
            <input
                id="field-abbreviation"
                name="abbreviation"
                class="form-control"
                placeholder=""
                title="Abreviatura de hasta 4 caracteres"
                v-model="form_values.abbreviation"
                >
        </div>
    </div>

    <div class="form-group row">
        <label for="long_name" class="col-md-4 col-form-label text-right">
            <span class="float-rigth">Nombre largo</span>
        </label>
        <div class="col-md-8">
            <input
                id="field-long_name"
                name="long_name"
                class="form-control"
                placeholder=""
                title="Nombre largo"
                required
                v-model="form_values.long_name"

                >
        </div>
    </div>

    <div class="form-group row">
        <label for="short_name" class="col-md-4 col-form-label text-right">
            <span class="float-rigth">Nombre corto</span>
        </label>
        <div class="col-md-8">
            <input
                id="field-short_name"
                name="short_name"
                class="form-control"
                placeholder=""
                title="Nombre corto"
                required
                v-model="form_values.short_name"
                >
        </div>
    </div>
</form>