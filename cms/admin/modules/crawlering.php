<?php
function logit( $content )
{
	$handle = fopen( "log.txt", "a" );
	fwrite( $handle, $content."\n" );
	fclose( $handle );
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

function formatUrl( $url, &$page )
{
	global $Crawler_Page;
	if ( preg_match( "/{(.*)\\[([0-9]*),([0-9]*),([0-9]*)\\]}/isU", $url, $matches ) )
	{
		$page = empty( $page ) ? 0 : $page;
		logit( "page:".$page );
		$Crawler_Page = TRUE;
		if ( $matches[3] < $page )
		{
			return 0;
		}
		else if ( $page < $matches[2] )
		{
			if ( $matches[4] == 1 && $page == 0 )
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
	set_time_limit( 0 );
}
if ( !isset( $debugger ) )
{
	$debugger = new Debug( );
	$debugger->startTimer( );
}
$run_num_once = 20;
$magic_quotes_gpc = get_magic_quotes_gpc( );
$max_running_time = get_cfg_var( "max_execution_time" );
$Crawler_Page = FALSE;
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
		"1" => array( "pattern" => "/<img[\\s]*[^><]*[\\s]*src=\"([^\"]*)\"[\\s]*[^><]*>/ise", "dataKey" => "1" ),
		"2" => array( "pattern" => "/<img[\\s]*[^><]*[\\s]*src=\\'([^\\']*)\\'[\\s]*[^><]*>/ise", "dataKey" => "1" ),
		"3" => array( "pattern" => "/url\(([^\)]*)\)/ise", "dataKey" => "1" ),
		"4" => array( "pattern" => "/url\(\s*[\"']?(.*?\.[jpg|gif|png|jpeg])\s*[\"']?\)/ise", 
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
		global $SYS_ENV;
		$ImgArray = $this->formatImgURL( $ImgArray );
		if ( !$this->localize )
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
				echo "<Br>pass localizeimg...".$saveFile[$key];
				continue;
			}
			$targetPath = $this->uploadType."/".$dataPath."/";
			if ( cmsware_mkdir( $this->rootPath.$targetPath, 511 ) )
			{
				if ( $this->changeName == "1" )
				{
					$pathinfo[extension] = strtolower( $pathinfo[extension] );

					$upAttachType = explode("|",$SYS_ENV['upAttachType']);
					$allow_extension = array( "jpg", "gif", "jpeg", "png" );
					$allow_extension=array_merge($allow_extension,$upAttachType);
					if ( !in_array( $pathinfo[extension], $allow_extension ) )
					{
						$imginfo = getimagesize( $var );
						if ( !$imginfo )
						{
							$saveFile[$key] = $var;
							continue;
						}
						switch ( $imginfo[2] )
						{
							case 1 :
								$pathinfo[extension] = "gif";
								break;
							case 2 :
								$pathinfo[extension] = "jpg";
								break;
							case 3 :
								$pathinfo[extension] = "png";
								break;
							default :
								$pathinfo[extension] = $pathinfo["extension"];
								break;
						}
					}
					$random = rand( 0, 1000 );
					$rename = $this->uploadType.date( "YmdHis", time( ) ).$key.$random.".".$pathinfo[extension];
				}
				else
				{
					$rename = $pathinfo['basename'];
				}
				$destination = $this->rootPath.$targetPath.$rename;
				echo "<I>start copying...</I>".$var." to ".$destination."<BR>";
				if ( copy( url_valid( $var ), $destination ) )
				{
					if ( $this->uploadType == "img" )
					{
						$img_size = Image::getimgsize( $destination );
						$info = $img_size['width']."*".$img_size['height'];
					}
					if ( $SYS_ENV['EnableCLWaterMark'] == 1 && $this->uploadType == "img")
					{
						imagewatermark( $destination, $SYS_ENV['WaterMarkPosition'], $SYS_ENV['WaterMarkImgPath'] );
					}
					$this->flushData( );
					$this->addData( "Category", $this->uploadType );
					$this->addData( "Src", $var );
					$this->addData( "Type", 1 );
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
		global $sys;
		$time = time( );
		$src = $this->getData( "Src" );
		$this->addData( "CreationUserID", $sys->session['sUId'] );
		$this->addData( "CreationDate", $time );
		$this->addData( "ModifiedDate", $time );
		if ( $this->dataInsert( $table->resource ) )
		{
			$ResourceID = $this->db_insert_id;
			$this->flushData( );
			$this->addData( "ResourceID", $ResourceID );
			$this->addData( "CollectionKey", md5( $this->targetURL ) );
			$this->dataInsert( $table->resource_ref );
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
			if ( !$this->resourceRefExists( $result[ResourceID] ) )
			{
				$this->flushData( );
				$this->addData( "ResourceID", $result[ResourceID] );
				$this->addData( "CollectionKey", md5( $this->targetURL ) );
				$this->dataInsert( $table->resource_ref );
			}
			return $result;
		}
		else
		{
			return FALSE;
		}
	}

	function resourceRefExists( $ResourceID )
	{
		global $db;
		global $table;
		$src = md5( $this->targetURL );
		$result = $db->getRow( "SELECT count(*) as nr FROM {$table->resource_ref} WHERE ResourceID={$ResourceID} AND CollectionKey='{$src}'" );
		if ( !empty( $result[nr] ) )
		{
			return TRUE;
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

class Parse_Html extends iData
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
	),
	2=>array(
	"pattern" => "/url\(([^\)]*)\)/ise",
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
						$imgLocalize = FALSE;
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
										$imgLocalize = TRUE;
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
										$return = $filter( $return , $this->url );
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
						if ( !$imgLocalize )  //这是干嘛的？
						{
							$ImgAutoLocalize = new Crawler_ImgAutoLocalize( $this->url, 0 );
							$return = $ImgAutoLocalize->execute( $return );
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
		logit( "URL:".$url );
//		$handle = fopen( $url, "rb" );
//		if ( $handle )
//		{
//			stream_set_timeout( $handle, 20 );
//			$contents = "";
//			do
//			{
//				$data = fread( $handle, 8192 );
//				if ( strlen( $data ) == 0 )
//				{
//					break;
//				}
//				$contents .= $data;
//			} while ( 1 );
//			fclose( $handle );
//		}
		$contents  = getContentCURL($url);
		return $contents;
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
		if ( empty( $matchLink ) )
		{
			return FALSE;
		}
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
		$this->matches='';
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
	
		$this->Parse( $content );
		
		return $this->matches; //为什么返回$this->matches? 给前端调用返回的 返回 匹配的东西
	}

	function taskAdd( )
	{
		global $db;
		global $table;
		if ( $this->dataInsert( $table->tasks ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function taskGet( $TaskID )
	{
		global $db;
		global $table;
		$result = $db->getRow( "SELECT TaskData FROM {$table->tasks} WHERE TaskID='{$TaskID}'" );
		return unserialize( stripslashes( $result['TaskData'] ) );
	}

	function taskUpdate( $TaskID )
	{
		global $db;
		global $table;
		$where = "where TaskID='".$TaskID."'";
		if ( $this->dataUpdate( $table->tasks, $where ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function taskEnd( $TaskID )
	{
		global $db;
		global $table;
		return $db->query( "DELETE FROM {$table->tasks} WHERE TaskID='{$TaskID}'" );
	}

}

$pattern = formatpattern( $CateInfo['UrlFilterRule'] );
$index_link_pattern = array(
"1" => array(
"pattern" => $pattern[0],
"mode" => "absolute",
"replace" => "",
"dataKey" => "1"
)
);
$pattern = formatpattern( $CateInfo['TargetURLArea'] );
$index_link_space_pattern = array(
"1" => array(
"pattern" => $pattern[0],
"mode" => "absolute",
"replace" => "",
"dataKey" => "1",
"match" => "one"
)
);
$firstPage = $IN['Page'];
$params = array(
"targetURL" => formaturl( $CateInfo['TargetURL'], &$IN['Page'] )
);
$crawler = new Parse_Html( $params );
switch ( $IN['mode'] )
{
	case "running" :
		$TaskData = $crawler->taskGet( $IN[TaskID] );
		$TaskNum = count( $TaskData );
		if ( $TaskNum == 0 || empty( $TaskData ) )
		{
			$crawler->taskEnd( $IN[TaskID] );
			if ( $Crawler_Page )
			{
				echo "<script language='JavaScript'>\n";
				echo "var oBao = new ActiveXObject(\"Microsoft.XMLHTTP\");\n";
				echo "var returnValue;\n";
				echo "oBao.open(\"POST\",\"admin_collection.php?sId=".$IN['sId']."&o=Crawlering&CateID=".$CateInfo['CateID']."&Page=".$IN['Page']."\" ,false);\n";
				echo "oBao.send();\n";
				echo "returnValue = unescape(oBao.responseText);\n";
				echo "if(returnValue.lastIndexOf('<b>') == -1) {\n";
				echo "top.CrawlerTaskFrame.addThread(\"admin_collection.php?sId=".$IN['sId']."&o=Crawlering&mode=running&TaskID=\"+ returnValue +\"&CateID=".$CateInfo['CateID']."&Page=".$IN['Page']."\" , returnValue);\n";
				echo "top.TaskInfoFrame.addInfo('".$CateInfo['Name'].":Page-".$IN['Page']."[<a href=# onclick=\"top.CrawlerTaskFrame.endThread(\\'' + returnValue + '\\')\">STOP</a>]', returnValue);\n";
				echo "}\n";
				echo "</script>";
				sleep( 1 );
				exit( "<script>parent.endThread('".$IN[TaskID]."');</script>" );
			}
			else
			{
				echo "<script>parent.addInfo('".$CateInfo[Name].":Task Finished', '".$IN[TaskID]."');</script>";
				exit( "<script>parent.endThread('".$IN[TaskID]."');</script>" );
			}
		}
		else
		{
			--$IN[Page];
			echo "Task Running...<br>";
			if ( $IN['run_num'] != "" )
			{
				$run_num_once = $IN['run_num'];
			}
			echo $params['targetURL'];
			echo "<br>";
			$fieldInfo = content_table_admin::gettablefieldsinfo( $CateInfo[TableID] );
			$RulesInfo = collection_cate_admin::getrules( $CateInfo[CateID] );
			foreach ( $fieldInfo as $key => $var )
			{
				if ( empty( $RulesInfo[$var[ContentFieldID]] ) )
				{
					continue;
				}
				$pattern = formatpattern( $RulesInfo[$var[ContentFieldID]] );
				$patternArray[] = array(
				"pattern" => $pattern[0],
				"filter" => $pattern[1],
				"localizeImg" => $pattern[2],
				"mode" => "absolute",
				"replace" => "",
				"dataKey" => "1",
				"match" => "one"
				);
				$validFields[] = $var[FieldName];
			}
			$crawler->setContentPattern( $patternArray );
			$crawler->UrlPageRule = formatpattern( $CateInfo['UrlPageRule'] );
			if ( !empty( $TaskData ) )
			{
				$i = 0;
				for ( $i;	$i <= $run_num_once;	++$i	) //一次采集的页数
				{
					$current_task = array_shift( $TaskData );  //目前采集的页面
					if ( $CollectionID = $collection->recordExists( $CateInfo, $current_task ) )  //加入记录存在
					{
						if ( $CateInfo[RepeatCollection] == 1 ) //加入允许重复采集则采集
						{
							$result = $crawler->RunTask( $current_task );
							echo $result[0];
							$collection->flushData( );
							foreach ( $validFields as $key => $var )
							{
								$collection->addData( $var, $result[$key] );
							}
							$time = time( );
							$collection->addData( "ModifiedDate", $time );
							if ( $collection->update( $CateInfo, $CollectionID ) )
							{
								echo "<br>Update database successfully:".$current_task;
							}
							else
							{
								echo "<br>Update database fail:".$current_task;
							}
						}
						else   //不重复采集则跳过
						{
							echo "<I>Record exists,continue...</I><br>&nbsp;&nbsp;&nbsp;&nbsp;<I>".$current_task."</I>&nbsp;...<br>";
							continue;
						}
					}
					else   //记录不存在
					{
						echo "<I>Start crawlering...</I><br>&nbsp;&nbsp;&nbsp;&nbsp;<I>".$current_task."</I>&nbsp;...<br>";
						$result = $crawler->RunTask( $current_task ); //开始采集任务
						//在这里返回来的数据是重复的 ？？
						echo $result[0];
						$collection->flushData();  //以下向数据库添加数据 
						foreach ( $validFields as $key => $var )
						{
							$collection->addData( $var, $result[$key] );
						}
						$time = time( );
						$collection->addData( "Src", $current_task );
						$collection->addData( "CateID", $CateInfo[CateID] );
						$collection->addData( "CreationDate", $time );
						$collection->addData( "ModifiedDate", $time );
						if ( $CateInfo[TableID] == 1 && $result[0] == "" )
						{
							echo "<br>Title is Null";
							continue;
						}
						if ( $collection->add( $CateInfo ) )
						{
							echo "<br>Insert Into database successfully:".$current_task;
						}
						else
						{
							echo "<br>Insert Into database failed:".$current_task;
						}
					}
				}
				echo "Complete<B>".( $run_num_once + 1 )."</B>, left <B>".( $TaskNum - $run_num_once )."</B>";
			}
			else
			{
				echo "Notice: Task Session Not Found";
			}
			$TaskData = serialize( $TaskData );
			$crawler->flushData( );
			$crawler->addData( "TaskData", $TaskData );
			$crawler->addData( "TaskTime", time( ) );
			$crawler->taskUpdate( $IN[TaskID] );
			echo "<br>max_running_time:".$max_running_time;
			echo "<br>run_num_once:".$run_num_once;
			$totaltime = $debugger->endTimer( );
			$run_num = floor( $max_running_time / ceil( $totaltime / $run_num_once ) );
			if ( $Crawler_Page )
			{
				$referer = $base_url."o=Crawlering&mode=running&TaskID=".$IN['TaskID']."&CateID=".$CateInfo['CateID']."&run_num=".$run_num."&Page=".$IN['Page'];
			}
			else
			{
				$referer = $base_url."o=Crawlering&mode=running&TaskID=".$IN['TaskID']."&CateID=".$CateInfo['CateID']."&run_num=".$run_num;
			}
			echo "<meta http-equiv=\"refresh\" content=\"2;url={$referer}\">";  //执行下一次循环
		}
		break;
	case "task_init" :
	default :
		$linkArray = $crawler->indexParse( $index_link_space_pattern, $index_link_pattern );
		$Crawler_Task = $linkArray;
		$TaskID = Auth::makesessionkey( );
		$TaskData = serialize( $Crawler_Task );
		$crawler->flushData( );
		$crawler->addData( "TaskID", $TaskID );
		$crawler->addData( "TaskData", $TaskData );
		$crawler->addData( "TaskTime", time( ) );
		if ( $crawler->taskAdd( ) )
		{
			exit( $TaskID );
		}
		else
		{
			exit( " Task init failed" );
		}
		break;
}
echo "\r\n ";
?>
