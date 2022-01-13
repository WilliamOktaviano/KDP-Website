<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass(array('Article.class.php'));  
$article = new Article();

if(empty($_GET)){
	header("location: /");
	die;
}
 
$id = $_GET['id'];  

$rsArticles = $article->getDataRowById($id, ' and statuskey = 1'); 
if(empty($rsArticles)){
	header("location: /");
	die;
}

$rsArticles[0]['phpThumbHash'] =  getPHPThumbHash($rsArticles[0]['file']);
$rsArticles[0]['publishDateISO8601'] =  date('c',strtotime($rsArticles[0]['publishdate']));
$rsArticles[0]['modifiedDateISO8601'] =  date('c',strtotime($rsArticles[0]['publishdate']));
$rsArticles[0]['linktitle'] =  str_replace($class->arrSearch,$class->arrReplace,$rsArticles[0]['title']); 
 
$arrTwigVar ['rsArticle'] =  $article->updateContentLang($rsArticles);
$arrTwigVar ['tag'] = (!empty($rsArticles[0]['tag'])) ? explode(',',$rsArticles[0]['tag']) : '';
  
$arrTwigVar ['META_TITLE'] = $rsArticles[0]['title'];
$arrTwigVar ['META_DESCRIPTION'] = $rsArticles[0]['shortdesc'];  


array_push($arrActive,'/article.php');
array_push($arrActive,'/article.php?'.$id);
/*for($i=0;$i<count($arrTwigVar['categoryPath']);$i++)  
    array_push($arrActive,'/article.php?'.$arrTwigVar['categoryPath'][$i]['pkey']);  */
 
$arrTwigVar ['ACTIVE_MENU'] =  $arrActive;  

$arrTwigVar ['CANONICAL'] = rtrim($class->loadSetting('sitesName'),'/') . '/article' ;

$companyName = $class->loadSetting('companyName');
$companyLogo = $class->loadSetting('companyLogo');
$structureData =' 
<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "NewsArticle",
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "'.HTTP_HOST.'",
    "URL" : "'.rtrim(HTTP_HOST,'/'). REQUEST_URI .$rsArticles[0]['linktitle'].'"
  },
  "headline": "'.$rsArticles[0]['title'].'",
  "image": [
    "'.HTTP_HOST.'phpthumb/phpThumb.php?src='. $class->phpThumbURLSrc.'article/'.$rsArticles[0]['pkey'].'/'.$rsArticles[0]['file'].'&hash='.$rsArticles[0]['phpThumbHash'].'" 
   ],
  "datePublished": "'.$rsArticles[0]['publishDateISO8601'].'",
  "dateModified":  "'.$rsArticles[0]['publishDateISO8601'].'",
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
  "description": "'.$rsArticles[0]['shortdesc'].'"
}
</script>
';
    
$arrTwigVar ['STRUCTURE_DATA'] = $structureData; 
  
/* ===================== ARTICLES ========================================== */  
$rsLatestArticles = $article->searchData($article->tableName.'.statuskey',1,true, ' and publishdate <= now()',' order by '.$article->tableName.'.publishdate desc', ' limit ' . $class->loadSetting('latestNews'));
$arrArticleKey = array_column($rsLatestArticles,'pkey');
$rsArticleCategory = $article->getDetailWithRelatedInformation($arrArticleKey);
$rsArticleCategoryCol = array_column($rsArticleCategory,null,'refkey');
    

foreach($rsLatestArticles as $key=>$articlesRow){ 
  $rsLatestArticles[$key]['phpThumbHash'] =  getPHPThumbHash($articlesRow['file']); 
  $rsLatestArticles[$key]['categoryname'] =  $rsArticleCategoryCol[$articlesRow['pkey']]['categoryname'];
} 
$arrTwigVar['rsLatestArticles'] = $article->updateContentLang($rsLatestArticles);  


/* ===================== ARTICLES ========================================== */  
$rsLatestArticles = $article->searchData($article->tableName.'.statuskey',1,true, '  and featured = 1 and publishdate <= now()',' order by '.$article->tableName.'.publishdate desc', ' limit ' . $class->loadSetting('latestNews'));
$arrArticleKey = array_column($rsLatestArticles,'pkey');
$rsArticleCategory = $article->getDetailWithRelatedInformation($arrArticleKey);
$rsArticleCategoryCol = array_column($rsArticleCategory,null,'refkey');
    
foreach($rsLatestArticles as $key=>$articlesRow){ 
  $rsLatestArticles[$key]['phpThumbHash'] =  getPHPThumbHash($articlesRow['file']); 
  $rsLatestArticles[$key]['categoryname'] =  $rsArticleCategoryCol[$articlesRow['pkey']]['categoryname'];
} 
$arrTwigVar['rsLatestFeaturedArticles'] = $article->updateContentLang($rsLatestArticles);    

echo $twig->render('article-detail.html', $arrTwigVar);
?>