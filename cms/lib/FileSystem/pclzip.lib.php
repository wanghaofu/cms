<?php


class PclZip
{

	var $zipname = "";
	var $zip_fd = 0;
	var $error_code = 1;
	var $error_string = "";
	var $magic_quotes_status = NULL;

	function PclZip( $p_zipname )
	{
		if ( !function_exists( "gzopen" ) )
		{
			exit( "Abort ".basename( __FILE__ )." : Missing zlib extensions" );
		}
		$this->zipname = $p_zipname;
		$this->zip_fd = 0;
		$this->magic_quotes_status = -1;
		return;
	}

	function create( $p_filelist )
	{
		$v_result = 1;
		$this->privErrorReset( );
		$v_options = array( );
		$v_add_path = "";
		$v_remove_path = "";
		$v_remove_all_path = false;
		$v_options[PCLZIP_OPT_NO_COMPRESSION] = FALSE;
		$v_size = func_num_args( );
		if ( 1 < $v_size )
		{
			$v_arg_list =& func_get_args( );
			array_shift( $v_arg_list );
			--$v_size;
			if ( is_integer( $v_arg_list[0] ) && 77000 < $v_arg_list[0] )
			{
				$v_result = $this->privParseOptions( $v_arg_list, $v_size, $v_options, array( "optional", "optional", "optional", "optional", "optional", "optional", "optional" ) );
				if ( $v_result != 1 )
				{
					return 0;
				}
				if ( isset( $v_options[PCLZIP_OPT_ADD_PATH] ) )
				{
					$v_add_path = $v_options[PCLZIP_OPT_ADD_PATH];
				}
				if ( isset( $v_options[PCLZIP_OPT_REMOVE_PATH] ) )
				{
					$v_remove_path = $v_options[PCLZIP_OPT_REMOVE_PATH];
				}
				if ( isset( $v_options[PCLZIP_OPT_REMOVE_ALL_PATH] ) )
				{
					$v_remove_all_path = $v_options[PCLZIP_OPT_REMOVE_ALL_PATH];
					$v_add_path = $v_arg_list[0];
				}
			}
			else if ( $v_size == 2 )
			{
				$v_remove_path = $v_arg_list[1];
			}
			else if ( 2 < $v_size )
			{
				PclZip::priverrorlog( PCLZIP_ERR_INVALID_PARAMETER, "Invalid number / type of arguments" );
				return 0;
			}
		}
		$p_result_list = array( );
		if ( is_array( $p_filelist ) )
		{
			$v_result = $this->privCreate( $p_filelist, $p_result_list, $v_add_path, $v_remove_path, $v_remove_all_path, $v_options );
		}
		else if ( is_string( $p_filelist ) )
		{
			$v_list = explode( PCLZIP_SEPARATOR, $p_filelist );
			$v_result = $this->privCreate( $v_list, $p_result_list, $v_add_path, $v_remove_path, $v_remove_all_path, $v_options );
		}
		else
		{
			PclZip::priverrorlog( PCLZIP_ERR_INVALID_PARAMETER, "Invalid variable type p_filelist" );
			$v_result = PCLZIP_ERR_INVALID_PARAMETER;
		}
		if ( $v_result != 1 )
		{
			return 0;
		}
		return $p_result_list;
	}

	function add( $p_filelist )
	{
		$v_result = 1;
		$this->privErrorReset( );
		$v_options = array( );
		$v_add_path = "";
		$v_remove_path = "";
		$v_remove_all_path = false;
		$v_options[PCLZIP_OPT_NO_COMPRESSION] = FALSE;
		$v_size = func_num_args( );
		if ( 1 < $v_size )
		{
			$v_arg_list =& func_get_args( );
			array_shift( $v_arg_list );
			--$v_size;
			if ( is_integer( $v_arg_list[0] ) && 77000 < $v_arg_list[0] )
			{
				$v_result = $this->privParseOptions( $v_arg_list, $v_size, $v_options, array( "optional", "optional", "optional", "optional", "optional", "optional", "optional", "optional", "optional" ) );
				if ( $v_result != 1 )
				{
					return 0;
				}
				if ( isset( $v_options[PCLZIP_OPT_ADD_PATH] ) )
				{
					$v_add_path = $v_options[PCLZIP_OPT_ADD_PATH];
				}
				if ( isset( $v_options[PCLZIP_OPT_REMOVE_PATH] ) )
				{
					$v_remove_path = $v_options[PCLZIP_OPT_REMOVE_PATH];
				}
				if ( isset( $v_options[PCLZIP_OPT_REMOVE_ALL_PATH] ) )
				{
					$v_remove_all_path = $v_options[PCLZIP_OPT_REMOVE_ALL_PATH];
					$v_add_path = $v_arg_list[0];
				}
			}
			else if ( $v_size == 2 )
			{
				$v_remove_path = $v_arg_list[1];
			}
			else if ( 2 < $v_size )
			{
				PclZip::priverrorlog( PCLZIP_ERR_INVALID_PARAMETER, "Invalid number / type of arguments" );
				return 0;
			}
		}
		$p_result_list = array( );
		if ( is_array( $p_filelist ) )
		{
			$v_result = $this->privAdd( $p_filelist, $p_result_list, $v_add_path, $v_remove_path, $v_remove_all_path, $v_options );
		}
		else if ( is_string( $p_filelist ) )
		{
			$v_list = explode( PCLZIP_SEPARATOR, $p_filelist );
			$v_result = $this->privAdd( $v_list, $p_result_list, $v_add_path, $v_remove_path, $v_remove_all_path, $v_options );
		}
		else
		{
			PclZip::priverrorlog( PCLZIP_ERR_INVALID_PARAMETER, "Invalid variable type p_filelist" );
			$v_result = PCLZIP_ERR_INVALID_PARAMETER;
		}
		if ( $v_result != 1 )
		{
			return 0;
		}
		return $p_result_list;
	}

	function listContent( )
	{
		$v_result = 1;
		$this->privErrorReset( );
		if ( !$this->privCheckFormat( ) )
		{
			return 0;
		}
		$p_list = array( );
		if ( ( $v_result = $this->privList( $p_list ) ) != 1 )
		{
			unset( $p_list );
			return 0;
		}
		return $p_list;
	}

	function extract( )
	{
		$v_result = 1;
		$this->privErrorReset( );
		if ( !$this->privCheckFormat( ) )
		{
			return 0;
		}
		$v_options = array( );
		$v_path = "";
		$v_remove_path = "";
		$v_remove_all_path = false;
		$v_size = func_num_args( );
		$v_options[PCLZIP_OPT_EXTRACT_AS_STRING] = FALSE;
		if ( 0 < $v_size )
		{
			$v_arg_list =& func_get_args( );
			if ( is_integer( $v_arg_list[0] ) && 77000 < $v_arg_list[0] )
			{
				$v_result = $this->privParseOptions( $v_arg_list, $v_size, $v_options, array( "optional", "optional", "optional", "optional", "optional", "optional", "optional", "optional", "optional", "optional", "optional", "optional", "optional", "optional", "optional" ) );
				if ( $v_result != 1 )
				{
					return 0;
				}
				if ( isset( $v_options[PCLZIP_OPT_PATH] ) )
				{
					$v_path = $v_options[PCLZIP_OPT_PATH];
				}
				if ( isset( $v_options[PCLZIP_OPT_REMOVE_PATH] ) )
				{
					$v_remove_path = $v_options[PCLZIP_OPT_REMOVE_PATH];
				}
				if ( isset( $v_options[PCLZIP_OPT_REMOVE_ALL_PATH] ) )
				{
					$v_remove_all_path = $v_options[PCLZIP_OPT_REMOVE_ALL_PATH];
				}
				if ( isset( $v_options[PCLZIP_OPT_ADD_PATH] ) )
				{
					if ( 0 < strlen( $v_path ) && substr( $v_path, -1 ) != "/" )
					{
						$v_path .= "/";
					}
					$v_path .= $v_options[PCLZIP_OPT_ADD_PATH];
					$v_path = $v_arg_list[0];
				}
			}
			else if ( $v_size == 2 )
			{
				$v_remove_path = $v_arg_list[1];
			}
			else if ( 2 < $v_size )
			{
				PclZip::priverrorlog( PCLZIP_ERR_INVALID_PARAMETER, "Invalid number / type of arguments" );
				return 0;
			}
		}
		$p_list = array( );
		$v_result = $this->privExtractByRule( $p_list, $v_path, $v_remove_path, $v_remove_all_path, $v_options );
		if ( $v_result < 1 )
		{
			unset( $p_list );
			return 0;
		}
		return $p_list;
	}

	function extractByIndex( $p_index )
	{
		$v_result = 1;
		$this->privErrorReset( );
		if ( !$this->privCheckFormat( ) )
		{
			return 0;
		}
		$v_options = array( );
		$v_path = "";
		$v_remove_path = "";
		$v_remove_all_path = false;
		$v_size = func_num_args( );
		$v_options[PCLZIP_OPT_EXTRACT_AS_STRING] = FALSE;
		if ( 1 < $v_size )
		{
			$v_arg_list =& func_get_args( );
			array_shift( $v_arg_list );
			--$v_size;
			if ( is_integer( $v_arg_list[0] ) && 77000 < $v_arg_list[0] )
			{
				$v_result = $this->privParseOptions( $v_arg_list, $v_size, $v_options, array( "optional", "optional", "optional", "optional", "optional", "optional", "optional", "optional", "optional", "optional" ) );
				if ( $v_result != 1 )
				{
					return 0;
				}
				if ( isset( $v_options[PCLZIP_OPT_PATH] ) )
				{
					$v_path = $v_options[PCLZIP_OPT_PATH];
				}
				if ( isset( $v_options[PCLZIP_OPT_REMOVE_PATH] ) )
				{
					$v_remove_path = $v_options[PCLZIP_OPT_REMOVE_PATH];
				}
				if ( isset( $v_options[PCLZIP_OPT_REMOVE_ALL_PATH] ) )
				{
					$v_remove_all_path = $v_options[PCLZIP_OPT_REMOVE_ALL_PATH];
				}
				if ( isset( $v_options[PCLZIP_OPT_ADD_PATH] ) )
				{
					if ( 0 < strlen( $v_path ) && substr( $v_path, -1 ) != "/" )
					{
						$v_path .= "/";
					}
					$v_path .= $v_options[PCLZIP_OPT_ADD_PATH];
				}
				if ( !isset( $v_options[PCLZIP_OPT_EXTRACT_AS_STRING] ) )
				{
					$v_options[PCLZIP_OPT_EXTRACT_AS_STRING] = FALSE;
					$v_path = $v_arg_list[0];
				}
			}
			else if ( $v_size == 2 )
			{
				$v_remove_path = $v_arg_list[1];
			}
			else if ( 2 < $v_size )
			{
				PclZip::priverrorlog( PCLZIP_ERR_INVALID_PARAMETER, "Invalid number / type of arguments" );
				return 0;
			}
		}
		$v_arg_trick = array(
			PCLZIP_OPT_BY_INDEX,
			$p_index
		);
		$v_options_trick = array( );
		$v_result = $this->privParseOptions( $v_arg_trick, sizeof( $v_arg_trick ), $v_options_trick, array( "optional" ) );
		if ( $v_result != 1 )
		{
			return 0;
		}
		$v_options[PCLZIP_OPT_BY_INDEX] = $v_options_trick[PCLZIP_OPT_BY_INDEX];
		if ( ( $v_result = $this->privExtractByRule( $p_list, $v_path, $v_remove_path, $v_remove_all_path, $v_options ) ) < 1 )
		{
			return 0;
		}
		return $p_list;
	}

	function delete( )
	{
		$v_result = 1;
		$this->privErrorReset( );
		if ( !$this->privCheckFormat( ) )
		{
			return 0;
		}
		$v_options = array( );
		$v_size = func_num_args( );
		if ( 0 < $v_size )
		{
			$v_arg_list =& func_get_args( );
			$v_result = $this->privParseOptions( $v_arg_list, $v_size, $v_options, array( "optional", "optional", "optional", "optional" ) );
			if ( $v_result != 1 )
			{
				return 0;
			}
		}
		$this->privDisableMagicQuotes( );
		$v_list = array( );
		if ( ( $v_result = $this->privDeleteByRule( $v_list, $v_options ) ) != 1 )
		{
			$this->privSwapBackMagicQuotes( );
			unset( $v_list );
			return 0;
		}
		$this->privSwapBackMagicQuotes( );
		return $v_list;
	}

	function deleteByIndex( $p_index )
	{
		$p_list = $this->delete( PCLZIP_OPT_BY_INDEX, $p_index );
		return $p_list;
	}

	function properties( )
	{
		$this->privErrorReset( );
		$this->privDisableMagicQuotes( );
		if ( !$this->privCheckFormat( ) )
		{
			$this->privSwapBackMagicQuotes( );
			return 0;
		}
		$v_prop = array( );
		$v_prop['comment'] = "";
		$v_prop['nb'] = 0;
		$v_prop['status'] = "not_exist";
		if ( @is_file( $this->zipname ) )
		{
			if ( ( $this->zip_fd = fopen( $this->zipname, "rb" ) ) == 0 )
			{
				$this->privSwapBackMagicQuotes( );
				PclZip::priverrorlog( PCLZIP_ERR_READ_OPEN_FAIL, "Unable to open archive '".$this->zipname."' in binary read mode" );
				return 0;
			}
			$v_central_dir = array( );
			if ( ( $v_result = $this->privReadEndCentralDir( $v_central_dir ) ) != 1 )
			{
				$this->privSwapBackMagicQuotes( );
				return 0;
			}
			$this->privCloseFd( );
			$v_prop['comment'] = $v_central_dir['comment'];
			$v_prop['nb'] = $v_central_dir['entries'];
			$v_prop['status'] = "ok";
		}
		$this->privSwapBackMagicQuotes( );
		return $v_prop;
	}

	function duplicate( $p_archive )
	{
		$v_result = 1;
		$this->privErrorReset( );
		if ( is_object( $p_archive ) && get_class( $p_archive ) == "pclzip" )
		{
			$v_result = $this->privDuplicate( $p_archive->zipname );
		}
		else if ( is_string( $p_archive ) )
		{
			if ( !is_file( $p_archive ) )
			{
				PclZip::priverrorlog( PCLZIP_ERR_MISSING_FILE, "No file with filename '".$p_archive."'" );
				$v_result = PCLZIP_ERR_MISSING_FILE;
			}
			else
			{
				$v_result = $this->privDuplicate( $p_archive );
			}
		}
		else
		{
			PclZip::priverrorlog( PCLZIP_ERR_INVALID_PARAMETER, "Invalid variable type p_archive_to_add" );
			$v_result = PCLZIP_ERR_INVALID_PARAMETER;
		}
		return $v_result;
	}

	function merge( $p_archive_to_add )
	{
		$v_result = 1;
		$this->privErrorReset( );
		if ( !$this->privCheckFormat( ) )
		{
			return 0;
		}
		if ( is_object( $p_archive_to_add ) && get_class( $p_archive_to_add ) == "pclzip" )
		{
			$v_result = $this->privMerge( $p_archive_to_add );
		}
		else if ( is_string( $p_archive_to_add ) )
		{
			$v_object_archive = new PclZip( $p_archive_to_add );
			$v_result = $this->privMerge( $v_object_archive );
		}
		else
		{
			PclZip::priverrorlog( PCLZIP_ERR_INVALID_PARAMETER, "Invalid variable type p_archive_to_add" );
			$v_result = PCLZIP_ERR_INVALID_PARAMETER;
		}
		return $v_result;
	}

	function errorCode( )
	{
		if ( PCLZIP_ERROR_EXTERNAL == 1 )
		{
			return pclerrorcode( );
		}
		else
		{
			return $this->error_code;
		}
	}

	function errorName( $p_with_code = false )
	{
		$v_name = array( "PCLZIP_ERR_NO_ERROR", "PCLZIP_ERR_WRITE_OPEN_FAIL", "PCLZIP_ERR_READ_OPEN_FAIL", "PCLZIP_ERR_INVALID_PARAMETER", "PCLZIP_ERR_MISSING_FILE", "PCLZIP_ERR_FILENAME_TOO_LONG", "PCLZIP_ERR_INVALID_ZIP", "PCLZIP_ERR_BAD_EXTRACTED_FILE", "PCLZIP_ERR_DIR_CREATE_FAIL", "PCLZIP_ERR_BAD_EXTENSION", "PCLZIP_ERR_BAD_FORMAT", "PCLZIP_ERR_DELETE_FILE_FAIL", "PCLZIP_ERR_RENAME_FILE_FAIL", "PCLZIP_ERR_BAD_CHECKSUM", "PCLZIP_ERR_INVALID_ARCHIVE_ZIP", "PCLZIP_ERR_MISSING_OPTION_VALUE", "PCLZIP_ERR_INVALID_OPTION_VALUE", "PCLZIP_ERR_UNSUPPORTED_COMPRESSION", "PCLZIP_ERR_UNSUPPORTED_ENCRYPTION" );
		if ( isset( $v_name[$this->error_code] ) )
		{
			$v_value = $v_name[$this->error_code];
		}
		else
		{
			$v_value = "NoName";
		}
		if ( $p_with_code )
		{
			return $v_value." (".$this->error_code.")";
		}
		else
		{
			return $v_value;
		}
	}

	function errorInfo( $p_full = false )
	{
		if ( PCLZIP_ERROR_EXTERNAL == 1 )
		{
			return pclerrorstring( );
		}
		else if ( $p_full )
		{
			return $this->errorName( true )." : ".$this->error_string;
		}
		else
		{
			return $this->error_string." [code ".$this->error_code."]";
		}
	}

	function privCheckFormat( $p_level = 0 )
	{
		$v_result = true;
		clearstatcache( );
		$this->privErrorReset( );
		if ( !is_file( $this->zipname ) )
		{
			PclZip::priverrorlog( PCLZIP_ERR_MISSING_FILE, "Missing archive file '".$this->zipname."'" );
			return false;
		}
		if ( !is_readable( $this->zipname ) )
		{
			PclZip::priverrorlog( PCLZIP_ERR_READ_OPEN_FAIL, "Unable to read archive '".$this->zipname."'" );
			return false;
		}
		return $v_result;
	}

	function privParseOptions( &$p_options_list, $p_size, &$v_result_list, $v_requested_options = false )
	{
		$v_result = 1;
		$i = 0;
		while ( $i < $p_size )
		{
			if ( !isset( $v_requested_options[$p_options_list[$i]] ) )
			{
				PclZip::priverrorlog( PCLZIP_ERR_INVALID_PARAMETER, "Invalid optional parameter '".$p_options_list[$i]."' for this method" );
				return PclZip::errorcode( );
			}
			switch ( $p_options_list[$i] )
			{
			case PCLZIP_OPT_PATH :
			case PCLZIP_OPT_REMOVE_PATH :
			case PCLZIP_OPT_ADD_PATH :
				if ( $p_size <= $i + 1 )
				{
					PclZip::priverrorlog( PCLZIP_ERR_MISSING_OPTION_VALUE, "Missing parameter value for option '".pclziputiloptiontext( $p_options_list[$i] )."'" );
					return PclZip::errorcode( );
				}
				$v_result_list[$p_options_list[$i]] = pclziputiltranslatewinpath( $p_options_list[$i + 1], false );
				++$i;
				break;
			case PCLZIP_OPT_BY_NAME :
				if ( $p_size <= $i + 1 )
				{
					PclZip::priverrorlog( PCLZIP_ERR_MISSING_OPTION_VALUE, "Missing parameter value for option '".pclziputiloptiontext( $p_options_list[$i] )."'" );
					return PclZip::errorcode( );
				}
				if ( is_string( $p_options_list[$i + 1] ) )
				{
					$v_result_list[$p_options_list[$i]][0] = $p_options_list[$i + 1];
				}
				else if ( is_array( $p_options_list[$i + 1] ) )
				{
					$v_result_list[$p_options_list[$i]] = $p_options_list[$i + 1];
				}
				else
				{
					PclZip::priverrorlog( PCLZIP_ERR_INVALID_OPTION_VALUE, "Wrong parameter value for option '".pclziputiloptiontext( $p_options_list[$i] )."'" );
					return PclZip::errorcode( );
				}
				++$i;
				break;
			case PCLZIP_OPT_BY_EREG :
			case PCLZIP_OPT_BY_PREG :
				if ( $p_size <= $i + 1 )
				{
					PclZip::priverrorlog( PCLZIP_ERR_MISSING_OPTION_VALUE, "Missing parameter value for option '".pclziputiloptiontext( $p_options_list[$i] )."'" );
					return PclZip::errorcode( );
				}
				if ( is_string( $p_options_list[$i + 1] ) )
				{
					$v_result_list[$p_options_list[$i]] = $p_options_list[$i + 1];
				}
				else
				{
					PclZip::priverrorlog( PCLZIP_ERR_INVALID_OPTION_VALUE, "Wrong parameter value for option '".pclziputiloptiontext( $p_options_list[$i] )."'" );
					return PclZip::errorcode( );
				}
				++$i;
				break;
			case PCLZIP_OPT_COMMENT :
			case PCLZIP_OPT_ADD_COMMENT :
			case PCLZIP_OPT_PREPEND_COMMENT :
				if ( $p_size <= $i + 1 )
				{
					PclZip::priverrorlog( PCLZIP_ERR_MISSING_OPTION_VALUE, "Missing parameter value for option '".pclziputiloptiontext( $p_options_list[$i] )."'" );
					return PclZip::errorcode( );
				}
				if ( is_string( $p_options_list[$i + 1] ) )
				{
					$v_result_list[$p_options_list[$i]] = $p_options_list[$i + 1];
				}
				else
				{
					PclZip::priverrorlog( PCLZIP_ERR_INVALID_OPTION_VALUE, "Wrong parameter value for option '".pclziputiloptiontext( $p_options_list[$i] )."'" );
					return PclZip::errorcode( );
				}
				++$i;
				break;
			case PCLZIP_OPT_BY_INDEX :
				if ( $p_size <= $i + 1 )
				{
					PclZip::priverrorlog( PCLZIP_ERR_MISSING_OPTION_VALUE, "Missing parameter value for option '".pclziputiloptiontext( $p_options_list[$i] )."'" );
					return PclZip::errorcode( );
				}
				$v_work_list = array( );
				if ( is_string( $p_options_list[$i + 1] ) )
				{
					$p_options_list[$i + 1] = strtr( $p_options_list[$i + 1], " ", "" );
					$v_work_list = explode( ",", $p_options_list[$i + 1] );
				}
				else if ( is_integer( $p_options_list[$i + 1] ) )
				{
					$v_work_list[0] = $p_options_list[$i + 1]."-".$p_options_list[$i + 1];
				}
				else if ( is_array( $p_options_list[$i + 1] ) )
				{
					$v_work_list = $p_options_list[$i + 1];
				}
				else
				{
					PclZip::priverrorlog( PCLZIP_ERR_INVALID_OPTION_VALUE, "Value must be integer, string or array for option '".pclziputiloptiontext( $p_options_list[$i] )."'" );
					return PclZip::errorcode( );
				}
				$v_sort_flag = false;
				$v_sort_value = 0;
				$j = 0;
				for ( ;	$j < sizeof( $v_work_list );	++$j	)
				{
					$v_item_list = explode( "-", $v_work_list[$j] );
					$v_size_item_list = sizeof( $v_item_list );
					if ( $v_size_item_list == 1 )
					{
						$v_result_list[$p_options_list[$i]][$j]['start'] = $v_item_list[0];
						$v_result_list[$p_options_list[$i]][$j]['end'] = $v_item_list[0];
					}
					else if ( $v_size_item_list == 2 )
					{
						$v_result_list[$p_options_list[$i]][$j]['start'] = $v_item_list[0];
						$v_result_list[$p_options_list[$i]][$j]['end'] = $v_item_list[1];
					}
					else
					{
						PclZip::priverrorlog( PCLZIP_ERR_INVALID_OPTION_VALUE, "Too many values in index range for option '".pclziputiloptiontext( $p_options_list[$i] )."'" );
						return PclZip::errorcode( );
					}
					if ( $v_result_list[$p_options_list[$i]][$j]['start'] < $v_sort_value )
					{
						$v_sort_flag = true;
						PclZip::priverrorlog( PCLZIP_ERR_INVALID_OPTION_VALUE, "Invalid order of index range for option '".pclziputiloptiontext( $p_options_list[$i] )."'" );
						return PclZip::errorcode( );
					}
					$v_sort_value = $v_result_list[$p_options_list[$i]][$j]['start'];
				}
				++$i;
				break;
			case PCLZIP_OPT_REMOVE_ALL_PATH :
			case PCLZIP_OPT_EXTRACT_AS_STRING :
			case PCLZIP_OPT_NO_COMPRESSION :
			case PCLZIP_OPT_EXTRACT_IN_OUTPUT :
			case PCLZIP_OPT_REPLACE_NEWER :
			case PCLZIP_OPT_STOP_ON_ERROR :
				$v_result_list[$p_options_list[$i]] = true;
				break;
			case PCLZIP_OPT_SET_CHMOD :
				if ( $p_size <= $i + 1 )
				{
					PclZip::priverrorlog( PCLZIP_ERR_MISSING_OPTION_VALUE, "Missing parameter value for option '".pclziputiloptiontext( $p_options_list[$i] )."'" );
					return PclZip::errorcode( );
				}
				$v_result_list[$p_options_list[$i]] = $p_options_list[$i + 1];
				++$i;
				break;
			case PCLZIP_CB_PRE_EXTRACT :
			case PCLZIP_CB_POST_EXTRACT :
			case PCLZIP_CB_PRE_ADD :
			case PCLZIP_CB_POST_ADD :
				if ( $p_size <= $i + 1 )
				{
					PclZip::priverrorlog( PCLZIP_ERR_MISSING_OPTION_VALUE, "Missing parameter value for option '".pclziputiloptiontext( $p_options_list[$i] )."'" );
					return PclZip::errorcode( );
				}
				$v_function_name = $p_options_list[$i + 1];
				if ( !function_exists( $v_function_name ) )
				{
					PclZip::priverrorlog( PCLZIP_ERR_INVALID_OPTION_VALUE, "Function '".$v_function_name."()' is not an existing function for option '".pclziputiloptiontext( $p_options_list[$i] )."'" );
					return PclZip::errorcode( );
				}
				$v_result_list[$p_options_list[$i]] = $v_function_name;
				++$i;
				break;
			default :
				PclZip::priverrorlog( PCLZIP_ERR_INVALID_PARAMETER, "Unknown parameter '".$p_options_list[$i]."'" );
				return PclZip::errorcode( );
			}
			++$i;
		}
		if ( $v_requested_options !== false )
		{
			$key = reset( $v_requested_options );
			for ( ;	$key = key( $v_requested_options );	$key = next( $v_requested_options )	)
			{
				if ( !( $v_requested_options[$key] == "mandatory" ) && isset( $v_result_list[$key] ) )
				{
					PclZip::priverrorlog( PCLZIP_ERR_INVALID_PARAMETER, "Missing mandatory parameter ".pclziputiloptiontext( $key )."(".$key.")" );
					return PclZip::errorcode( );
				}
			}
		}
		return $v_result;
	}

	function privCreate( $p_list, &$p_result_list, $p_add_dir, $p_remove_dir, $p_remove_all_dir, &$p_options )
	{
		$v_result = 1;
		$v_list_detail = array( );
		$this->privDisableMagicQuotes( );
		if ( ( $v_result = $this->privOpenFd( "wb" ) ) != 1 )
		{
			return $v_result;
		}
		$v_result = $this->privAddList( $p_list, $p_result_list, $p_add_dir, $p_remove_dir, $p_remove_all_dir, $p_options );
		$this->privCloseFd( );
		$this->privSwapBackMagicQuotes( );
		return $v_result;
	}

	function privAdd( $p_list, &$p_result_list, $p_add_dir, $p_remove_dir, $p_remove_all_dir, &$p_options )
	{
		$v_result = 1;
		$v_list_detail = array( );
		if ( !is_file( $this->zipname ) || filesize( $this->zipname ) == 0 )
		{
			$v_result = $this->privCreate( $p_list, $p_result_list, $p_add_dir, $p_remove_dir, $p_remove_all_dir, $p_options );
			return $v_result;
		}
		$this->privDisableMagicQuotes( );
		if ( ( $v_result = $this->privOpenFd( "rb" ) ) != 1 )
		{
			$this->privSwapBackMagicQuotes( );
			return $v_result;
		}
		$v_central_dir = array( );
		if ( ( $v_result = $this->privReadEndCentralDir( $v_central_dir ) ) != 1 )
		{
			$this->privCloseFd( );
			$this->privSwapBackMagicQuotes( );
			return $v_result;
		}
		@rewind( $this->zip_fd );
		$v_zip_temp_name = PCLZIP_TEMPORARY_DIR.uniqid( "pclzip-" ).".tmp";
		if ( ( $v_zip_temp_fd = @fopen( $v_zip_temp_name, "wb" ) ) == 0 )
		{
			$this->privCloseFd( );
			$this->privSwapBackMagicQuotes( );
			PclZip::priverrorlog( PCLZIP_ERR_READ_OPEN_FAIL, "Unable to open temporary file '".$v_zip_temp_name."' in binary write mode" );
			return PclZip::errorcode( );
		}
		$v_size = $v_central_dir['offset'];
		while ( $v_size != 0 )
		{
			$v_read_size = $v_size < PCLZIP_READ_BLOCK_SIZE ? $v_size : PCLZIP_READ_BLOCK_SIZE;
			$v_buffer = fread( $this->zip_fd, $v_read_size );
			@fwrite( $v_zip_temp_fd, $v_buffer, $v_read_size );
			$v_size -= $v_read_size;
		}
		$v_swap = $this->zip_fd;
		$this->zip_fd = $v_zip_temp_fd;
		$v_zip_temp_fd = $v_swap;
		$v_header_list = array( );
		if ( ( $v_result = $this->privAddFileList( $p_list, $v_header_list, $p_add_dir, $p_remove_dir, $p_remove_all_dir, $p_options ) ) != 1 )
		{
			fclose( $v_zip_temp_fd );
			$this->privCloseFd( );
			@unlink( $v_zip_temp_name );
			$this->privSwapBackMagicQuotes( );
			return $v_result;
		}
		$v_offset = @ftell( $this->zip_fd );
		$v_size = $v_central_dir['size'];
		while ( $v_size != 0 )
		{
			$v_read_size = $v_size < PCLZIP_READ_BLOCK_SIZE ? $v_size : PCLZIP_READ_BLOCK_SIZE;
			$v_buffer = @fread( $v_zip_temp_fd, $v_read_size );
			@fwrite( $this->zip_fd, $v_buffer, $v_read_size );
			$v_size -= $v_read_size;
		}
		$i = 0;
		$v_count = 0;
		for ( ;	$i < sizeof( $v_header_list );	++$i	)
		{
			if ( $v_header_list[$i]['status'] == "ok" )
			{
				if ( ( $v_result = $this->privWriteCentralFileHeader( $v_header_list[$i] ) ) != 1 )
				{
					fclose( $v_zip_temp_fd );
					$this->privCloseFd( );
					@unlink( $v_zip_temp_name );
					$this->privSwapBackMagicQuotes( );
					return $v_result;
				}
				++$v_count;
			}
			$this->privConvertHeader2FileInfo( $v_header_list[$i], $p_result_list[$i] );
		}
		$v_comment = $v_central_dir['comment'];
		if ( isset( $p_options[PCLZIP_OPT_COMMENT] ) )
		{
			$v_comment = $p_options[PCLZIP_OPT_COMMENT];
		}
		if ( isset( $p_options[PCLZIP_OPT_ADD_COMMENT] ) )
		{
			$v_comment .= $p_options[PCLZIP_OPT_ADD_COMMENT];
		}
		if ( isset( $p_options[PCLZIP_OPT_PREPEND_COMMENT] ) )
		{
			$v_comment = $p_options[PCLZIP_OPT_PREPEND_COMMENT].$v_comment;
		}
		$v_size = ftell( $this->zip_fd ) - $v_offset;
		if ( ( $v_result = $this->privWriteCentralHeader( $v_count + $v_central_dir['entries'], $v_size, $v_offset, $v_comment ) ) != 1 )
		{
			unset( $v_header_list );
			$this->privSwapBackMagicQuotes( );
			return $v_result;
		}
		$v_swap = $this->zip_fd;
		$this->zip_fd = $v_zip_temp_fd;
		$v_zip_temp_fd = $v_swap;
		$this->privCloseFd( );
		@fclose( $v_zip_temp_fd );
		$this->privSwapBackMagicQuotes( );
		@unlink( $this->zipname );
		pclziputilrename( $v_zip_temp_name, $this->zipname );
		return $v_result;
	}

	function privOpenFd( $p_mode )
	{
		$v_result = 1;
		if ( $this->zip_fd != 0 )
		{
			PclZip::priverrorlog( PCLZIP_ERR_READ_OPEN_FAIL, "Zip file '".$this->zipname."' already open" );
			return PclZip::errorcode( );
		}
		if ( ( $this->zip_fd = fopen( $this->zipname, $p_mode ) ) == 0 )
		{
			PclZip::priverrorlog( PCLZIP_ERR_READ_OPEN_FAIL, "Unable to open archive '".$this->zipname."' in ".$p_mode." mode" );
			return PclZip::errorcode( );
		}
		return $v_result;
	}

	function privCloseFd( )
	{
		$v_result = 1;
		if ( $this->zip_fd != 0 )
		{
			@fclose( $this->zip_fd );
		}
		$this->zip_fd = 0;
		return $v_result;
	}

	function privAddList( $p_list, &$p_result_list, $p_add_dir, $p_remove_dir, $p_remove_all_dir, &$p_options )
	{
		$v_result = 1;
		$v_header_list = array( );
		if ( ( $v_result = $this->privAddFileList( $p_list, $v_header_list, $p_add_dir, $p_remove_dir, $p_remove_all_dir, $p_options ) ) != 1 )
		{
			return $v_result;
		}
		$v_offset = @ftell( $this->zip_fd );
		$i = 0;
		$v_count = 0;
		for ( ;	$i < sizeof( $v_header_list );	++$i	)
		{
			if ( $v_header_list[$i]['status'] == "ok" )
			{
				if ( ( $v_result = $this->privWriteCentralFileHeader( $v_header_list[$i] ) ) != 1 )
				{
					return $v_result;
				}
				++$v_count;
			}
			$this->privConvertHeader2FileInfo( $v_header_list[$i], $p_result_list[$i] );
		}
		$v_comment = "";
		if ( isset( $p_options[PCLZIP_OPT_COMMENT] ) )
		{
			$v_comment = $p_options[PCLZIP_OPT_COMMENT];
		}
		$v_size = ftell( $this->zip_fd ) - $v_offset;
		if ( ( $v_result = $this->privWriteCentralHeader( $v_count, $v_size, $v_offset, $v_comment ) ) != 1 )
		{
			unset( $v_header_list );
			return $v_result;
		}
		return $v_result;
	}

	function privAddFileList( $p_list, &$p_result_list, $p_add_dir, $p_remove_dir, $p_remove_all_dir, &$p_options )
	{
		$v_result = 1;
		$v_header = array( );
		$v_nb = sizeof( $p_result_list );
		$j = 0;
		for ( ;	$j < count( $p_list ) && $v_result == 1;	++$j	)
		{
			$p_filename = pclziputiltranslatewinpath( $p_list[$j], false );
			if ( $p_filename == "" )
			{
				continue;
			}
			if ( !file_exists( $p_filename ) )
			{
				PclZip::priverrorlog( PCLZIP_ERR_MISSING_FILE, "File '{$p_filename}' does not exists" );
				return PclZip::errorcode( );
			}
			if ( is_file( $p_filename ) || is_dir( $p_filename ) && !$p_remove_all_dir )
			{
				if ( ( $v_result = $this->privAddFile( $p_filename, $v_header, $p_add_dir, $p_remove_dir, $p_remove_all_dir, $p_options ) ) != 1 )
				{
					return $v_result;
				}
				$p_result_list[$v_nb++] = $v_header;
			}
			if ( @is_dir( $p_filename ) )
			{
				if ( $p_filename != "." )
				{
					$v_path = $p_filename."/";
				}
				else
				{
					$v_path = "";
				}
				if ( $p_hdir = @opendir( $p_filename ) )
				{
					while ( ( $p_hitem = @readdir( $p_hdir ) ) !== false )
					{
						if ( $p_hitem == "." || $p_hitem == ".." )
						{
							continue;
						}
						if ( @is_file( $v_path.$p_hitem ) )
						{
							if ( ( $v_result = $this->privAddFile( $v_path.$p_hitem, $v_header, $p_add_dir, $p_remove_dir, $p_remove_all_dir, $p_options ) ) != 1 )
							{
								return $v_result;
							}
							$p_result_list[$v_nb++] = $v_header;
						}
						else if ( @is_dir( $v_path.$p_hitem ) )
						{
							$p_temp_list[0] = $v_path.$p_hitem;
							$v_result = $this->privAddFileList( $p_temp_list, $p_result_list, $p_add_dir, $p_remove_dir, $p_remove_all_dir, $p_options );
							$v_nb = sizeof( $p_result_list );
						}
					}
					@closedir( $p_hdir );
				}
				unset( $p_temp_list );
				unset( $p_hdir );
				unset( $p_hitem );
			}
		}
		return $v_result;
	}

	function privAddFile( $p_filename, &$p_header, $p_add_dir, $p_remove_dir, $p_remove_all_dir, &$p_options )
	{
		$v_result = 1;
		if ( $p_filename == "" )
		{
			PclZip::priverrorlog( PCLZIP_ERR_INVALID_PARAMETER, "Invalid file list parameter (invalid or empty list)" );
			return PclZip::errorcode( );
		}
		$v_stored_filename = $p_filename;
		if ( $p_remove_all_dir )
		{
			$v_stored_filename = basename( $p_filename );
		}
		else if ( $p_remove_dir != "" )
		{
			if ( substr( $p_remove_dir, -1 ) != "/" )
			{
				$p_remove_dir .= "/";
			}
			if ( substr( $p_filename, 0, 2 ) == "./" || substr( $p_remove_dir, 0, 2 ) == "./" )
			{
				if ( substr( $p_filename, 0, 2 ) == "./" && substr( $p_remove_dir, 0, 2 ) != "./" )
				{
					$p_remove_dir = "./".$p_remove_dir;
				}
				if ( substr( $p_filename, 0, 2 ) != "./" && substr( $p_remove_dir, 0, 2 ) == "./" )
				{
					$p_remove_dir = substr( $p_remove_dir, 2 );
				}
			}
			$v_compare = pclziputilpathinclusion( $p_remove_dir, $p_filename );
			if ( 0 < $v_compare )
			{
				if ( $v_compare == 2 )
				{
					$v_stored_filename = "";
				}
				else
				{
					$v_stored_filename = substr( $p_filename, strlen( $p_remove_dir ) );
				}
			}
		}
		if ( $p_add_dir != "" )
		{
			if ( substr( $p_add_dir, -1 ) == "/" )
			{
				$v_stored_filename = $p_add_dir.$v_stored_filename;
			}
			else
			{
				$v_stored_filename = $p_add_dir."/".$v_stored_filename;
			}
		}
		$v_stored_filename = pclziputilpathreduction( $v_stored_filename );
		clearstatcache( );
		$p_header['version'] = 20;
		$p_header['version_extracted'] = 10;
		$p_header['flag'] = 0;
		$p_header['compression'] = 0;
		$p_header['mtime'] = filemtime( $p_filename );
		$p_header['crc'] = 0;
		$p_header['compressed_size'] = 0;
		$p_header['size'] = filesize( $p_filename );
		$p_header['filename_len'] = strlen( $p_filename );
		$p_header['extra_len'] = 0;
		$p_header['comment_len'] = 0;
		$p_header['disk'] = 0;
		$p_header['internal'] = 0;
		$p_header['external'] = is_file( $p_filename ) ? 0 : 16;
		$p_header['offset'] = 0;
		$p_header['filename'] = $p_filename;
		$p_header['stored_filename'] = $v_stored_filename;
		$p_header['extra'] = "";
		$p_header['comment'] = "";
		$p_header['status'] = "ok";
		$p_header['index'] = -1;
		if ( isset( $p_options[PCLZIP_CB_PRE_ADD] ) )
		{
			$v_local_header = array( );
			$this->privConvertHeader2FileInfo( $p_header, $v_local_header );
			eval( "\$v_result = ".$p_options[PCLZIP_CB_PRE_ADD]."(PCLZIP_CB_PRE_ADD, \$v_local_header);" );
			if ( $v_result == 0 )
			{
				$p_header['status'] = "skipped";
				$v_result = 1;
			}
			if ( $p_header['stored_filename'] != $v_local_header['stored_filename'] )
			{
				$p_header['stored_filename'] = pclziputilpathreduction( $v_local_header['stored_filename'] );
			}
		}
		if ( $p_header['stored_filename'] == "" )
		{
			$p_header['status'] = "filtered";
		}
		if ( 255 < strlen( $p_header['stored_filename'] ) )
		{
			$p_header['status'] = "filename_too_long";
		}
		if ( $p_header['status'] == "ok" )
		{
			if ( is_file( $p_filename ) )
			{
				if ( ( $v_file = @fopen( $p_filename, "rb" ) ) == 0 )
				{
					PclZip::priverrorlog( PCLZIP_ERR_READ_OPEN_FAIL, "Unable to open file '{$p_filename}' in binary read mode" );
					return PclZip::errorcode( );
				}
				if ( $p_options[PCLZIP_OPT_NO_COMPRESSION] )
				{
					$v_content_compressed = @fread( $v_file, $p_header['size'] );
					$p_header['crc'] = crc32( $v_content_compressed );
					$p_header['compressed_size'] = $p_header['size'];
					$p_header['compression'] = 0;
				}
				else
				{
					$v_content = @fread( $v_file, $p_header['size'] );
					$p_header['crc'] = crc32( $v_content );
					$v_content_compressed = @gzdeflate( $v_content );
					$p_header['compressed_size'] = strlen( $v_content_compressed );
					$p_header['compression'] = 8;
				}
				if ( ( $v_result = $this->privWriteFileHeader( $p_header ) ) != 1 )
				{
					@fclose( $v_file );
					return $v_result;
				}
				@fwrite( $this->zip_fd, $v_content_compressed, $p_header['compressed_size'] );
				@fclose( $v_file );
			}
			else
			{
				if ( substr( $p_header['stored_filename'], -1 ) != "/" )
				{
					$p_header['stored_filename'] .= "/";
				}
				$p_header['size'] = 0;
				$p_header['external'] = 16;
				if ( ( $v_result = $this->privWriteFileHeader( $p_header ) ) != 1 )
				{
					return $v_result;
				}
			}
		}
		if ( isset( $p_options[PCLZIP_CB_POST_ADD] ) )
		{
			$v_local_header = array( );
			$this->privConvertHeader2FileInfo( $p_header, $v_local_header );
			eval( "\$v_result = ".$p_options[PCLZIP_CB_POST_ADD]."(PCLZIP_CB_POST_ADD, \$v_local_header);" );
			if ( $v_result == 0 )
			{
				$v_result = 1;
			}
		}
		return $v_result;
	}
//goto ??
	function privWriteFileHeader( &$p_header )
	{
		$v_result = 1;
		$p_header['offset'] = ftell( $this->zip_fd );
		$v_date = getdate( $p_header['mtime'] );

	}
}
