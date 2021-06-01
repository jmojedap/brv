<div class="float-right" style="max-width: 125px">
    <div class="input-group">
        <span class="input-group-prepend">
            <button id="btn_explorar_prev" class="btn btn-default" type="button" title="Página anterior">
                <i class="fa fa-caret-left"></i>
            </button>
        </span>
        <input
            id="field-num_page"
            type="number"
            name="num_page"
            value="<?= $num_page; ?>"
            min="1"
            max="<?= $max_page; ?>"
            class="form-control"
            title="<?= $max_page; ?> páginas en total">
        <span class="input-group-append">
            <button id="btn_explorar_next" class="btn btn-default" type="button" title="Página siguiente">
                <i class="fa fa-caret-right"></i>
            </button>
        </span>
    </div>
</div>