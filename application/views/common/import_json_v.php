<div class="row">
    <div class="col col-md-7">
        <div class="card">
            <div class="card-body">      
                <?= form_open_multipart($destination_form) ?>
                    <div class="form-group row">
                        <label for="file" class="col-md-3">Archivo</label>
                        <div class="col-md-9">
                            <input type="file" class="form-control" name="json_file" accept="application/json" required>
                        </div>
                    </div>
                
                    <div class="form-group row">
                        <div class="col-md-9 offset-md-3">
                            <button class="btn btn-primary">Importar</button>
                        </div>
                    </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
    
    <div class="col col-md-5">
        <div class="card">
            <div class="card-body">
                <h5>¿En qué consiste este proceso?</h5>
                <p>
                    <?= $help_note ?>
                </p>

                <h5>Instrucciones para importar datos con archivo JSON</h5>
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
                <a href="<?= $url_file ?>" class="btn btn-secondary">
                    <i class="fa fa-file"></i>
                    <?= $template_file_name ?>
                </a>

            </div>
        </div>
    </div>
    
</div>