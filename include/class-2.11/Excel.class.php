<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\Spreadsheet\Cell;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;  

class Excel extends BaseClass{
  
   function __construct($filename="export.xlsx"){
		
		parent::__construct();
       
       /* $this->spreadsheetRespon = null;
        $this->writerRespon = null; */
        $this->filenameRespon = $filename;
		$this->uploadFolder = 'export/'; 

        $this->spreadsheetRespon = new Spreadsheet();
        $this->writerRespon = new Xlsx($this->spreadsheetRespon);

        $this->activeSheet = $this->spreadsheetRespon->getActiveSheet();
        $this->subtotalAddNamedRange  = 'subtotalCell';
        $this->subtotalColumn = array();
   }
                                    
  function exportToSave($arrTemplate, $arrExportParam, $companyInformation = array(), $fileDataName = ''){ 
       
        $fullpath = $this->uploadTempDoc . $this->uploadFolder; 
        if (!is_dir($fullpath)) 
            mkdir($fullpath, 0755, true); 

        $exportType = $arrExportParam['exportType'];
        $module = $arrExportParam['module'];

        $headerDataStructure = $arrTemplate[0]['dataStructure']; 

        $rsData =  $arrTemplate[0]['dataToExport'];
        $reportTitle = str_replace('&','',$arrTemplate[0]['reportTitle']); 

        $companyName = (isset($companyInformation['name'])) ? $companyInformation['name'] : $this->loadSetting('companyName');


        // kalo sdh ad fielnya langsung download saja
        if(!empty($fileDataName)){
 
               switch($exportType){
                  case 2 :
                      $fileName = $module.'_'.$fileDataName.'.xlsx';  
                      break;
                  default :
                      $fileName = $reportTitle.'_'.$fileDataName.'.xlsx'; 
               }

                //$fullpath .= $fileName; / /gk boelh diconcat disini, nanti dibawah error
                if (file_exists($fullpath.$fileName)){ 
                    header('location:'.HTTP_HOST.'download.php?temp=1&filename='.$this->uploadFolder.$fileName); 
                    die;   
                }  
        }


      
      $this->activeSheet->setTitle(substr($reportTitle,0,31));
      $this->activeSheet->setShowGridlines(false);
      
      $mergeColIndex = 0;
      $totalLevel = count($arrTemplate); 
      
      // get highest col
      $headerTotalCol = count($arrTemplate[0]['dataStructure']);
      $highestCol = 1;
      for($i=0;$i<count($arrTemplate);$i++){  
          $totalCol = count($arrTemplate[$i]['dataStructure']);
          if ($i > 0)   $totalCol++; // karena majuin 1 kolom di cell A gk dipake
            
          // tambahkan yg merge manual
          $totalManualMerge = 0;
          foreach ($arrTemplate[$i]['dataStructure'] as $el){  
              if (isset($el['mergeExcelCell']) && !empty($el['mergeExcelCell']))
                  $totalManualMerge += ($el['mergeExcelCell'] - 1);
          } 
          
          if ($i==0)
              $headerTotalCol += $totalManualMerge;
          
          
          $totalCol += $totalManualMerge; 

          $highestCol = ($totalCol > $highestCol) ? $totalCol : $highestCol;
      }
        
      $highestColumnAlpha = $this->getColumnAlpha($highestCol);
      //$this->setLog($highestCol . ' => ' .$highestColumnAlpha);
         
      $totalCellMerge = $highestCol - $headerTotalCol;
      
      // ======= WRITE HEADER
      
      $reportHeaderRows = 1;
      $tableHeaderRow = $reportHeaderRows;
      
      $filterInformation = '';
      $headerBackgroundColor = '666666';
      
      // Utk Template
      
      $this->spreadsheetRespon->getProperties()->setCreator("PT. Winn Teknologi Nusantara");
          
      $fileIndexName = (empty($fileDataName)) ? $this->userkey.time() : $fileDataName;
          
      switch($exportType){
          case 2 :
              $this->filenameRespon = $module.'_'.$fileIndexName.'.xlsx'; 
              $this->spreadsheetRespon->getProperties()->setKeywords($module);
              break;
          default :
              $this->filenameRespon = $reportTitle.'_'.$fileIndexName.'.xlsx';

              $this->activeSheet->setCellValueByColumnAndRow(1, $reportHeaderRows,$companyName);  
              $cell = $this->activeSheet->getStyle('A'.$reportHeaderRows); 
              $cell->getFont()->getColor()->setARGB('428bca');    
              $cell->getFont()->setSize('16'); 
              $this->activeSheet->mergeCells('A'.$reportHeaderRows.':'.$highestColumnAlpha.$reportHeaderRows);
              $reportHeaderRows++;

              $this->activeSheet->setCellValueByColumnAndRow(1, $reportHeaderRows,$reportTitle);  
              $cell = $this->activeSheet->getStyle('A'.$reportHeaderRows);   
              $cell->getFont()->setSize('16'); 
              $this->activeSheet->mergeCells('A'.$reportHeaderRows.':'.$highestColumnAlpha.$reportHeaderRows);
              $reportHeaderRows++;


              // FILTER INFORMATION
              $filterInformation = $arrTemplate[0]['filterInformation'] ;
              foreach ($filterInformation as $item){
                  $this->activeSheet->setCellValueByColumnAndRow(1, $reportHeaderRows,$item['label'] . ': ' . $item['filter']);  
                  $cell = $this->activeSheet->getStyle('A'.$reportHeaderRows);    
                  $this->activeSheet->mergeCells('A'.$reportHeaderRows.':'.$highestColumnAlpha.$reportHeaderRows);
                  $reportHeaderRows++;
              }

              $tableHeaderRow = $reportHeaderRows + 1;
              
              $headerBackgroundColor = '428bca';
      }
     
      
      $firstRow = $tableHeaderRow + 1;
      
      // ======= WRITE TABLE HEADER  
      $col=1;
      $headerRowTotalMerge = 0 ;
      foreach ($headerDataStructure as $el){   
        $colToWrite = $col;
        $colToWrite = (!empty($mergeColIndex) && $colToWrite > $mergeColIndex)  ?  $colToWrite + $totalCellMerge : $colToWrite;  
        $colToWrite +=   $headerRowTotalMerge;
           
        $this->mergeCellIfNeeded($el, $colToWrite,$tableHeaderRow, $headerRowTotalMerge); 
          
        // merge kolom agar rapi
        if ($totalCellMerge > 0 && !isset($el['width'])){ 
             $mergeColIndex = $colToWrite;
             $columnAlpha = $this->getColumnAlpha($colToWrite); 
             $columnToAlpha = $this->getColumnAlpha($colToWrite+$totalCellMerge);  
              
             $this->activeSheet->mergeCells($columnAlpha.$tableHeaderRow.':'.$columnToAlpha.$tableHeaderRow); 
        }
            
        $this->activeSheet->setCellValueByColumnAndRow($colToWrite, $tableHeaderRow, strip_tags(str_replace('<br>',chr(13),$el['title']))); 
        $col++;
      }   
      
      // ======= WRITE TABLE ROWS
      $runningRows = $firstRow;
      for($i=0;$i<count($rsData);$i++){  
          
          for($level=0;$level<$totalLevel;$level++){ 
             $eachLevelData = $rsData[$i][$level];  // level 0,1,2 utk menunjukan header / detail
            
             $dataStructure = $arrTemplate[$level]['dataStructure'];
             $arrSubtotal = $arrTemplate[$level]['total'];
               
             //$structureKey = array_keys($dataStructure); 
            
             $col = 2;
             if ($level > 0) {   
                   // TABLE HEADER UTK detail
                 
                   // utk space antara detail dan header
                   $this->insertRowForSpace($runningRows, $level); 
                 
                  $detailRowTotalMerge = 0 ;
                  foreach ($dataStructure as $el){ 
                    $colToWrite = $col + $detailRowTotalMerge;  
                      
                    $columnAlpha = $this->getColumnAlpha($colToWrite); 
                      
                    $this->mergeCellIfNeeded($el, $colToWrite,$runningRows, $detailRowTotalMerge); 
                           
                    $this->activeSheet->setCellValueByColumnAndRow($colToWrite, $runningRows, $el['title']);   
                       
                    $cell = $this->activeSheet->getStyle($columnAlpha.$runningRows);
                    $el['bold'] = true;
                    $this->formatCell($cell,$el);
                    $col++;
                  }
                 
                  $this->activeSheet->GetRowDimension($runningRows)->setOutlineLevel($level);
                  $runningRows++;
              }else{
                 
                  // table rows
                 
                 if ($totalCellMerge > 0){
                      // merge kolom agar rapi
                      // cari yg width nya !isset
                      $col =1 ;
                      foreach ($dataStructure as $el){
                          $colToWrite = $col;
                          
                          if (!isset($el['width'])){  
                             $columnAlpha = $this->getColumnAlpha($colToWrite); 
                             $columnToAlpha = $this->getColumnAlpha($colToWrite+$totalCellMerge);  
                             $this->activeSheet->mergeCells($columnAlpha.$runningRows.':'.$columnToAlpha.$runningRows);
                             break;
                          }
                          $col++;
                      }
                 }
             }
             // table header 
              
             $arrHighlightCell = array();
              
             $startDetailRow = $runningRows; 
             for($j=0;$j<count($eachLevelData);$j++){ 
                 $datarow = $eachLevelData[$j];  
                 
                  $col=1;    
                  //if ($level > 0) $col++;  
                      
                  $detailRowTotalMerge = 0 ;
                  foreach ($datarow as $index=>$data){     
                        $colToWrite = ($level > 0) ? $col + 1 : $col;
                        $colToWrite += $detailRowTotalMerge;
                        $colToWrite = ($level == 0  && $colToWrite > $mergeColIndex) ? $colToWrite + $totalCellMerge : $colToWrite; 
                            
                        $columnAlpha = $this->getColumnAlpha($colToWrite); 
                      
                        $el =  $dataStructure[$index];
                       
                        $this->mergeCellIfNeeded($el, $colToWrite,$runningRows, $detailRowTotalMerge); 
                            
                        if (isset($el['format']) && ($el['format'] == 'date' || $el['format'] == 'datetime')) 
                            $data['excelValue'] = PhpOffice\PhpSpreadsheet\Shared\Date::stringToExcel($data['excelValue']);
                     
                        $el['textColor'] = (isset($data['excelStyle']['textColor'])) ? $data['excelStyle']['textColor'] : ''; 
                     
                        $pattern = '/<sup>(.+)<\/sup>/i';
                        if(preg_match($pattern, $data['excelValue'],$result)) {
                            //$this->setLog("test " . json_encode($result));
                             $objRichText =  new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                             $objCubed = $objRichText->createText(preg_replace('/<sup>(.+)<\/sup>/i', '', $data['excelValue']));
                             $objCubed = $objRichText->createTextRun($result[1]);
                            
                             if (!empty($el['textColor']))
                                $objCubed->getFont()->getColor()->setARGB($el['textColor']);  
                             
                             $objCubed->getFont()->setSuperScript(true);
                            
                             $data['excelValue'] = $objRichText; //preg_replace($pattern, '',$data['excelValue'] ) . $result[1];
                          }    
                      
                        // strips all HTML tag
                        $data['excelValue'] = strip_tags($data['excelValue']);
                          
                        // kalo ad cell validation
                        if(isset($el['validation']) && !empty($el['validation'])){
                            $validationList = implode(',',$el['validation']);
                             
                            $validation = $this->activeSheet->getCell($columnAlpha.$runningRows)->getDataValidation();
                            $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST );
                            $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP );
                            $validation->setAllowBlank(false);
                            $validation->setShowInputMessage(true);
                            $validation->setShowErrorMessage(true);
                            $validation->setShowDropDown(true);
                            $validation->setErrorTitle('Input error');
                            $validation->setError('Value is not in list.');
                            $validation->setPromptTitle('Pick from list');
                            $validation->setPrompt('Please pick a value from the drop-down list.');
                            $validation->setFormula1('"'.$validationList.'"');
                            
                        }
                      
                        $this->activeSheet->setCellValueByColumnAndRow($colToWrite, $runningRows, $data['excelValue']);  
                      
                        // kalo field ini akan dijumlahkan, dan level outline 0, kasi predefinename utk sum
                        if ($level == 0 && isset($el['calculateTotal']) && $el['calculateTotal']){ 
                            if (!isset($this->subtotalColumn[$level][$col]))
                                $this->subtotalColumn[$level][$col] = array();
                                
                            array_push($this->subtotalColumn[$level][$col],$columnAlpha.$runningRows);
                        }
                      
                        // FORMAT CELL ROWS
                        // harus per cell, karena kalo per kolom, problem kalo ad 2 level
                        $columnAlpha = $this->getColumnAlpha($colToWrite); 
                        $cellStyle = $this->activeSheet->getStyle($columnAlpha.$runningRows);  
                        $this->formatCell($cellStyle,$el); 
                       
                        // background highlight cell
                        $el['backgroundColor'] = (isset($data['excelStyle']['backgroundColor'])) ? $data['excelStyle']['backgroundColor'] : '';     
                        if (!empty($el['backgroundColor']))     
                            array_push($arrHighlightCell,array('cell' => $columnAlpha.$runningRows, 'backgroundColor' => $el['backgroundColor']));
                      
                      
                        $col++; 
                  }    
                     
                   if($level > 0){   
                        $this->activeSheet->GetRowDimension($runningRows)->setOutlineLevel($level);
                   } else{  
                       // baris jika ad header detail
                       if ($totalLevel > 1){ 
                           $color = 'f2f2f2'; 
                       }else{ 
                        // baris jika ad header, even odd  
                           $odd  = (!isset($odd)) ? 1 : $odd * -1;
                           $color = ($odd < 0 ) ? 'f2f2f2' : 'ffffff';
                       }
                       
                       $highlightCell = $this->activeSheet->getStyle('A'.$runningRows.':'.$highestColumnAlpha.$runningRows); //$columnAlpha otomatis sudah paling tinggi
                       $highlightCell->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($color);
                   }
                 
                   // overwrite background
                   // gk tau knp gk bisa pake cell style dr atas
                   foreach($arrHighlightCell as $cellRow) 
                       $this->activeSheet->getStyle($cellRow['cell'])->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($cellRow['backgroundColor']);
                   
                   $runningRows++;
                  
              }  
               
               // footer utk rows detail
              if ($level > 0){ 
                  if (!empty($eachLevelData)){
                       $col = 1;
                       $hasTotal = false;

                       $detailRowTotalMerge = 0;
                       foreach ($dataStructure as $el){     
                            $colToWrite = $col  + 1 + $detailRowTotalMerge;
                            $columnAlpha = $this->getColumnAlpha($colToWrite); 

                            $calculateTotal =  (isset($el['calculateTotal'])) ? $el['calculateTotal'] : false;   

                            $this->mergeCellIfNeeded($el, $colToWrite,$runningRows, $detailRowTotalMerge); 

                            if($calculateTotal){  
                                $columnAlpha = $this->getColumnAlpha($colToWrite); 
                                 
                                $this->activeSheet->setCellValueByColumnAndRow($colToWrite, $runningRows, '=sum('.$columnAlpha.$startDetailRow.':'.$columnAlpha.($runningRows-1).')'); 
                                 
                                $cellStyle = $this->activeSheet->getStyle($columnAlpha.$runningRows);
                                
                                //update decimal
                                
                                $format =  (isset($el['format'])) ? $el['format'] : '';   
                                if($format){
                                    switch($format){
                                            case 'decimal' : 
                                                    $format = '#,##0.00'; 
                                                    break;
                                            default : 
                                                    $format = '#,##0'; 
 
                                    }
                                }
                                        
                                $cellStyle->getNumberFormat()->setFormatCode($format); 

                                
                                $styleArray = [ 
                                        'font' => [
                                            'bold' => true,
                                        ],
                                        'borders' => [
                                            'top' => [
                                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                                'color' => ['argb' => 'dedede'],
                                            ],
                                        ],
                                ];
 
                                $cellStyle->applyFromArray($styleArray);

                                $hasTotal = true;
                            }

                            $col++;
                       }
                       if ($hasTotal){
                           $this->activeSheet->GetRowDimension($runningRows)->setOutlineLevel($level);
                           $runningRows++; 
                       }

                  }
                           
                   $this->insertRowForSpace($runningRows, $level); 
                   
              } 

          } 
      }
       
      
      // FORMAT HEADER and FOOTER
      if (!empty($rsData)){
            $col = 1;
            $detailRowTotalMerge =  0;
            foreach ($headerDataStructure as $el){    
                $colToWrite = (!empty($mergeColIndex) && $col > $mergeColIndex)  ?  $col + $totalCellMerge : $col;  
                $colToWrite += $detailRowTotalMerge;

                $calculateTotal =  (isset($el['calculateTotal'])) ? $el['calculateTotal'] : false;

                $columnAlpha = $this->getColumnAlpha($colToWrite); 
                $cellStyle = $this->activeSheet->getStyle($columnAlpha.$tableHeaderRow);
                 
                $this->formatCell($cellStyle,$el); 

                // =======  SET FOOTER TOTAL
                //$subtotalRows = $highestRow + 1;

                $this->mergeCellIfNeeded($el, $colToWrite,$runningRows, $detailRowTotalMerge); 

                if ($calculateTotal){ 
                    $cell = $this->activeSheet;
                    
                    $level = 0; // temp
                    $cellRange = $this->subtotalAddNamedRange.$col; 
                    $cell->setCellValueByColumnAndRow($colToWrite, $runningRows, '=sum('.$cellRange.')');

                    $cellStyle = $cell->getStyle($columnAlpha.$runningRows);
                    $cellStyle->getNumberFormat()->setFormatCode('#,##0');   
                    $cellStyle->getFont()->setBold(true); 
                }

                $this->activeSheet->getColumnDimension($columnAlpha)->setAutoSize(true); 
                $col++;
            }
      }
        
      
        // set autosize for the rest, if detail has more columns
        for($i=$col;$i<=$highestCol;$i++){
           $columnAlpha = $this->getColumnAlpha($i);  
           $this->activeSheet->getColumnDimension($columnAlpha)->setAutoSize(true); 
        } 
        
      
        //  ======= STYLING  
      
        // SET TABLE HEADER STYLE  
        // problem, detail jg kefilter
        //$this->activeSheet->setAutoFilter('B'.$tableHeaderRow.':'.$highestColumnAlpha.$tableHeaderRow);
      
        // HEADER TABLE STYLE
        $styleArray = [ 
            'font' => [
                        'bold' => true,
                        'size' => 12,
                        'color' =>  ['argb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 
                'startColor' => [
                    'argb' => $headerBackgroundColor,
                ],
            ], 
        ]; 
      
        $cell = $this->activeSheet->getStyle('A'.$tableHeaderRow.':'.$highestColumnAlpha.$tableHeaderRow);
        $cell->applyFromArray($styleArray);  
        $this->activeSheet->freezePane("A".$firstRow);
       
      
        // set rest of style
        $styleArray = [
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                'wrapText' => true
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '999999'],
                ],
            ],
        ]; 
        $cell = $this->activeSheet->getStyle('A'.$tableHeaderRow.':'.$highestColumnAlpha.($runningRows-1));
        $cell->applyFromArray($styleArray);

        $level = 0; // temporary
      
        if (isset($this->subtotalColumn[$level])){
            foreach ($this->subtotalColumn[$level] as $key=>$col){

                $cellRange = ($totalLevel > 1) ? implode(',',$col) : $col[0] .':'.$col[count($col)-1];  

                //$this->setLog($cellRange);
                $this->spreadsheetRespon->addNamedRange( new \PhpOffice\PhpSpreadsheet\NamedRange($this->subtotalAddNamedRange.$key, $this->spreadsheetRespon->getActiveSheet(), $cellRange) );
            }
        }
      
        $fullpath .= $this->filenameRespon;

        $this->writerRespon->save($fullpath); 
        header('location:'.HTTP_HOST.'download.php?temp=1&filename='.$this->uploadFolder.$this->filenameRespon); 
        die; 
  }
    
    function mergeCellIfNeeded($el, $colToWrite,$runningRows, &$totalMerge){
        
        $columnAlpha = $this->getColumnAlpha($colToWrite); 
        
        if (isset($el['mergeExcelCell']) && !empty($el['mergeExcelCell'])){
             //$this->setLog('merge ' . $el['mergeExcelCell']);
             $el['mergeExcelCell']--; 
             $columnToAlpha = $this->getColumnAlpha($colToWrite+$el['mergeExcelCell']);  
             $this->activeSheet->mergeCells($columnAlpha.$runningRows.':'.$columnToAlpha.$runningRows);
             $totalMerge += $el['mergeExcelCell'];
        } 
    }
    
    function formatCell($cellStyle,$el=array()){
        $align =  (isset($el['align'])) ? $el['align'] : '';
        $format =  (isset($el['format'])) ? $el['format'] : ''; 
        
        if($format){
            switch($format){
                    case 'accounting' :
                    case 'number' : $cellStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT); 
                                    $cellStyle->getNumberFormat()->setFormatCode('#,##0'); 
                                    break; 
                    case 'decimal' : $cellStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT); 
                                    $cellStyle->getNumberFormat()->setFormatCode('#,##0.00'); 
                                    break;
                    case 'date' :   $cellStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); 
                                    $cellStyle->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH); 
                                    break;
                    case 'datetime' : $cellStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); 
                                    $cellStyle->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DATETIME); 
                                    break;
                    // beberapa seperti field 'code' yg angka semua, harus diperlakukan sebagai string
                    case 'string' : $cellStyle->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT );
                                    break;
            }
        }

        if(!empty($align)){
            switch($align){
                    case 'right' : $cellStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT); 
                                   break; 
                    case 'center' : $cellStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); 
                                   break; 
            }
        }
        
        if(isset($el['indent']))
            $cellStyle->getAlignment()->setIndent($el['indent']);
            
        $cellFont = $cellStyle->getFont();
        
        if (isset($el['bold']) &&  $el['bold'])  
            $cellFont->setBold(true);  
        
        if (isset($el['textColor']) && !empty($el['textColor'])) { 
            // $this->setLog('test ' . $el['textColor']);
             $cellFont->getColor()->setARGB($el['textColor']);  
        }
    }
    
    function getColumnAlpha($colIndex){
   
        $alphas = range('A', 'Z');  
        
        if ($colIndex <= 26){
            return $alphas[$colIndex -1];
        }else{ 
            $firstLetterIndex = floor($colIndex / 26);
            $lastLetterIndex = $colIndex % 26;
             
            return $alphas[$firstLetterIndex-1] . $alphas[$lastLetterIndex-1];
        }
         
    }
    
    function insertRowForSpace(&$runningRows, $level ,$rowHeight = '10'){
        $cell = $this->activeSheet->GetRowDimension($runningRows);
        $cell->setOutlineLevel($level);
        $cell->setRowHeight($rowHeight);
        $runningRows++; 
    }
}

?>