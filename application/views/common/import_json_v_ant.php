<?php
    $att_form = array(
        'class' => 'form-horizontal'
    );
?>

<?php if ( isset($vista_menu) ) { ?>
    <?php $this->load->view($vista_menu); ?>
<?php } ?>

<?php if ( isset($vista_submenu) ) { ?>
    <?php $this->load->view($vista_submenu); ?>
<?php } ?>

<div class="row">
    <div class="col col-md-7">
        <div class="card">
            <div class="card-body">      
                <?= form_open_multipart($destino_form, $att_form) ?>
                    <div class="form-group row">
                        <label for="archivo" class="col-sm-2 control-label">Archivo</label>
                        <div class="col-sm-10">
                            <input type="file" class="form-control" name="json_file" accept="application/json" required>
                        </div>
                    </div>
                
                    <div class="form-group row">
                        <div class="col-md-10 offset-md-2">
                            <button class="btn btn-primary btn-lg" type="submit">Importar</button>
                        </div>
                    </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
    
    <div class="col col-md-5">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title"><?= $titulo_ayuda ?></h4>
                <p>
                    <?= $nota_ayuda ?>
                </p>

                <h4>Instrucciones para importar datos desde archivo MS Excel</h4>
                <ul>
                    <li>El tipo de archivo requerido es: <span class="resaltar">MS Excel 97-2003 (.xls) o 2007 (.xlsx)</span>.</li>
                    <li>El nombre de la hoja de cálculo dentro del archivo, de la cual se tomarán los datos, no puede contener caracteres con tildes ni letras ñ.</li>
                    <li>Verifique el el primer registro esté ubicado en la <span class="label label-success">fila 2</span> de la hoja de cálculo.</li>
                    <?php foreach($parrafos_ayuda as $parrafo) : ?>
                        <li>
                            <?= $parrafo ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                
                <h4>Descargue el formato ejemplo</h4>
                <?= anchor($url_archivo, '<i class="fa fa-download"></i> ' . $nombre_archivo, 'class="btn btn-success" title="Descargar formato"') ?>
            </div>
        </div>
    </div>
    
</div>