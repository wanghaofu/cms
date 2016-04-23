<?php


function get_file_path( $class_path )
{
	if ( strpos( $class_path, "." ) !== FALSE )
	{
		$filename = str_replace( ".", DIRECTORY_SEPARATOR, $class_path );
		$filename = CLASS_PATH.DIRECTORY_SEPARATOR.$filename;
	}
	else
	{
		$filename = $class_path;
	}
	$ext = pathinfo( $filename, PATHINFO_EXTENSION );
	if ( $ext == "" )
	{
		$filename .= ".php";
	}
	if ( is_readable( $filename ) )
	{
		return realpath( $filename );
	}
	if ( is_array( $GLOBALS['CLASS_PATH'] ) )
	{
		$filename = str_replace( ".", DIRECTORY_SEPARATOR, $class_path );
		$ext = pathinfo( $filename, PATHINFO_EXTENSION );
		if ( $ext == "" )
		{
			$filename .= ".php";
		}
		foreach ( $GLOBALS['CLASS_PATH'] as $classdir )
		{
			$path = $classdir.DIRECTORY_SEPARATOR.$filename;
			if ( is_readable( $path ) )
			{
				return realpath( $path );
			}
		}
	}
	return FALSE;
}

function load_class( $class_path )
{
	$className = str_replace( ".", "_", $class_path );
	if ( class_exists( $className ) )
	{
		return TRUE;
	}
	$filename = get_file_path( $class_path );
	if ( $filename )
	{
		require_once( $filename );
		if ( class_exists( $className ) )
		{
			return TRUE;
		}
	}
	trigger_error( "{$filename} not exists, {$className} not found", E_USER_ERROR );
	return FALSE;
}

function &get_singleton( $class_path )
{
	static $objs = array( );
	$className = str_replace( ".", "_", $class_path );
	if ( isset( $objs[$className] ) )
	{
		return $objs[$className];
	}
	if ( !class_exists( $className ) )
	{
		load_class( $class_path );
	}
	$objs[$className] = new $className( );
	return $objs[$className];
}

function import( $package )
{
	$package_class_path = str_replace( ".", DS, $package );
	$package_class_path = CLS_PATH.$package_class_path.".php";
	if ( file_exists( $package_class_path ) )
	{
		require_once( $package_class_path );
	}
	else
	{
		exit( "Fatal Errors: {$package_class_path} does not exists!" );
	}
}

function logger( $_msg, $_level = "INFO" )
{
	$debuginfo = debug_backtrace( );
	$file = pathinfo( $debuginfo[0]['file'] );
	if ( is_array( $_msg ) )
	{
		$_msg = "Array ".var_export( $_msg, TRUE );
	}
	else if ( is_bool( $_msg ) )
	{
		$_msg = $_msg ? "Boolean TRUE" : "Boolean FALSE";
	}
	else if ( is_int( $_msg ) )
	{
		$_msg = "INT ".$_msg;
	}
	switch ( $_level )
	{
	case "error" :
		echo "ERROR [{$file['basename']}:{$debuginfo[0]['line']}] ".$_msg."\n";
		break;
	case "INFO" :
	case "info" :
	default :
		echo "INFO [{$file['basename']}:{$debuginfo[0]['line']}] ".$_msg."\n";
		break;
	}
}

function CreationUser( $_userid, $field = "uName" )
{
	global $table;
	global $db;
	$sql = "SELECT {$field} FROM {$table->user}  WHERE uId='{$_userid}'  ";
	$result = $db->getRow( $sql );
	if ( $field == "*" )
	{
		return $result;
	}
	else
	{
		return $result[$field];
	}
}

function load_lang( $_string_path )
{
	return include( $_string_path );
}

function add_next_page_link( $_content, $_mode = 0 )
{
	$pageNav = $GLOBALS['_CMS']['ContentPageNav'];
	$pagenum = $GLOBALS['_CMS']['CurrentPage'] + 1;
	$counter = 0;
	foreach ( $pageNav as $key => $var )
	{
		if ( $counter == $pagenum )
		{
			$url = $var['URL'];
		}
		++$counter;
	}
	if ( empty( $url ) )
	{
		$params = array(
			"action" => "LIST",
			"return" => "List",
			"nodeid" => $GLOBALS['_CMS']['NodeID'],
			"num" => "1",
			"where" => "i.PublishDate < ".$GLOBALS['_CMS']['PublishDate']." "
		);
		$List = cms_list( $params );
		if ( !empty( $List ) )
		{
			foreach ( $List as $key => $var )
			{
				$url = $var['URL'];
			}
		}
		if ( empty( $url ) )
		{
			$firstPage = array_shift( $pageNav );
			$url = $firstPage['URL'];
		}
	}
	if ( $_mode == 1 )
	{
		$pattern = "/<img.*src=.*([^\"'].*)[\"']?[\\s].*>/isU";
		@preg_match_all( $pattern, $_content, $out );
		foreach ( $out[0] as $key => $var )
		{
			$_content = str_replace( $var, "<a href=\"".$url."\">".$var."</a>", $_content );
		}
	}
	else
	{
		$_content = "<a href=\"".$url."\">".$_content."</a>";
	}
	return $_content;
}

function de( $str ,$exit=false ,$trace=true){
    global $debugnum;
    $debugnum++;
    $debugInfo =  debug_backtrace();
    if( php_sapi_name() === 'cli')
    {
        $cli=true;
    }else{
        $cli = false;
     }
     if($cli)
     {
         $break_line = "\n";
     }
    echo "<div style='font-size:14px;background-color:#f1f6f7'>{$break_line}";
    echo "<div style='font-size:16px;background-color:dfe5e6;color:#001eff;font-weight:bold'>{$break_line}";
    foreach( $debugInfo as $key=>$value ){
        if($key==0 ){
            echo "*** <span style='font-size:18px'>{$debugnum}</span> {$value['file']} (debug in file)  {$value['line']} (row) </br>{$break_line}";
        } else {
            if ( $track )
            {
                echo "&nbsp;&nbsp;<span style='font-size:12px;'>>> include in file:{$value['file']} line:{$value['line']} row </br></span>{$break_line}";
            } else {
                break;
            }
        }
    }
    echo "</div>";
    echo "<pre>{$break_line}";
    if ( !isset( $str ) )
    {
        echo 'the vars in not set!';
    }elseif ( is_numeric($str) ){
        echo $str;
    }elseif ( is_object( $str ) ){
        print_r( $str);
    }elseif ( is_string( $str )){
        echo $str;
    }elseif( is_array( $str ) ){
        print_r( $str );
    }elseif ( is_null( $str )){
        echo 'the vars is null ';
    }elseif( is_bool( $str ) ){
        echo $str;
    }
    echo '</pre>';
    echo "</div>";
    if ( $exit ){
        exit();
    }
}

if ( PHP_VERSION_5 )
{
	include_once( dirname( __FILE__ )."/functions.php5.php" );
}
else
{
	include_once( dirname( __FILE__ )."/functions.php4.php" );
}
$GLOBALS['GLOBALS']['CLASS_PATH'] = array( );
if ( defined( "ROOT_PATH" ) )
{
	$GLOBALS['GLOBALS']['CLASS_PATH'][] = ROOT_PATH."lib/";
}
if ( defined( "CLS_PATH" ) )
{
	$GLOBALS['GLOBALS']['CLASS_PATH'][] = CLS_PATH;
}
?>
