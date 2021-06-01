<div class="center_box_750">
    <div class="card mb-2">
        <div class="card-body">
            <form action="<?= $destination_form ?>" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                <div class="form-group row">
                    <label for="file" class="col-md-3 col-form-label text-right">Archivo</label>
                    <div class="col-md-9">
                        <input type="file" class="form-control" name="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="sheet_name" class="col-md-3 col-form-label text-right">Hoja de cálculo</label>
                    <div class="col-md-9">
                        <input
                            type="text" name="sheet_name" class="form-control"
                            placeholder="Nombre de la hoja de cálculo" title="Nombre de la hoja de cálculo"
                            required value="<?= $sheet_name ?>"
                            >
                    </div>
                </div>
            
                <div class="form-group row">
                    <div class="col-md-9 offset-md-3">
                        <button class="btn btn-primary w120p">Importar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5>¿En qué consiste este proceso?</h5>
            <p>
                <?= $help_note ?>
            </p>

            <h5>Instrucciones para importar datos con archivo Excel</h5>
            <ul>
                <li>El tipo de archivo requerido es <b class="text-success">Excel (.xlsx)</b>.</li>
                <li>Verifique que el primer registro esté ubicado en la <b class="text-success">fila 2</b> de la hoja de cálculo.</li>
                <?php foreach($help_tips as $tip) : ?>
                    <li>
                        <?= $tip ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            
            <h5>Descargue el formato ejemplo</h5>
            <a href="<?= $url_file ?>" class="btn btn-success">
                <i class="fa fa-download"></i>
                <?= $template_file_name ?>
            </a>

        </div>
    </div>
</div>