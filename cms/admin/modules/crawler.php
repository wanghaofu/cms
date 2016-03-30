<?php
class Parse_Html
{
	var $targetURL = NULL;
	var $pattern = NULL;
	var $txt_to_parse = NULL;
	var $matches = NULL;
	var $debug = FALSE;
	var $SysInfo = NULL;
	var $remoteurl = NULL;
	var $localImgUrl = NULL;
	var $_Image_Pattern = array
	(
	1 => array
	(
	"pattern" => "/<img[\\s]*[^><]*[\\s]*src=[\"\\']?([^\"><\\s]*.[jpg|gif|png|jpeg])[\"\\']?[\\s]*[^><]*>/ise",
	"mode" => "absolute",
	"dataKey" => "1"
	)
	);

	function Parse_Html( $params )
	{
		if ( isset( $params['targetURL'] ) )
		{
			$this->targetURL = $params['targetURL'];
		}
		if ( isset( $params['img_save_path'] ) )
		{
			$this->img_save_path = $params['img_save_path'];
		}
	}

	function Set_Pattern( $pattern )
	{
		$this->pattern = $pattern;
	}

	function Set_Subject( $txt_to_parse )
	{
		$this->txt_to_parse = $txt_to_parse;
	}

	function setContentPattern( $pattern )
	{
		$this->content_pattern = $pattern;
	}

	function getPage( $pattern, $str )
	{
		if ( preg_match( $this->UrlPageRule[0], $str, $matches ) )
		{
			$url[0] = $matches[1];
			$url = $this->formatHtmlURL( $url, $this->url );
			if ( !empty( $this->pre_url ) && $this->pre_url == $url )
			{
				return FALSE;
			}
			$this->pre_url = $url;
			$url = $url[0];
		}
		$content = $this->GetRemoteFileContent( $url );
		$content = str_replace( "\r\n", "\n", $content );
		$content = str_replace( "\r", "\n", $content );
		if ( preg_match( $pattern[pattern], $content, $matches ) )
		{
			$return = $matches[1];
			$ImgAutoLocalize = new Crawler_ImgAutoLocalize( $url, 0 );
			$return = $ImgAutoLocalize->execute( $return );
			if ( !empty( $pattern[filter][0] ) && is_array( $pattern[filter] ) )
			{
				foreach ( $pattern[filter] as $key => $var1 )
				{
					if ( !empty( $var1 ) )
					{
						if ( $var1 == "localizeImg" )
						{
							$ImgAutoLocalize = new Crawler_ImgAutoLocalize( $this->url );
							$return = $ImgAutoLocalize->execute( $return );
						}
						else if ( $var1 == "page" )
						{
							$pageContent = $this->getPage( $pattern, $content );
							if ( !empty( $pageContent ) )
							{
								$return .=$pageContent;
							}
						}
						else if ( $var1 == "pages" )
						{
							$pageContent = $this->getPage( $pattern, $content );
							if ( !empty( $pageContent ) )
							{
								$return .= CMS_PAGE_MARK.$pageContent;
							}
						}
						else
						{
							$filter = "crawler_".$var1;
							$return = $filter( $return );
						}
					}
				}
			}
			else
			{
				$return = $return;
			}
		}
		return $return;
	}

	function Private_ExecuteParse( &$str )
	{
		if ( $this->pattern == "" || $str == "" )
		{
			$this->SysInfo[] = "please set parse pattern and subject first";
			return FALSE;
		}
		else
		{
			foreach ( $this->pattern as $key => $var )
			{
				$datakey = $var['dataKey'];
				if ( $var[match] == "one" )
				{
					if ( preg_match( "/{Timer}/isU", $var[pattern], $matches ) )
					{
						$this->matches[] = time( );
					}
					else if ( preg_match( "/{Default:(.*)}/isU", $var[pattern], $matches ) )
					{
						$this->matches[] = $matches[1];
					}
					else if ( preg_match( "/{URL}/isU", $var[pattern], $matches ) )
					{
						$return = $this->url;
						if ( !empty( $var[filter][0] ) && is_array( $var[filter] ) )
						{
							foreach ( $var[filter] as $key => $var1 )
							{
								if ( !empty( $var1 ) )
								{
									$filter = "crawler_".$var1;
									$return = $filter( $return );
								}
							}
						}
						$this->matches[] = $return;
					}
					else if ( preg_match( $var[pattern], $str, $matches ) )
					{
						$return = $matches[$datakey];
						$ImgAutoLocalize = new Crawler_ImgAutoLocalize( $this->url, 0 );
						$return = $ImgAutoLocalize->execute( $return );
						if ( !empty( $var[filter][0] ) && is_array( $var[filter] ) )
						{

							foreach ( $var[filter] as $key => $var1 )
							{
								if ( !empty( $var1 ) )
								{
									if ( $var1 == "localizeImg" )
									{
										$ImgAutoLocalize = new Crawler_ImgAutoLocalize( $this->url );
										$return = $ImgAutoLocalize->execute( $return );
									}
									else if ( $var1 == "page" )
									{
										$pageContent = $this->getPage( $var, $str );
										if ( !empty( $pageContent ) )
										{
											$return .= $pageContent;
										}
									}
									else if ( $var1 == "pages" )
									{
										$pageContent = $this->getPage( $var, $str );
										if ( !empty( $pageContent ) )
										{
											$return .= CMS_PAGE_MARK.$pageContent;
										}
									}
									else if ( $varl == "localizeAttach") //附件本地化
									{
										$filter = "crawler_".$var1;
										$return = $filter( $return ,$this->url );
									}
									else
									{
										$filter = "crawler_".$var1;
										$return = $filter( $return );
									}
								}
							}
						}
						else
						{
							$return = $return;
						}
						$this->matches[] = $return;
						$this->SysInfo[] = " parse successfully";
					}
					else
					{
						$this->matches[] = "";
					}
				}
				else if ( preg_match_all( $var[pattern], $str, $matches, PREG_PATTERN_ORDER ) )
				{
					if ( !empty( $var[filter][0] ) && is_array( $var[filter] ) )
					{
						foreach ( $var[filter] as $key => $var1 )
						{
							if ( !empty( $var1 ) )
							{
								if ( $var1 == "localizeImg" )
								{
									$ImgAutoLocalize = new Crawler_ImgAutoLocalize( $this->url );
									$return = $ImgAutoLocalize->execute( $return );
								}
								else
								{
									$filter = "crawler_".$var1;
									$return = $filter( $matches[$datakey] );
								}
							}
						}
					}
					else
					{
						$return = $matches[$datakey];
					}
					if ( $var[localizeImg] == "1" )
					{
						$ImgAutoLocalize = new Crawler_ImgAutoLocalize( $this->url );
						$return = $ImgAutoLocalize->execute( $return );
					}
					$this->matches[] = $return;
					$this->SysInfo[] = " parse successfully";
				}
			}
		}
	}

	function Parse( &$str )
	{
		$this->private_ExecuteParse( &$str );
	}

	function Debug( )
	{
		print_r( $this->$matches );
		exit( );
	}

	function GetRemoteFileContent( $url )
	{
//		$txt = file( $url );
//		$total = count( $txt );
//		$output = "{$txt['0']}";
//		$i = 1;
//		for ( ;	$i <= $total;	++$i	)
//		{
//			$output .= "{$txt[$i]}";
//		}
// 	 echo $url;
		$output = getContentCURL($url);
		return $output;
	}

	function Report( )
	{
		if ( $this->debug == TRUE )
		{
			foreach ( $this->SysInfo as $val )
			{
				echo $val."<br>";
			}
			print_r( $this->matches );
		}
		else
		{
			return $this->SysInfo;
		}
	}

	function getHostName( $url )
	{
		$data = parse_url( $url );
		return $data['scheme']."://".$data['host'];
	}

	function formatHtmlURL( $Urls, $base_url = "" )
	{
		$base_url = empty( $base_url ) ? $this->targetURL : $base_url;
		if ( !empty( $Urls ) )
		{
			foreach ( $Urls as $var )
			{
				$UrlsOK[] = url2absolute( $base_url, $var );
			}
		}
		return $UrlsOK;
	}

	function indexParse( $space_pattern, $pattern )
	{
		$content = $this->GetRemoteFileContent( $this->targetURL );
		if ( $space_pattern[1]['pattern'] != "" )
		{
			$this->Set_Pattern( $space_pattern );
			$this->Parse( $content );
			$content = $this->matches[0];
			$this->Set_Pattern( $pattern );
			$this->Parse( $content );
			$matchLink = $this->matches[1];
		} else {
			$this->Set_Pattern( $pattern );
			$this->Parse( $content );
			$matchLink = $this->matches[0];
		}

		$this->Set_Pattern( $pattern );
		$this->Parse( $content );
		$matchLink = $this->matches[1];
		$header = $this->getHostName( $this->targetURL );
		$location = pathinfo( $this->targetURL );
		if ( $location['dirname'] == "http:" )
		{
			$location['dirname'] = $this->targetURL;
		}
		$yes = $this->formatHtmlURL( $matchLink );
		$valid_href = array_unique( $yes );
		return $valid_href;
	}

	function RunTask( $task )
	{
		$this->Set_Pattern( $this->content_pattern );
		$this->url = $task;
		
		$content = $this->GetRemoteFileContent( $task );
		$content = str_replace( "\r\n", "\n", $content );
		$content = str_replace( "\r", "\n", $content );
		$isSysCharSet= mb_check_encoding($content,CHARSET);
		if($isSysCharSet===false )
		{
			$content = mb_convert_encoding($content, CHARSET, "auto");
		}
		
		if(empty($content))
		{
			exit( "content is empty");
		}
		$this->Parse( $content );
		return $this->matches;
	}

}

function formatPattern( $pattern )
{
	$pattern = str_replace( "&#092;", "\\", $pattern );
	$pattern = str_replace( "@_@", "\"", $pattern );
	if ( empty( $pattern ) )
	{
		return TRUE;
	}
	$ismatch = strpos( $pattern, "isU" );
	$isURLmatch = strpos( $pattern, "{URL}" );
	if ( $ismatch === FALSE && $isURLmatch === FALSE )
	{
		$pattern = str_replace( "/", "\\/", $pattern );
		$pattern = str_replace( "\"", "\\\"", $pattern );
		$pattern = str_replace( "\r\n", "\\n", $pattern );
		$pattern = str_replace( "\n", "\\n", $pattern );
		$pattern = str_replace( " ", "\\s", $pattern );
		$pattern = str_replace( "{DATA}", "(.*)", $pattern );
		if ( strpos( $pattern, "==>" ) )
		{
			$patternArray = explode( "==>", $pattern );
			foreach ( $patternArray as $key => $var )
			{
				if ( $key == 0 )
				{
					$pattern = $var;
				}
				else if ( $key == 1 )
				{
					$pattern .= "/isU==>".$var;
				}
				else
				{
					$pattern .= "==>".$var;
				}
			}
		}
		else
		{
			$pattern .= "/isU";
		}
		$pattern = "/".$pattern;
	}
	if ( preg_match( "/(.*)==>\\[(.*),([01])\\]/i", $pattern, $matches ) )
	{
		$return = array(
		$matches[1],
		$matches[2],
		$matches[3]
		);
	}
	else if ( preg_match( "/(.*)==>\\[(.*)\\]/isU", $pattern, $matches ) )
	{
		preg_match_all( "/==>\\[(.*)\\]/isU", $pattern, $matches1 );
		$return = array(
		$matches[1],
		$matches1[1],
		0
		);
	}
	else
	{
		$return = array(
		$pattern,
		"",
		""
		);
	}
	return $return;
}

function formatPattern_init( $pattern )
{
	$pattern = str_replace( "&#092;", "\\", $pattern );
	$pattern = str_replace( "@_@", "\"", $pattern );
	if ( empty( $pattern ) )
	{
		return TRUE;
	}
	$ismatch = strpos( $pattern, "isU" );
	if ( $ismatch === FALSE )
	{
		$pattern = str_replace( "/", "\\/", $pattern );
		$pattern = str_replace( "\"", "\"", $pattern );
		$pattern = str_replace( "\r\n", "\\n", $pattern );
		$pattern = str_replace( "\n", "\\n", $pattern );
		$pattern = str_replace( "\r", "\\r", $pattern );
		$pattern = str_replace( " ", "\\s", $pattern );
		$pattern = str_replace( "{DATA}", "(.*)", $pattern );
		if ( strpos( $pattern, "==>" ) )
		{
			$patternArray = explode( "==>", $pattern );
			foreach ( $patternArray as $key => $var )
			{
				if ( $key == 0 )
				{
					$pattern = $var;
				}
				else if ( $key == 1 )
				{
					$pattern .= "/isU==>".$var;
				}
				else
				{
					$pattern .= "==>".$var;
				}
			}
		}
		else
		{
			$pattern .= "/isU";
		}
		$pattern = "/".$pattern;
	}
	return $pattern;
}

function formatUrl( $url, $page )
{
	global $Crawler_Page;
	if ( preg_match( "/{(.*)\\[([0-9]*),([0-9]*),([0-9]*)\\]}/isU", $url, $matches ) )
	{
		$page = empty( $page ) ? 0 : $page;
		$Crawler_Page = TRUE;
		if ( $matches[3] < $page )
		{
			return 0;
		}
		else if ( $page < $matches[2] )
		{
			if ( $matches[4] == 1 )
			{
				$url = str_replace( $matches[0], "", $url );
				$page += $matches[2];
			}
			else
			{
				$page += $matches[2];
				$url = str_replace( $matches[0], $matches[1].$page, $url );
				++$page;
			}
		}
		else
		{
			$url = str_replace( $matches[0], $matches[1].$page, $url );
			++$page;
		}
	}
	else if ( preg_match( "/{(.*)\\[([0-9]*),([0-9]*)\\]}/isU", $url, $matches ) )
	{
		$page = empty( $page ) ? 0 : $page;
		$Crawler_Page = TRUE;
		if ( $matches[3] < $page )
		{
			return 0;
		}
		else if ( $page < $matches[2] )
		{
			$url = str_replace( $matches[0], "", $url );
			$page += $matches[2];
		}
		else
		{
			$url = str_replace( $matches[0], $matches[1].$page, $url );
			++$page;
		}
	}
	preg_match_all( "/[\\s\\S]+\\[([\\s\\S]*)\\][\\s\\S]+/isU", $url, $matches );
	$data = $matches[1];
	foreach ( $data as $var )
	{
		$url = str_replace( "[".$var."]", date( $var, time( ) ), $url );
	}
	return $url;
}

include_once( SETTING_DIR."crawler.ini.php" );
if ( !ini_get( "safe_mode" ) )
{
	set_time_limit( 5000 );
}
$magic_quotes_gpc = get_magic_quotes_gpc( );
class Crawler_ImgAutoLocalize extends iData
{

	function Crawler_ImgAutoLocalize( $url, $localize = 1 )
	{
		global $db;
		global $table;
		global $SYS_ENV;
		$sql = "SELECT varValue as num FROM {$table->sys} WHERE  varName ='ResourceNum'";
		$row = $db->getRow( $sql );
		$this->NodeID = 0;
		$this->upload_num = $row[num];
		$this->uploadType = "img";
		$this->rootPath = $SYS_ENV['ResourcePath']."/";
		$this->changeName = 1;
		$this->targetURL = $url;
		$this->localize = $localize;
	}

	function execute( $value )
	{
		$ImgArray = $this->_parseContent( $value );
		$localImgArray = $this->_localize( $ImgArray );
		if ( $localImgArray )
		{
			return $this->_output( $value, $ImgArray, $localImgArray );
		}
		else
		{
			return $value;
		}
	}

	function _parseContent( &$content )
	{
		$_Image_Pattern = array(
		"1" => array( "pattern" => "/<img[\\s]*[^><]*[\\s]*src=[\"]?([^\"><\\s]*.[jpg|gif|png|jpeg])[\"]?[\\s]*[^><]*>/ise", "dataKey" => "1" ),
			"2" => array( "pattern" => "/url\(\s*[\"']?(.*?\.[jpg|gif|png|jpeg])\s*[\"']?\)/ise", 
		"dataKey" => "1" )
		);
		foreach ( $_Image_Pattern as $key => $var )
		{
			$datakey = $var['dataKey'];
			if ( preg_match_all( $var[pattern], $content, $match, PREG_PATTERN_ORDER ) )
			{
				$matches[] = $match[$datakey];
			}
		}
		$img_data = $matches[0];
		if ( is_array( $img_data ) )
		{
			array_unique( $img_data );
		}
		return $img_data;
	}

	function getHostName( $url )
	{
		$data = parse_url( $url );
		return $data['scheme']."://".$data['host'];
	}

	function _output( $value, $ImgArray, $localImgArray )
	{
		if ( !empty( $ImgArray ) )
		{
			foreach ( $ImgArray as $key => $var )
			{
				$value = str_replace( $ImgArray[$key], $localImgArray[$key], $value );
			}
		}
		return $value;
	}

	function formatImgURL( $Images )
	{
		if ( !empty( $Images ) )
		{
			foreach ( $Images as $var )
			{
				$ImagesOK[] = url2absolute( $this->targetURL, $var );
			}
		}
		return $ImagesOK;
	}

	function _localize( $ImgArray )
	{
		global $db;
		$ImgArray = $this->formatImgURL( $ImgArray );
		if ( !$this->localize )
		{
			return $ImgArray;
		}
		else
		{
			return $ImgArray;
		}
		if ( !is_array( $ImgArray ) )
		{
			return FALSE;
		}
		$num = 0;
		foreach ( $ImgArray as $key => $var )
		{
			$dataPath = $this->makeAutoPath( );
			$pathinfo = pathinfo( $var );
			if ( $result = $this->recordExists( $var ) )
			{
				$saveFile[$key] = $this->rootPath.$result[Path];
				continue;
			}
			$targetPath = $this->uploadType."/".$dataPath."/";
			if ( cmsware_mkdir( $this->rootPath.$targetPath, 511 ) )
			{
				if ( $this->changeName == "1" )
				{
					$rename = $this->uploadType.date( "YmdHis", time( ) ).$key.".".$pathinfo[extension];
				}
				else
				{
					$rename = $pathinfo['basename'];
				}
				$destination = $this->rootPath.$targetPath.$rename;
				if ( copy( $var, $destination ) )
				{
					if ( $this->uploadType == "img" )
					{
						$img_size = Image::getimgsize( $destination );
						$info = $img_size['width']."*".$img_size['height'];
					}
					$this->flushData( );
					$this->addData( "Category", $this->uploadType );
					$this->addData( "Src", $var );
					$this->addData( "Type", 1 );
					$this->addData( "NodeID", $this->NodeID );
					$this->addData( "Name", $rename );
					$this->addData( "Path", $targetPath.$rename );
					$this->addData( "Size", filesize( $destination ) );
					$this->addData( "Info", $info );
					$this->insertDBLog( );
					++$num;
					$saveFile[$key] = $destination;
				}
			}
			else
			{
				return FALSE;
			}
		}
		$this->Counter( $num );
		return $saveFile;
	}

	function Counter( $num = 1 )
	{
		global $db;
		global $table;
		$sql = "UPDATE {$table->sys} SET `varValue`=varValue +1  where varName='ResourceNum'";
		$row = $db->query( $sql );
	}

	function insertDBLog( )
	{
		global $db;
		global $table;
		$time = time( );
		$this->addData( "CreationDate", $time );
		$this->addData( "ModifiedDate", $time );
		if ( $this->dataInsert( $table->resource ) )
		{
			return TRUE;
		}
		else
		{
			new Error( "Failure: insertDBLog" );
			return FALSE;
		}
	}

	function recordExists( $src )
	{
		global $db;
		global $table;
		$result = $db->getRow( "SELECT ResourceID,Path FROM {$table->resource} WHERE Src='{$src}'" );
		if ( !empty( $result[ResourceID] ) )
		{
			return $result;
		}
		else
		{
			return FALSE;
		}
	}

	function makeAutoPath( )
	{
		$num = $this->upload_num;
		$num = strval( $num );
		$add_zero = 8 - strlen( $num );
		$num = str_repeat( "0", $add_zero ).$num;
		$DirSecond = "h".substr( $num, 0, 3 );
		$DirFirst = "h".substr( $num, -5, 2 );
		return $DirSecond."/".$DirFirst;
	}

}
$pattern = formatpattern( $IN['UrlFilterRule'] );
$index_link_pattern = array(
"1" => array(
"pattern" => $pattern[0],
"mode" => "absolute",
"replace" => "",
"dataKey" => "1"
)
);
$pattern = formatpattern( $IN['TargetURLArea'] );
$index_link_space_pattern = array(
"1" => array(
"pattern" => $pattern[0],
"mode" => "absolute",
"replace" => "",
"dataKey" => "1",
"match" => "one"
)
);
$params = array(
"targetURL" => formaturl( $_POST['TargetURL'], 0 )
);
$crawler = new Parse_Html( $params );

switch ( $IN['mode'] )
{
	case "running" :
		
		foreach ( $IN as $key => $var )
		{
			$prefix = substr( $key, 0, 5 );
			$suffix = substr( $key, 5 );
			if ( $prefix == "rule_" )
			{
				if ( $var == "" )
				{
					continue;
				}
				$Rules[] = array(
				"title" => $IN["title_".$suffix],
				"rule" => $var
				);
				$pattern = formatpattern( $var );
				$patternArray[] = array(
				"pattern" => $pattern[0],
				"filter" => $pattern[1],
				"localizeImg" => 1,
				"mode" => "absolute",
				"replace" => "",
				"dataKey" => "1",
				"match" => "one"
				);
			}
			else
			{
				continue;
			}
			
		}
		
		$crawler->setContentPattern( $patternArray );
		$crawler->UrlPageRule = formatpattern( $IN['UrlPageRule'] );
		$result = $crawler->RunTask( $_POST[Url] );
		
		$TPL->assign( "result", $result );
		$TPL->assign( "Rules", $Rules );
		$TPL->assign( "Url", $_POST[Url] );
		$TPL->display( "collection_testRules_content_result.html" );
		break;
	default :
		foreach ( $IN as $key => $var )
		{
			$prefix = substr( $key, 0, 5 );
			$suffix = substr( $key, 5 );
			if ( $prefix == "rule_" )
			{
				$pattern = formatpattern_init( $var );
				$Rules[$suffix] = array(
				"title" => $IN["title_".$suffix],
				"rule" => str_replace( "\"", "@_@", $pattern )
				);
			}
			else
			{
				continue;
			}
		}
		$TPL->assign( "Rules", $Rules );
		$TPL->assign( "UrlPageRule", str_replace( "\"", "@_@", $IN['UrlPageRule'] ) );
		$TPL->assign( "TargetURL", $IN['TargetURL'] );
		$linkArray = $crawler->indexParse( $index_link_space_pattern, $index_link_pattern );
		$TPL->assign( "Links", $linkArray );
		$TPL->display( "collection_testRules_links_result.html" );
		break;
}
echo "\r\n ";
?>
