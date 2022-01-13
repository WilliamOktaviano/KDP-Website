<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass(array('Article.class.php','Category.class.php','ArticleCategory.class.php'));  
$article = new Article();
$articleCategory = new ArticleCategory();

$pageIndex = ( isset($_GET) && !empty($_GET['page']) ) ?  $_GET['page'] : 0 ;
$categorykey = ( isset($_GET) && !empty($_GET['cat']) ) ?  $_GET['cat'] : 0 ;

$arrParentPath = array();
$cat = 0; 
$criteria = '';

if(isset($_GET) && !empty($_GET['cat'])){ 
    
    $cat = $_GET['cat'];
    $cat = explode(',',$cat);
    
    $criteria .= ' and ' . $article->tableNameDetail .'.categorykey in ('.$class->oDbCon->paramString($cat,',').')';
    
    // select ulang karena rs news bisa empty
    // asumsi ambil yg pertama utk path
    $rsCat = $articleCategory->getDataRowById($cat[0]); 
    //$categoryName = $rsCat[0]['name']; 
      
	$arrParentPath[0]['pkey'] = $rsCat[0]['pkey'];
	$arrParentPath[0]['name'] = $rsCat[0]['name']; 
	$parentkey = $rsCat[0]['parentkey'];
	 
	while($parentkey <> 0){ 
		$rsParent = $articleCategory->getDataRowById($parentkey); 
		$parentkey = $rsParent[0]['parentkey'];
		
		$ctr = count($arrParentPath);
		$arrParentPath[$ctr]['pkey'] =  $rsParent[0]['pkey'];
		$arrParentPath[$ctr]['name'] = $rsParent[0]['name']; 
	} 

}

// kalo ad tag 
if(isset($_GET) && !empty($_GET['tag']))
        $criteria .= ' and ' .$article->tableName.'.tag like '  .$class->oDbCon->paramString('%'.$_GET['tag'].'%') ;

$arrTwigVar['categoryPath'] = $arrParentPath; 
$arrTwigVar ['pageIndex'] =  $pageIndex;
 
$totalrowsperpage =  $class->loadSetting('newsTotalRowsPerPage');
if (empty($totalrowsperpage))  $totalrowsperpage = 10;

$now = $pageIndex * $totalrowsperpage;
$limit = ' limit ' . $now . ', ' . $totalrowsperpage;
     
$criteria .= ' and ' .$article->tableName.'.statuskey = 1 and publishdate <= now()' ;

  
$rsArticleCategory = $articleCategory->searchData($articleCategory->tableName.'.statuskey',1,true,'','order by '.$articleCategory->tableName.'.name asc');
$arrCategoryName = array_column($rsArticleCategory,'name','pkey');

$rsArticles = $article->searchDataWithCategory('','',true,$criteria,'order by '.$article->tableName.'.publishdate desc, title asc',$limit);

$arrArticleKey = array_column($rsArticles,'pkey');
$rsArticleCategoryCol = $article->getDetailWithRelatedInformation($arrArticleKey);
$rsArticleCategoryCol = array_column($rsArticleCategoryCol,null,'refkey');


for($i=0;$i<count($rsArticles);$i++){
    $rsArticles[$i]['publishDateISO8601'] =  date('c',strtotime($rsArticles[$i]['publishdate']));
    $rsArticles[$i]['modifiedDateISO8601'] =  date('c',strtotime($rsArticles[$i]['publishdate']));  
    $rsArticles[$i]['phpThumbHash'] =  getPHPThumbHash($rsArticles[$i]['file']);
    $rsArticles[$i]['categoryname'] =  $rsArticleCategoryCol[$rsArticles[$i]['pkey']]['categoryname'];
}

$totalPages = ceil( $article->getTotalRowsWithCategory($criteria) / $totalrowsperpage); 
 

$arrTwigVar ['rsArticleCategory'] =  $rsArticleCategory;
$arrTwigVar ['rsArticles'] =  $article->updateContentLang($rsArticles);
$arrTwigVar ['totalPages'] =  $totalPages;   
$arrTwigVar ['CANONICAL'] = rtrim($class->loadSetting('sitesName'),'/') . '/article' ;
     
$companyName = $class->loadSetting('companyName');
$companyLogo = $class->loadSetting('companyLogo');

$arrItemList = array();  

for($i=0;$i<count($rsArticles);$i++){
    
    /*
    $itemList = '{
                  "@context": "http://schema.org",
                  "@type": "ListItem",
                  "position": "1",
                  "item": {
                            "@type": "NewsArticle",
                            "mainEntityOfPage": {
                                "@type": "WebPage",
                                "@id": "'.HTTP_HOST.'",
                                "URL" : "'.rtrim(HTTP_HOST,'/'). REQUEST_URI .$rsArticles[$i]['linktitle'].'"
                            },
                            "headline": "'.$rsArticles[$i]['title'].'",
                            "image": [
                            "'.HTTP_HOST.'phpthumb/phpThumb.php?src='. $class->phpThumbURLSrc.'articles/'.$rsArticles[$i]['pkey'].'/'.$rsArticles[$i]['file'].'&hash='.$rsArticles[$i]['phpThumbHash'].'" 
                            ],
                            "datePublished": "'.$rsArticles[$i]['publishDateISO8601'].'",
                            "dateModified":  "'.$rsArticles[$i]['publishDateISO8601'].'",
                            "author": {
                                "@type": "Person",
                                "name": "'.$companyName.'"
                            },
                            "publisher": {
                                "@type": "Organization",
                                "name": "'.$companyName.'",
                                "logo": {
                                  "@type": "ImageObject",
                                  "url": "'.HTTP_HOST.'phpthumb/phpThumb.php?src='. $class->phpThumbURLSrc.'settings/companyLogo/'.$companyLogo.'&hash='.getPHPThumbHash($companyLogo).'"
                                }
                            },
                            "url" : "'.rtrim(HTTP_HOST,'/'). REQUEST_URI .$rsArticles[$i]['linktitle'].'",
                            "description": "'.$rsArticless[0]['shortdesc'].'"
                  } 
                }';
    */
    
    $linktitle =  str_replace($class->arrSearch,$class->arrReplace,$rsArticles[$i]['title']); 
        
    $itemList = '
     {
      "@type":"ListItem",
      "position":'.($i+1).',
      "url":"'.HTTP_HOST.'article-detail/'.$rsArticles[$i]['pkey'].'/'. $linktitle.'"  
     }
    ';
    
    array_push($arrItemList,$itemList);
}

 
$structureData =' 
<script type="application/ld+json">
{
    "@context": "http://schema.org/",
    "@type": "ItemList",
    "itemListElement": ['.implode(',',$arrItemList).']  
}
</script>
';
    
$arrTwigVar ['STRUCTURE_DATA'] = $structureData;  

echo $twig->render('articles.html', $arrTwigVar);

?>