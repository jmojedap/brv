<?php
    //$arr_respuesta_pol = json_decode($row_meta->valor, TRUE);
    
    $destacadas = array(
        'valor',
        'mensaje_respuesta_pol',
        'firma',
        'codigo_respuesta_pol',
    );
    
    $cl_alert = 'alert alert-info';
    $message = 'No se ha registrado confirmación de PayU';
    
    if ( $confirmations->num_rows() > 0 )
    {
        $message = $last_confirmation->mensaje_respuesta_pol;
        
        switch ( $arr_respuesta_pol['codigo_respuesta_pol'] ) {
            case '1':
                $cl_alert = 'alert alert-success';
                break;
            default:
                $cl_alert = 'alert alert-warning';
                break;
        }
    }
    
    //Validaciones
        //Firma de Confirmación
    
        $clase_firma = 'table-success';
        $texto_firma = '<i class="fa fa-check"></i> Ok';
        if ( $arr_respuesta_pol['firma'] != $firma_pol_confirmacion )
        {
            $clase_firma = 'text-danger';
            $texto_firma = '<i class="fa fa-times"></i> Error';
        }
        
        //Valor de la transacción
    
    $cl_detalle = ( $confirmations->num_rows() > 0 ) ? '' : 'd-none' ;

        
?>

<div class="<?= $cl_alert ?>" role="alert">
    <i class="fa fa-info-circle"></i> <?= $message ?>
</div>

<div class="row <?= $cl_detalle ?>">
    <div class="col col-md-6">
        <div class="card mb-2">
            <div class="card-header">
                Resumen Respuesta PayU
            </div>
            <?php if ( count($arr_respuesta_pol) > 0 ) { ?>
                <table class="table table-condensed">
                    <thead>
                        <th>Variable</th>
                        <th>Valor</th>
                    </thead>

                    <tbody>
                        <tr>
                            <td>Respuesta</td>
                            <td><?= $arr_respuesta_pol['mensaje_respuesta_pol'] ?></td>
                        </tr>
                        <tr class="<?= $clase_firma ?>">
                            <td>Firma POL Respuesta</td>
                            <td>
                                Firma Recibida: <?= $arr_respuesta_pol['firma'] ?><br/>
                                Firma Generada: <?= $firma_pol_confirmacion ?><br/>
                                <?= $texto_firma ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Valor</td>
                            <td>
                                <div class="row">
                                    <div class="col col-sm-6">
                                        Valor en PayU:
                                    </div>
                                    <div class="col col-sm-6">
                                        <?= $arr_respuesta_pol['valor'] ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col col-sm-6">
                                        Valor en DC:
                                    </div>
                                    <div class="col col-sm-6">
                                        <?= number_format($row->valor_total,2,'.','') ?>
                                    </div>
                                </div>

                                
                            </td>
                        </tr>
                        <tr class="">
                            <td>Estado POL</td>
                            <td>
                                <?= $this->App_model->nombre_item($arr_respuesta_pol['estado_pol'], 1, 9) ?>
                            </td>
                        </tr>
                        <tr class="">
                            <td>Tipo medio de pago</td>
                            <td>
                                <?= $this->App_model->nombre_item($arr_respuesta_pol['tipo_medio_pago'], 1, 11) ?>
                            </td>
                        </tr>
                        <tr class="">
                            <td>Medio de pago</td>
                            <td>
                                <?= $this->App_model->nombre_item($arr_respuesta_pol['medio_pago'], 1, 12) ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            <?php } ?>
        </div>

        <div class="card">
            <div class="card-header">
                Detalle Respuesta PayU
            </div>
            <?php if ( count($arr_respuesta_pol) > 0 ) { ?>
                <table class="table table-condensed">
                    <thead>
                        <th class="">Variable</th>
                        <th class="">Valor</th>
                    </thead>

                    <tbody>
                        <?php foreach ($arr_respuesta_pol as $key => $valor_campo) : ?>
                            <?php
                                $clase_fila = '';
                                if (in_array($key, $destacadas) ) { $clase_fila = 'table-info'; }
                            ?>
                            <tr class="<?= $clase_fila ?>">
                                <td><?= $key ?></td>
                                <td><?= $valor_campo ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            <?php } ?>
        </div>
    </div>
    <div class="col col-md-6">
        <div class="card">
            <div class="card-header">
                Confirmaciones PayU <i class="fa fa-caret-right"></i> <span class="badge badge-primary"><?= $confirmations->num_rows() ?></span>
            </div>
            <table class="table table-condensed">
                <thead>
                    <th>ID</th>
                    <th>Detalle</th>
                </thead>

                <tbody>
                    <?php foreach ($confirmations->result() as $row_confirmation) : ?>
                        <?php
                            $arr_confirmation = json_decode($row_meta->valor, TRUE);
                        ?>
                        <tr class="">
                            <td><?= $row_confirmation->id ?></td>
                            <td>
                                <span class="text-muted">Creada: </span>
                                <span class="text-primary" title="<?= $row_confirmation->created_at ?>"><?= $this->pml->ago($row_confirmation->created_at) ?></span>
                                &middot;
                                <span class="text-muted">Respuesta PayU:</span>
                                <strong class="text-primary"><?= $arr_confirmation['mensaje_respuesta_pol'] ?></strong>
                                &middot;
                                <span class="text-muted">Valor:</span>
                                <strong class="text-primary"><?= $this->pml->money($arr_confirmation['valor']) ?></strong>
                                &middot;
                                <span class="text-muted">Ref. Venta:</span>
                                <strong class="text-primary"><?= $arr_confirmation['ref_venta'] ?></strong>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

