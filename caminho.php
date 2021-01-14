<?php
// Create a temporary file in the temporary 
// files directory using sys_get_temp_dir()
/* 
$temp_file = tempnam(sys_get_temp_dir(), 'ArquivoPDF');

echo $temp_file;
echo "<br>";
$temp = tmpfile();
fwrite($temp, "escrevendo no arquivo temporario");
fseek($temp, 0);
echo fread($temp, 1024);
fclose($temp); // isto remove o arquivo
 * 
 */

//$tmpfname = tempnam (sys_get_temp_dir(), "ArquivoPDF");
//$tmpfname = sys_get_temp_dir()."\ArquivoPDF.PDF";
//echo $tmpfname; 

//$handle = fopen($tmpfname, "w");
//fwrite($handle, "writing to tempfile");
//fclose($handle);

// fazer alguma coisa

//unlink($tmpfname);



$tmpfname = tempnam ("/tmp", "ArquivoTeste");

$handle = fopen($tmpfname, "w");
fwrite($handle, "writing to tempfile");
fclose($handle);

// fazer alguma coisa

///unlink($tmpfname);

?>