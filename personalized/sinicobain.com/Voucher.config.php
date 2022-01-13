<?php

$this->arrDataListAvailableColumn = array(); 
array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 80));
array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 200));        
array_push($this->arrDataListAvailableColumn, array('code' => 'value','title' => 'value','dbfield' => 'value','default'=>true,'align'=>'right', 'format' => 'number', 'width' => 100));   
array_push($this->arrDataListAvailableColumn, array('code' => 'point','title' => 'point','dbfield' => 'pointneeded','default'=>true,'align'=>'right', 'format' => 'number', 'width' => 80));        
array_push($this->arrDataListAvailableColumn, array('code' => 'note','title' => 'note','dbfield' => 'trdesc', 'width' => 200));
array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));

?>