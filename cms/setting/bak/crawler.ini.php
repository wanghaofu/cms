<?php
define('CMS_PAGE_MARK', '<h3><font color="#888888">[Page: ]</font></h3>');

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
		$clear_pattern = array(

			'1' => array(
						'pattern'=>"/<OBJECT[\S|\s]*<\/OBJECT>/isU"
						,'replace'=>""
					),
			'2' => array(
						'pattern'=>"/<IFRAME[\S|\s]*<\/IFRAME>/isU"
						,'replace'=>""
					),
			'3' => array(
						'pattern'=>"/<SCRIPT[\S|\s]*<\/SCRIPT>/isU"
						,'replace'=>""
					),			
			'4' => array(
						'pattern'=>"/<A HREF=[\S|\s]*>([\S|\s]*)<\/A>/isU"
						,'replace'=>"\\1"
					),
			'5' => array(
						'pattern'=>"/<map[\S|\s]*>([\S|\s]*)<\/map>/isU"
						,'replace'=>""
					),
			'6' => array(
						'pattern'=>"/<!--[\S|\s]*-->/isU"
						,'replace'=>""
					),
			'7' => array(
						'pattern'=>"/<!--[\S|\s]*-->/isU"
						,'replace'=>""
					),
		);
								
	
		foreach($clear_pattern as $key=>$var) {

				$str = preg_replace($var['pattern'],$var['replace'], $str);
	
				
		}

		return $str;

}

function crawler_pconline($str)
{
		$clear_pattern = array(

			'1' => array(
						'pattern'=>"/<STYLE[\S|\s]*<\/STYLE>/isU"
						,'replace'=>""
					),
			'2' => array(
						'pattern'=>"/<STRONG><U>PClady独家专稿，未经许可不得转载！<\/U><\/STRONG>/isU"
						,'replace'=>""
					),
			'3' => array(
						'pattern'=>"/<div id=\"art_nextart\" class=\"f14\">[\S|\s]*<\/div>/isU"
						,'replace'=>""
					),	
				
			'4' => array(
						'pattern'=>"/<TABLE[\S|\s]*<\/TABLE>/isU"
						,'replace'=>""
					),
			'5' => array(
						'pattern'=>"/<tr[\S|\s]*<\/tr>/isU"
						,'replace'=>""
					),	
			'6' => array(
						'pattern'=>"/<(\/)?td[\S|\s]*>/isU"
						,'replace'=>""
					),
			'7' => array(
						'pattern'=>"/<(\/)?div[\S|\s]*>/isU"
						,'replace'=>""
					),
			'8' => array(
						'pattern'=>"/<(\/)?font[\S|\s]*>/isU"
						,'replace'=>""
					),
			'9' => array(
						'pattern'=>"/\[[\S|\s]*\]/isU"
						,'replace'=>""
					),
			'10' => array(
						'pattern'=>"/<(\/)?P[\S|\s]*>/isU"
						,'replace'=>"<br/>"
					),
			'11' => array(
						'pattern'=>"/<(\/)?table[\S|\s]*>/isU"
						,'replace'=>""
					),
			'12' => array(
						'pattern'=>"/<(\/)?tr[\S|\s]*>/isU"
						,'replace'=>""
					),			
			'13' => array(
						'pattern'=>"/<(\/)?a[\S|\s]*>/isU"
						,'replace'=>""
					),
			'14' => array(
						'pattern'=>"/<\/a/isU"
						,'replace'=>""
					),
			'15' => array(
						'pattern'=>"/下一篇：/isU"
						,'replace'=>""
					),
			'16' => array(
						'pattern'=>"/<select[\S|\s]*<\/select>/isU"
						,'replace'=>""
					),
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
?>