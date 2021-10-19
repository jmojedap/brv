<?php
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename="' . $file_name . '"');
    echo $content;
    exit();