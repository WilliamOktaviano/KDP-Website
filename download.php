<?php

include '_config.php';  
 
$path = DOC_ROOT.'log/';  
$logfilename = 'download'; 
$logfilename = '['.date('d-m-Y') .'] - '.$logfilename.'.txt'; 
$logfilename = $path.$logfilename;  

//error_log ('download => '.$_SERVER['REMOTE_ADDR'].chr(13),3,$filename);

$filename = $_GET['filename'];
//error_log ('$filename '.$filename.chr(13),3,$logfilename);
if (empty($filename))
    die;

$path = (isset($_GET) & !empty($_GET['temp'])) ?  UPLOAD_TEMP_DOC : DEFAULT_DOC_UPLOAD_PATH ;  
$path .= $filename; 
   
//error_log ('$path '.$path.chr(13),3,$logfilename);

if(!file_exists($path) || !is_file($path)) die; 
 
//error_log ('$path ok '.chr(13),3,$logfilename);

$filename = basename($filename); 
     
$file_extension = strtolower(substr(strrchr($filename,"."),1));
 
$cdisposition = '';

switch( $file_extension ) {
    case "gif": $ctype="image/gif"; break;
    case "png": $ctype="image/png"; break;
    case "jpeg":
    case "jpg": $ctype="image/jpeg"; break;
    case "svg": $ctype="image/svg+xml"; break;
    case "pdf": $ctype="application/pdf";
                $cdisposition = "inline;";
                break;
    default: $ctype="application/force-download";
               $cdisposition = "attachment;";
               break;
}

header('Content-type: ' . $ctype);
if (!empty($cdisposition))
     header('Content-Disposition: '.$cdisposition.'; filename="'.$filename.'"');
  
// ini kayanya masalah kalo pake kompresi
header('Content-Length: ' . filesize($path)); 
readfile($path);
 
/*
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
ob_clean();
flush();
readfile($path);
exit;
*/
 
?>