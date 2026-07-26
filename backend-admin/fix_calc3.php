<?php
$lines = file('app/Services/PackagingCalculatorService.php');
file_put_contents('app/Services/PackagingCalculatorService.php', implode('', array_slice($lines, 0, 1037)) . "}\n");
