<?php
// define('CMS_PAGE_MARK', '<h3><font color="#888888">[Page: ]</font></h3>');
define('CMS_PAGE_MARK', '');
function crawler_nl2br($str)
{
	$str = str_replace(' ', '&nbsp;&nbsp;', $str);
	return nl2br($str);

}
/**
 * 将字符串格式化为Timestamp
 */	
function crawler_timeFormat($str)
{
	$str = str_replace('&nbsp;',' ', $str);
	$str = str_replace('/','-', $str);
	$str = str_replace('年','-', $str);
	$str = str_replace('月','-', $str);
	$str = str_replace('日',' ', $str);

	$str = str_replace('时',':', $str);
	$str = str_replace('分',':', $str);
	$str = str_replace('秒','', $str);

	return strtotime($str);
}

/**
 * 清除内容中的flash/iframe/js广告等
 */
function crawler_clearRubbish($str)
{
	$clear_pattern = array();

	$clear_pattern[]=array(
	'pattern'=>"/<OBJECT[\S|\s]*<\/OBJECT>/isU"
	,'replace'=>""
	);
	$clear_pattern[]= array(
	'pattern'=>"/<IFRAME[\S|\s]*<\/IFRAME>/isU"
	,'replace'=>""
	);
	$clear_pattern[]= array(
	'pattern'=>"/<SCRIPT[\S|\s]*<\/SCRIPT>/isU"
	,'replace'=>""
	);
	$clear_pattern[]= array(
	'pattern'=>"/<A[\S|\s]*HREF=[\S|\s]*>([\S|\s]*)<\/A>/isU"
	,'replace'=>"\\1"
	);
	$clear_pattern[]= array(
	'pattern'=>"/<map[\S|\s]*>([\S|\s]*)<\/map>/isU"
	,'replace'=>""
	);
	$clear_pattern[]= array(
	'pattern'=>"/<!--[\S|\s]*-->/isU"
	,'replace'=>""
	);
	$clear_pattern[]= array(
	'pattern'=>"/<\/?td[\S|\s]*>/isU"
	,'replace'=>""
	);
	$clear_pattern[]= array(
	'pattern'=>"/<\/?tr[\S|\s]*>/isU"
	,'replace'=>""
	);
	$clear_pattern[]= array(
	'pattern'=>"/<\/?table[\S|\s]*>/isU"
	,'replace'=>""
	);
	$clear_pattern[]= array(
	'pattern'=>"/<\/?div[\S|\s]*>/isU"
	,'replace'=>""
	);
	$clear_pattern[]= array(
	'pattern'=>"/<(\/?p)[\S|\s]*>/isU"
	,'replace'=>"<$1>"
	);
	$clear_pattern[]= array(
	'pattern'=>"/点击图片进入下一页/isU"
	,'replace'=>""
	);


	foreach($clear_pattern as $key=>$var) {

		$str = preg_replace($var['pattern'],$var['replace'], $str);


	}

	return $str;

}

function crawler_clearPcOnline($str){
	$clear_pattern = array();
/** 	$clear_pattern[]=array(
 	'pattern'=>"/<DIV id=\"?proInfo2\"?>(.*?)<\/i><\/li><\/ul><\/div>/isU"
	,'replace'=>""
 	);
 **/
/**	
	$clear_pattern[]=array(
	'pattern'=>"/<div id=\"?artNav\"?>(.*?)<\/table><\/div>/isU"
	,'replace'=>""
	);
	**/
	$clear_pattern[]=array(
	'pattern'=>"/<zmkey[\S|\s]*<\/zmkey>/isU"
	,'replace'=>""
	);
	$clear_pattern[]=array(
	'pattern'=>"/PClady独家专稿[，\s]*未经许可请勿转载！/isU"
	,'replace'=>""
	);
	
	$clear_pattern[]=array(
	'pattern'=>"/<span style=\"color: #800080\">[\S|\s]*<\/span>/isU"
	,'replace'=>""
	);
	$clear_pattern[]=array(
	'pattern'=>"/<\/?span[\S|\s]*>/isU"
	,'replace'=>""
	);
	$clear_pattern[]=array(
	'pattern'=>"/<\/?span[\S|\s]*>/isU"
	,'replace'=>""
	);
	$clear_pattern[]=array(
	'pattern'=>"/>>/isU"
	,'replace'=>""
	);
	$clear_pattern[]=array(
	'pattern'=>"/<strong>相关阅读：<\/strong>/isU"
	,'replace'=>""
	);
// 	$clear_pattern[]=array(
// 	'pattern'=>"/\-/isU"
// 	,'replace'=>""
// 	);
	$clear_pattern[]=array(
	'pattern'=>"/点击图片进入下一页&gt;&gt;/isU"
	,'replace'=>""
	);
   $clear_pattern[]=array(
	'pattern'=>"/其他热点文章链接：<\/strong>/isU"
	,'replace'=>""
	);
	  $clear_pattern[]=array(
	'pattern'=>"/【全文阅读】/isU"
	,'replace'=>""
	);
	
	foreach($clear_pattern as $key=>$var) {
		$str = preg_replace($var['pattern'],$var['replace'], $str);
	}
	return $str;
}

function crawler_clearPcLadyText($str){
	$clear_pattern = array();
	$clear_pattern[]=array(
	'pattern'=>"/PClady/isU"
	,'replace'=>"sesoe"
	);
	foreach($clear_pattern as $key=>$var) {
		$str = preg_replace($var['pattern'],$var['replace'], $str);
	}
	return $str;
}
function crawler_clearPcQinZiText($str){
	$clear_pattern = array();
	$clear_pattern[]=array(
	'pattern'=>"/_太平洋亲子网亲子宝典/isU"
	,'replace'=>""
	);
	$clear_pattern[]=array(
	'pattern'=>"/_太平洋亲子网亲子宝典/isU"
	,'replace'=>""
	);
	$clear_pattern[]=array(
	'pattern'=>"/太平洋/isU"
	,'replace'=>""
	);
	$clear_pattern[]=array(
	'pattern'=>"/<!-- left650 -->(.*?)(<\/div>)?/isU"
	,'replace'=>""
	);
	
	
	
	foreach($clear_pattern as $key=>$var) {
		$str = preg_replace($var['pattern'],$var['replace'], $str);
	}
	return $str;
}

function crawler_clearPcQinZiTitleText($str){
	$clear_pattern = array();
	$clear_pattern[]=array(
	'pattern'=>"/_.*?/isU"
	,'replace'=>""
	);
	foreach($clear_pattern as $key=>$var) {
		$str = preg_replace($var['pattern'],$var['replace'], $str);
	}
	return $str;
}

function crawler_clearITeye($str){
	$clear_pattern = array();
	$clear_pattern[]= array(
			'pattern'=>"/<h3>.*<\/h3>/isU"
			,'replace'=>""
	);
	$clear_pattern[]=array(
			'pattern'=>"/\s*-\s*ITeye问答/isU"
			,'replace'=>" "
	);
	foreach($clear_pattern as $key=>$var) {
		$str = preg_replace($var['pattern'],$var['replace'], $str);
	}
	return $str;
}

function crawler_clearChinaUnix($str){
	$clear_pattern = array();
	$clear_pattern[]=array(
			'pattern'=>"/-.*$/isU"
			,'replace'=>" "
	);
	
	foreach($clear_pattern as $key=>$var) {
		$str = preg_replace($var['pattern'],$var['replace'], $str);
	}
	return $str;
}


function crawler_clearChinaUnixDiv($str){
	$clear_pattern = array();
	$clear_pattern[]=array(
			'pattern'=>"/<\/div>\s*<!--\s<div\s*class=\"Blog_con3_1\">.*<\/div>\s*-->/isU"
			,'replace'=>""
	);
	$clear_pattern[]= array(
			'pattern'=>"/<SCRIPT[\S|\s]*<\/SCRIPT>/isU"
			,'replace'=>""
	);
	$clear_pattern[]= array(
	'pattern'=>"/<A[\S|\s]*HREF=[\S|\s]*>([\S|\s]*)<\/A>/isU"
	,'replace'=>"\\1"
	);
	foreach($clear_pattern as $key=>$var) {
		$str = preg_replace($var['pattern'],$var['replace'], $str);
	}
	return $str;
}


/**
 * 去掉 HTML 标记
 */
function crawler_clearHTML($str)
{
	$str = strip($str);
	$search = array (
	"'<[\/\!]*?[^<>]*?>'si",
	);

	$replace = array ("",
	);

	return preg_replace ($search, $replace, $str);

}


/**
 * 华军软件园下载地址url提取,返回字串,使用分隔\\
 */
function crawler_download_url_parser__newhua($str)
{
	$patt = "/<a[\s]*href=\"(.*)\"/isU";
	if (preg_match_all($patt, $str, $match))
	{
		foreach($match[0] as $key=>$var) {
			if($key == 0) {
				$return  = $match[1][$key];

			} else {
				$return .= "\r".$match[1][$key];

			}

		}
		//print_r($match);exit;
	}


	return $return;

}

function crawler_download_star__newhua($str)
{
	$patt = "/icon_star.gif/isU";
	if (preg_match_all($patt, $str, $match))
	{

		return count($match[0]);
	}

}

function crawler_clearCommentRubbish__pconline($str)
{

	$clear_pattern = array(

	'1' => array(
	'pattern'=>"/<DIV align=center><A>察看评论详细内容(.*)<\/DIV>/isU"
	,'replace'=>""
	),

	);
	foreach($clear_pattern as $key=>$var) {

		$str = preg_replace($var['pattern'],$var['replace'], $str);
	}

	return $str;

}



function crawler_localizeAttach($resourceUrl,$url){
	//	$resourceUrl = "http://www.78baby.com{$resourceUrl}";
	//	if ( !$resourceUrl ) return false;
	$AttachLocalize = new Crawler_ImgAutoLocalize( $url );

	$tmp = explode( ".",  $url );
	//	$tmp = explode( ".", $resourceUrl );
	$key = count( $tmp ) - 1;
	$ext = $tmp[$key];
	switch ( $ext ){
		case 'swf':
			$fileType = 'flash';
			break;
		case 'chm':
			$fileType = 'docs';
		case 'doc':
			$fileType = 'docs';
		case 'docx':
			$fileType = 'docs';
		case 'txt':
			$fileType = 'docs';
		case 'rar':
			$fileType = 'docs';
			break;
		case 'jpg':
			$fileType = 'jpg';
		case 'wav':
			$fileType = 'mp3';
		case 'au':
			$fileType = 'mp3';
		case 'rm':
			$fileType = 'mp3';
		case 'mp3':
			$fileType = 'mp3';
			break;

		default:
			$fileType = $ext;
	}
	$AttachLocalize->uploadType = $fileType;
	$resourceUrlArr=array( $resourceUrl );
	$localAttachArr = $AttachLocalize->_localize( $resourceUrlArr );
	if ( $localAttachArr )
	{
		return  $AttachLocalize->_output( $resourceUrl , $resourceUrlArr , $localAttachArr );
	}
	else
	{
		return $resourceUrl;
	}
}

function crawler_clear17173( $str ){
	$str=str_replace('>','',$str);
	return $str;
}
function crawler_PromptRemotionUrl( $resourceUrl ){
	$url = "http://www.78baby.com{$resourceUrl}";
	return $url;
}

function crawler_utf8_to_gb2312($str) {
	//	$str = iconv("UTF-8","GBK",$str);
	$str = mb_convert_encoding($str, "gb2312", "auto");
	return $str;
}


//duowan 清理函数
function crawler_duowan_title_clear($str) {
	$strArr = explode('-',$str);
	return $strArr[0];
}

//宝宝数

function crawler_baobaotree_clear($str) {
	$clear_pattern = array(
	'1' => array(
	'pattern'=>"/<SPAN class=\"q_keyword\">(.*?)<\/SPAN>/isU"
	,'replace'=>""
	),
         '2' => array(
	'pattern'=>"/提问：/isU"
	,'replace'=>""
	),
	);
         foreach($clear_pattern as $key=>$var) {
		$str = preg_replace($var['pattern'],$var['replace'], $str);
	}

	return $str;
}


function crawler_duowan_clear($str) {
	$clear_pattern = array();

	$clear_pattern[]=array(
	'pattern'=>"/多玩/isU"
	,'replace'=>"天堂玩"
	);

	foreach($clear_pattern as $key=>$var) {
		$str = preg_replace($var['pattern'],$var['replace'], $str);
	}
	return $str;
}

function crawler_meiyou_clear($str) {
	$clear_pattern = array();

	$clear_pattern[]= array(
	'pattern'=>"/<\/?A[^>]*?>/isU"
	,'replace'=>""
	);

	$clear_pattern[]= array(
	'pattern'=>'/<img\s*[^>]*?button[^>]*?>/isU'
	,'replace'=>""
	);

	$clear_pattern[]= array(
	'pattern'=>"/<SCRIPT[\S|\s]*<\/SCRIPT>/isU"
	,'replace'=>""
	);
	
	$clear_pattern[]= array(
	'pattern'=>'/<img\s*[^>]*?arrow[^>]*?>/isU'
	,'replace'=>""
	);

	$clear_pattern[]= array(
	'pattern'=>'/PART[0-9]+/isU'
	,'replace'=>""
	);
	$clear_pattern[]= array(
	'pattern'=>'/第[0-9]+期/isU'
	,'replace'=>""
	);
	
	
	foreach($clear_pattern as $key=>$var) {
		$str = preg_replace($var['pattern'],$var['replace'], $str);
	}
	return $str;
}



?>