<?php


class kTemplate
{

	var $template_dir = "templates";
	var $compile_dir = "templates_c";
	var $compile_check = true;
	var $force_compile = false;
	var $caching = 0;
	var $cache_dir = "cache";
	var $lang_dir = "";
	var $compile_lang = true;
	var $cache_lifetime = 3600;
	var $client_caching = false;
	var $left_delimiter = "<";
	var $right_delimiter = ">";
	var $compilefile_prefix = "%%c_";
	var $tag_left_delim = "[";
	var $tag_right_delim = "]";
	var $registerParseFunArray = array( );
	var $regPreFilterArray = array( );
	var $_tpl_vars = array( );
	var $checkTplModify = true;
	var $forceCompile = false;
	var $autoRepair = false;
	var $enableMark = true;
	var $source = NULL;
	var $compiler_file = "kTemplate_Compiler.class.php";
	var $compiler_class = "kTemplate_Compiler";

	function kTemplate( $params = NULL )
	{
		if ( isset( $params['template_dir'] ) )
		{
			$this->template_dir = $params['template_dir'];
		}
		else
		{
			$this->template_dir = SYS_PATH."skin/admin/";
		}
		if ( isset( $params['compile_dir'] ) )
		{
			$this->compile_dir = $params['compile_dir'];
		}
		else
		{
			$this->compile_dir = CACHE_DIR."templates_c/";
		}
		if ( isset( $params['cache_dir'] ) )
		{
			$this->cache_dir = $params['cache_dir'];
		}
		else
		{
			$this->cache_dir = CACHE_DIR."cache/";
		}
		if ( isset( $params['lang_dir'] ) )
		{
			$this->cache_dir = $params['lang_dir'];
		}
	}

	function assign( $tpl_var, $value = null )
	{
		if ( is_array( $tpl_var ) )
		{
			foreach ( $tpl_var as $key => $val )
			{
				if ( $key != "" )
				{
					$this->_tpl_vars[$key] = $val;
				}
			}
		}
		else if ( $tpl_var != "" )
		{
			$this->_tpl_vars[$tpl_var] = $value;
		}
	}

	function assign_by_ref( $tpl_var, &$value )
	{
		if ( $tpl_var != "" )
		{
			$this->_tpl_vars[$tpl_var] =& $value;
		}
	}

	function _compile( $file_name )
	{
		if ( file_exists( KTPL_DIR.$this->compiler_file ) )
		{
			require_once( KTPL_DIR.$this->compiler_file );
		}
		else
		{
			exit( "Compiler does not exits!" );
		}
		$kTemplate_compiler = new $this->compiler_class( );
		$kTemplate_compiler->template_dir = $this->template_dir;
		$kTemplate_compiler->compile_dir = $this->compile_dir;
		$kTemplate_compiler->lang_dir = $this->lang_dir;
		$kTemplate_compiler->compile_lang = $this->compile_lang;
		$kTemplate_compiler->registerParseFunArray = $this->registerParseFunArray;
		$kTemplate_compiler->regPreFilterArray = $this->regPreFilterArray;
		$kTemplate_compiler->compilefile_prefix = $this->compilefile_prefix;
		$kTemplate_compiler->left_delimiter = $this->left_delimiter;
		$kTemplate_compiler->right_delimiter = $this->right_delimiter;
		$kTemplate_compiler->tag_left_delim = $this->tag_left_delim;
		$kTemplate_compiler->tag_right_delim = $this->tag_right_delim;
		$kTemplate_compiler->autoRepair = $this->autoRepair;
		$kTemplate_compiler->template_name = $this->template_name;
		if ( $kTemplate_compiler->compile( $file_name, $this->compilefile_prefix.$this->format( $this->template_name ) ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function _compile_output( &$content )
	{
		if ( file_exists( KTPL_DIR.$this->compiler_file ) )
		{
			require_once( KTPL_DIR.$this->compiler_file );
		}
		else
		{
			exit( "Compiler does not exits!" );
		}
		$kTemplate_compiler = new $this->compiler_class( );
		$kTemplate_compiler->template_dir = $this->template_dir;
		$kTemplate_compiler->compile_dir = $this->compile_dir;
		$kTemplate_compiler->lang_dir = $this->lang_dir;
		$kTemplate_compiler->compile_lang = $this->compile_lang;
		$kTemplate_compiler->registerParseFunArray = $this->registerParseFunArray;
		$kTemplate_compiler->regPreFilterArray = $this->regPreFilterArray;
		$kTemplate_compiler->compilefile_prefix = $this->compilefile_prefix;
		$kTemplate_compiler->left_delimiter = $this->left_delimiter;
		$kTemplate_compiler->right_delimiter = $this->right_delimiter;
		$kTemplate_compiler->tag_left_delim = $this->tag_left_delim;
		$kTemplate_compiler->tag_right_delim = $this->tag_right_delim;
		$kTemplate_compiler->autoRepair = $this->autoRepair;
		$kTemplate_compiler->_compile_php( $content );
	}

	function registerParseFun( $functionName )
	{
		$this->registerParseFunArray[] = $functionName;
	}

	function registerPreFilter( $functionName )
	{
		$this->regPreFilterArray[] = $functionName;
	}

	function registerCacheFun( $functionName )
	{
		$this->registerCacheFunArray[] = $functionName;
	}

	function cachePreFilter( &$contents )
	{
		if ( !empty( $this->registerCacheFunArray ) )
		{
			foreach ( $this->registerCacheFunArray as $var )
			{
				if ( function_exists( $var ) )
				{
					$contents = $var( $contents );
				}
			}
		}
	}

	function isCompiled( )
	{
		if ( !file_exists( $this->compile_name ) )
		{
			return false;
		}
		$expire = filemtime( $this->compile_name ) == filemtime( $this->template_name ) ? true : false;
		if ( $expire )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function is_cached( $file_name, $cache_id = NULL )
	{
		if ( $this->cached )
		{
			return true;
		}
		$this->cache_name = $this->cache_dir.md5( $file_name.$cache_id ).".cache";
		if ( !file_exists( $this->cache_name ) )
		{
			return false;
		}
		if ( !( $mtime = filemtime( $this->cache_name ) ) )
		{
			return false;
		}
		$this->cache_expire_time = $mtime + $this->cache_lifetime - time( );
		if ( $mtime + $this->cache_lifetime < time( ) )
		{
			unlink( $this->cache_name );
			return false;
		}
		else
		{
			$this->cached = true;
			return true;
		}
	}

	function clear_all_assign( )
	{
		$this->_tpl_vars = array( );
	}

	function _fetch( $file, $compile = 0 )
	{
		ob_start( );
		if ( is_file( $this->lang_name ) )
		{
			include_once( $this->lang_name );
		}
		if ( is_file( $this->global_lang_name ) )
		{
			include( $this->global_lang_name );
		}
		include( $file );
		$contents = ob_get_contents( );
		ob_end_clean( );
		if ( $this->enableMark )
		{
			$contents = empty( $SYS_ENV['CMSware_Mark'] ) ? $contents : $contents.$SYS_ENV['CMSware_Mark'];
		}
		$contents = empty( $SYS_ENV['CMSware_Powered'] ) ? $contents : preg_replace( "'(<title>)(.*)(</title>)'isU", "\\1\\2".$SYS_ENV['CMSware_Powered']."\\3", $contents );
		if ( $compile )
		{
			$this->_compile_output( $contents );
		}
		return $contents;
	}

	function format( $file_name )
	{
		$file_name = str_replace( ":", "_", $file_name );
		$file_name = str_replace( "/", "@", $file_name );
		$file_name = str_replace( "\\", "@", $file_name );
		$file_name = str_replace( "..", "^", $file_name );
		return $file_name;
	}

	function fetch( $file_name, $compile = 0 )
	{
		$this->template_name = $this->template_dir.$file_name;
		$this->compile_name = $this->compile_dir.$this->compilefile_prefix.$this->format( $this->template_name );
		$this->lang_name = $this->lang_dir.$file_name.".php";
		if ( $this->forceCompile )
		{
			if ( $this->_compile( $this->template_name ) )
			{
				return $this->_fetch( $this->compile_name, $compile );
			}
		}
		else
		{
			if ( $this->checkTplModify )
			{
				if ( !$this->isCompiled( ) )
				{
					if ( $this->_compile( $this->template_name ) )
					{
						return $this->_fetch( $this->compile_name, $compile );
					}
				}
				else
				{
					return $this->_fetch( $this->compile_name, $compile );
				}
			}
			else
			{
				ob_start( );
				if ( file_exists( $this->lang_name ) )
				{
					include( $this->lang_name );
				}
				include( $this->global_lang_name );
				if ( !include( $this->compile_name ) )
				{
					if ( $this->_compile( $this->template_name ) )
					{
						include( $this->compile_name );
					}
				}
				$contents = ob_get_contents( );
				ob_end_clean( );
				if ( $this->enableMark )
				{
					$contents = empty( $SYS_ENV['CMSware_Mark'] ) ? $contents : $contents.$SYS_ENV['CMSware_Mark'];
				}
				if ( $compile )
				{
					$contents = $this->_compile_output( $contents );
				}
				return $contents;
			}
		}
	}

	function fetch_cache( $file_name, $cache_id, $compile = 0 )
	{
		$this->cache_name = $this->cache_dir.md5( $file_name.$cache_id ).".cache";
		if ( $fp = @fopen( $this->cache_name, "r" ) )
		{
			$contents = fread( $fp, filesize( $this->cache_name ) );
			fclose( $fp );
			return $contents;
		}
		else
		{
			$contents = $this->fetch( $file_name, $compile );
			$this->cachePreFilter( $contents );
			if ( File::autowrite( $this->cache_name, $contents ) )
			{
				}
			else
			{
				exit( "Unable to write cache." );
			}
			return $contents;
		}
	}

	function clear_cache( $file_name, $cache_id )
	{
		$this->cache_name = $this->cache_dir.md5( $file_name.$cache_id ).".cache";
		if ( file_exists( $this->cache_name ) )
		{
			return unlink( $this->cache_name );
		}
		else
		{
			return true;
		}
	}

	function display( $file_name, $enable_gzip = NULL )
	{
		if ( !empty( $enable_gzip ) || $SYS_ENV['enable_gzip'] )
		{
			$buffer = $this->fetch( $file_name );
			if ( !ini_get( "zlib.output_compression" ) )
			{
				ob_start( "ob_gzhandler" );
			}
			print $buffer;
		}
		else
		{
			print $this->fetch( $file_name );
		}
	}

	function display_cache( $file_name, $cache_id = NULL, $enable_gzip = NULL )
	{
		if ( $this->client_caching )
		{
			header( "Last-Modified: ".gmdate( "D, d M Y H:i:s", time( ) + $this->cache_expire_time )." GMT" );
			header( "Expires: ".gmdate( "D, d M Y H:i:s", time( ) + $this->cache_expire_time )." GMT" );
		}
		if ( empty( $enable_gzip ) )
		{
			print $this->fetch_cache( $file_name, $cache_id );
		}
		else
		{
			$buffer = $this->fetch_cache( $file_name, $cache_id );
			ob_start( "ob_gzhandler" );
			print $buffer;
		}
	}

	function run_cache( $file_name, $cache_id = NULL, $enable_gzip = NULL )
	{
		$this->cache_name = $this->cache_dir.md5( $file_name.$cache_id ).".cache";
		if ( empty( $enable_gzip ) )
		{
			if ( file_exists( $this->cache_name ) )
			{
				include( $this->cache_name );
			}
			else
			{
				$contents = $this->fetch( $file_name, 1 );
				if ( File::autowrite( $this->cache_name, $contents ) )
				{
					}
				else
				{
					exit( "Unable to write cache." );
				}
				include( $this->cache_name );
			}
		}
		else
		{
			ob_start( "ob_gzhandler" );
			if ( file_exists( $this->cache_name ) )
			{
				include( $this->cache_name );
			}
			else
			{
				$contents = $this->fetch( $file_name, 1 );
				if ( File::autowrite( $this->cache_name, $contents ) )
				{
					}
				else
				{
					exit( "Unable to write cache." );
				}
				include( $this->cache_name );
			}
		}
	}

}

?>
