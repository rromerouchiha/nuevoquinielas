<?php

echo "
      <!DOCTYPE html>
      <html>
        <head>
            <title>Hora</title>
        </head>
        <body>
            <h1><p id='datos'></p></h1>
        </body>
      </html>
      <script>
        var d = new Date();
        alert(d);
        document.getElementById('datos').innerHTML = d;
      </script>
";
 $h = "3";// Hour for time zone goes here e.g. +7 or -4, just remove the + or -
$hm = $h * 60; 
$ms = $hm * 60;
$gmdate = gmdate("m/d/Y g:i:s A", time()-($ms)); // the "-" can be switched to a plus if that's what your time zone is.
echo "Your current time now is :  $gmdate . ";
echo "<br/><br/>";
$timezone  = -8; //(GMT -5:00) EST (U.S. & Canada) 
echo gmdate("Y/m/j H:i:s", time() + 3600*($timezone+date("I")));
echo "<br/>";
echo gmdate("Y/m/j", time() + 3600*($timezone+date("I")));
echo "<br/>";

$datos = timezone_identifiers_list ([ int $what = DateTimeZone::ALL [, string $country = NULL ]] )
print_r($datos);
$timezone_identifiers = DateTimeZone::listIdentifiers();
for ($i=0; $i < 10; $i++) {
    echo "$timezone_identifiers[$i]".'<br/>';
}

?>