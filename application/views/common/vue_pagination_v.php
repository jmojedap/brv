<div class="input-group float-right" style="width: 120px;">
    <div class="input-group-prepend">
        <button class="btn btn-light" v-on:click="sum_page(-1)" title="Página anterior">
            <i class="fa fa-caret-left"></i>
        </button>
    </div>
    <input
        id="campo-num_page"
        name="num_page"
        class="form-control"
        type="number"
        value="1"
        min="1"
        v-bind:max="max_page"
        v-model="num_page"
        v-on:change="get_list"
        v-bind:title="`${max_page} páginas en total`"
        >
    <div class="input-group-append">
        <button class="btn btn-light" v-on:click="sum_page(1)" title="Página siguiente">
            <i class="fa fa-caret-right"></i>
        </button>
    </div>
</div>