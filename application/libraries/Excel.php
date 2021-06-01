<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Excel {

    /**
     * Convierte un listado de una hoja de cálculo en un array
     * Desde la columna A y la fila 2
     * 
     * @param type $file
     * @param type $sheet_name
     * @return type
     */
    public function get_array($file, $sheet_name)
    {
        //Valor inicial
        $data = array('status' => 0, 'arr_sheet' => array(), 'message' => 'Se presentó un error al leer el archivo');
        
        //Cargando archivo
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setLoadSheetsOnly($sheet_name);
        $spreadsheet = $reader->load($file);
        $worksheet = $spreadsheet->getActiveSheet();
        
        if ( ! is_null($worksheet) )
        {
            $data['status'] = 1;
            
            $end_column = $worksheet->getHighestColumn();  //Última columna con datos
            $end_row = $worksheet->getHighestRow();        //Última fila con datos
            $range = "A2:{$end_column}{$end_row}";
            
            $data['arr_sheet'] = $worksheet->rangeToArray($range, NULL, TRUE, FALSE);
            $data['message'] = 'Filas encontradas: ' . intval($end_row - 1);
        }
        
        return $data;
    }

    public function arr_sheet_default($sheet_name)
    {
        $file = $_FILES['file']['tmp_name'];             //Se crea un archivo temporal, no se sube al servidor, se toma el nombre temporal
        $data = $this->get_array($file, $sheet_name);
        
        return $data;
    }
    
    public function archivo_query($datos)
    {
        $spreadsheet = new Spreadsheet();

        // Establecer propiedadess del documento
        $spreadsheet->getProperties()
            ->setCreator('Pacarina Media Lab')
            ->setLastModifiedBy('Pacarina Media Lab')
            ->setTitle($datos['sheet_name']);

        //Encabezados
            $campos = $datos['query']->list_fields();
            foreach ( $campos as $key => $campo ) 
            {
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($key + 1, 1, $campo);
            }
        
        //Valores
            $fila = 2;
            foreach ( $datos['query']->result() as $row ) 
            {
                foreach ( $campos as $key => $campo ) {
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($key + 1, $fila, $row->$campo);
                }
                $fila++;
            }

        // Establecer nombre a worksheet
        $spreadsheet->getActiveSheet()
            ->setTitle($datos['sheet_name']);

        // Objeto para crear archivo y guardar
        $writer = new Xlsx($spreadsheet);
        return $writer;
    }
}