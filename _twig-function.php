<?php  
 
$URLfilter = new Twig_SimpleFilter('urlfilter', function ($string) {
    global $class;
	$string = str_replace($class->arrSearch,$class->arrReplace,$string);
	$string = strtolower($string);
	
	return $string;
});
$twig->addFilter($URLfilter);
 
$URLfilter = new Twig_SimpleFilter('format_date', function ($string) {
    global $class; 
	return  (empty($string ) || $string == '1970-01-01 00:00:00' || $string == '1970-01-01') ? '00 / 00 / 0000' : $class->formatDBDate($string);
});
$twig->addFilter($URLfilter);


$url_decode = new Twig_SimpleFilter('url_decode', function ($string) {
	return urldecode($string);
});
$twig->addFilter($url_decode);
  

$ucwords = new Twig_SimpleFilter('ucwords', function ($string) {
	return ucwords(strtolower($string));
});
$twig->addFilter($ucwords);
  
$ucwords = new Twig_SimpleFilter('ucfirst', function ($string) {
	return ucfirst(strtolower($string));
});
$twig->addFilter($ucwords);
  

$WAfilter = new Twig_SimpleFilter('wafilter', function ($string) {
    global $class;
	$string = str_replace(' ','',$string);
	$string = str_replace('-','',$string);
	$string = ltrim($string, "0");
	$string = ltrim($string, "+62");
	$string = ltrim($string, "+");
	$string = '62'.$string;
	$string = strtolower($string);
	
	return $string;
});
$twig->addFilter($WAfilter);
 

$function = new Twig_SimpleFunction('generate_report_row_attr', function ($dataStructure, $currKey,$order) { 
    
    global $class;
    
    $el = $dataStructure[$currKey];
    $width = (isset($el['width']) && !empty($el['width']))  ? 'width:'.$el['width'].';' : '';
    
    $align = '';
    if (isset($el['format'])){
        switch($el['format']){
            case 'integer' : 
            case 'number' : 
            case 'accounting' : 
            case 'autodecimal' : 
            case 'decimal' : $align = "text-align:right;" ;
                             break;
            case 'date' : $align = "text-align:center;" ;
                             break;
            case 'datetime' : $align = "text-align:center;" ;
                             break;
        } 
    }
                     
    $align = (isset($el['align']) && !empty($el['align'])) ? 'text-align:'.$el['align'].';' : $align; 
    
    $styleHTML = $width.' '.$align ;
    
         
    $orderType = -1;
    $orderIcon = '';
    $sortableActive = '';
    /*$updateOrderScript = '';*/
    
    $sortable = (isset($el['sortable']) && !$el['sortable']) ?  '' : 'sortable' ;  
                          
    if(!empty($sortable)){
         
        if ($el['dbfield'] == $order['orderBy']){
            $sortableActive = 'sortable-active';
            $orderType = $order['orderType'] * -1;
            $arrowIcon = ($order['orderType'] == 1) ? 'arrow-down' : 'arrow-up';
            $orderIcon = '<div class="order-type ' . $arrowIcon . '" style="display:inline"></div>';
            
            /*$updateOrderScript = '<script>
                            jQuery(document).ready(function(){        
                                $("[name=hidOrderBy]").val(\''.$order['orderBy'].'\'); 
                                $("[name=hidOrderType]").val(\''.$order['orderType'].'\'); 
                            })
                        </script>';*/
        }
    } 
    
    // kalo group
    $groupBorderL = '';
    if (isset($el['group'])){
        // kalo kolom pertama
        $groupName = '';
        foreach($dataStructure as $key=>$row){
            if (!isset($row['group']))
                continue;
            
            if($key == $currKey){
                $groupBorderL = ($row['group'] != $groupName) ?  'header-group-bl' : '';
                break;
            }   
                
            $groupName = $row['group'];
        }
     
    }
    
    $classHTML =  $sortable.' '.$sortableActive. ' ' .$groupBorderL;  
    
 
    return array('style' => $styleHTML, 'class' => $classHTML, 'orderType' => $orderType,  'orderIcon' => $orderIcon) ;
});
$twig->addFunction($function);


$function = new Twig_SimpleFunction('calculate_total_col_width', function ($dataStructure) { 
      
    $totalWidth = 0;
  
    foreach($dataStructure as $key=>$row){ 
       $ittrWidth = str_replace('px','', (isset($row['width'])) ? $row['width'] : 0); 
       $hidWidth = str_replace('px','', (isset($row['hidWidth'])) ? $row['hidWidth'] : 0); 
        
            
       $totalWidth += $ittrWidth + $hidWidth;
    }
    
    $totalWidth += 50;
    
    return $totalWidth ;
});
$twig->addFunction($function); 

$function = new Twig_SimpleFunction('generate_report_group_header_attr', function ($dataStructure,$currKey) { 
    global $class;
    
    $prevGroup = '';
    
    $firstOfGroup = false;
    foreach($dataStructure as $key=>$row){
        $ittrGroup = (isset($row['group'])) ? $row['group'] : ''; 
        if ($key == $currKey && $prevGroup != $ittrGroup){
            $firstOfGroup = true;
            break;
        }
        $prevGroup = $ittrGroup;
    }
    
    
    $totalWidth = 0;
    $colspan=0;
    $startCounting = false;
    $currGroup = $dataStructure[$currKey]['group'];
    
    foreach($dataStructure as $key=>$row){
       $ittrGroup = (isset($row['group'])) ? $row['group'] : '';
       $ittrWidth = str_replace('px','', (isset($row['width'])) ? $row['width'] : 0); 
        
       if ($ittrGroup == $currGroup){
           $startCounting = true;
           $colspan++;
           $totalWidth += $ittrWidth;
       }elseif ($startCounting){
           break;
       }
    }
    
    // margin
    $margin = $colspan * 2 * 4 ;
    $totalWidth += $margin + 100;
    
    //$class->setLog($colspan);
    return array('firstOfGroup' => $firstOfGroup, 'totalWidth' => 'width:'.$totalWidth.'px', 'colspan' => (!empty($colspan)) ? 'colspan="'.$colspan.'"' : ''  ) ;
});
$twig->addFunction($function); 
 
 
$function = new Twig_SimpleFunction('propotional_size', function ($width,$height,$maxWidth,$maxHeight) {  
    $diffWidth = $width - $maxWidth;
    $diffHeight = $height - $maxHeight;
     
    if($diffWidth < 0 && $diffHeight < 0)
        return  array('width' => $width, 'height' => $height) ;
        
    $useWidth = ($diffWidth > $diffHeight) ? true : false; 
    
    $perc = ($useWidth) ? $diffWidth / $width * 100 : $diffHeight / $height * 100;
     
    $newWidth = $width - ($perc / 100 * $width);
    $newHeight = $height - ($perc / 100 * $height);
    
    return array('width' => $newWidth, 'height' => $newHeight) ;
});
$twig->addFunction($function); 
?>