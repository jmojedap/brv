<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSS Grid Curso</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        body{
            height: 100vh;
        }

        .container-base{
            padding: 2px;
            border: 1px solid red;
        }

        .box{
            background-color: lime;
            padding: 1em;
            border: 1px solid blue;
        }
        
        /* Pantallas pequeñas */
        @media (max-width: 730px) {
            .container-base { 
                color: red;
            }
        }
    </style>
</head>
<body>
    <div class="container-base">
        <div class="box">SOY UNA CAJA BOX 1</div>
        <div class="box">SOY UNA CAJA BOX 2</div>
        <div class="box">SOY UNA CAJA BOX 3</div>
        <div class="box">SOY UNA CAJA BOX 4</div>
        <div class="box">SOY UNA CAJA BOX 5</div>
    </div>
</body>
</html>