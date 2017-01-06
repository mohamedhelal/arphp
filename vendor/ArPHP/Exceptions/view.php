<?php
/**
 * -----------------------------------
 * File  : view.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $this->getTitle($e);?></title>
    <style>
        body{
            background: #f5f5f5;
        }
        #error{
            width: 80%;
            margin:30px auto auto auto;
        }
        #message{
            background: #fff;
            padding:1em 1em 1em 20px;
            border: 1px solid #dfdfdf;
            border-radius: 5px 5px 0 0;
            text-transform: capitalize;
        }
        #query{
            background: #fcffd0;
            padding:1em 1em 1em 20px;
            border: 1px solid #dfdfdf;
            border-top: none ;
            border-radius:0 0 5px 5px;
        }
        #Trace{
            margin-top: 10px;
            background: #fff;
            border: 1px solid #dfdfdf;
            border-radius: 5px;
        }
        #Trace > div{
            background: #eee;
            padding: 1em;
        }
        #Trace > div i {
            margin-right: 20px;
            font-weight: bolder;
            color: #a30f15;
        }
        #Trace ul{
            list-style-type: decimal;
            margin:0 0 0 30px;
            padding:0;
        }
        #Trace ul li{
            padding: 5px 5px;
            font-size: 14px;
        }
        #Trace b{
           margin: 0 2px;
        }
        #Trace ul li i{
            color: #888;
        }
    </style>
</head>
<body>
<div id="error">
    <div id="message">
        <?php echo $e->getMessage();?>
    </div>
    <?php if($this->showTrace($e)){?>
    <div id="Trace">
        <div>
            <i><?php echo $this->getErrorType($e);?></i><?php echo str_replace(base_path(),'',$e->getFile());?> line <?php echo $e->getLine();?>
        </div>
        <ul>
        <li><?php echo implode('</li><li>',$this->getTrace($e));?></li>
        </ul>
    </div>
    <?php } ?>
</div>
</body>
</html>
