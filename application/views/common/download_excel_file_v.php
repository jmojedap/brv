<?php
header('Content-Type: application/vnd.ms-excel');   //mime type
header('Content-Disposition: attachment;filename="'. $file_name .'.xlsx"'); //Le dice al browser el nombre del archivo
header('Cache-Control: max-age=0'); //no cache

$obj_writer->save('php://output');