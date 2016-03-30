<?php
class iFile
{

	var $Fp = NULL;
	var $Pipe = NULL;
	var $File = NULL;
	var $OpenMode = NULL;

	function OpenFile( $File, $Pipe = "0", $Mode = "r" )
	{
		$this->OpenMode = $Mode;
		$this->File = $File;
		if ( $Pipe == "0" )
		{
			$this->Pipe = "f";
		}
		else
		{
			$this->Pipe = "p";
		}
		if ( $this->OpenMode == "r" || $this->OpenMode == "r+" )
		{
			if ( $this->CheckFile( ) )
			{
				if ( $this->Pipe == "f" )
				{
					$this->Fp = fopen( $this->File, $this->OpenMode );
				}
				else if ( $Pipe == "p" )
				{
					$this->Fp = popen( $this->File, $this->OpenMode );
				}
				else
				{
					echo "Check The OpenFile Mode,It refer to fwrite() function.";
				}
			}
			else
			{
				echo "Access Error: Check {$File} is exist ";
			}
		}
		else if ( $this->Pipe == "f" )
		{
			$this->Fp = fopen( $this->File, $this->OpenMode );
		}
		else if ( $Pipe == "p" )
		{
			$this->Fp = popen( $this->File, $this->OpenMode );
		}
		else
		{
			echo "Check The OpenFile Pipe,It can be 'f' or 'p'.";
		}
		return $this->Fp;
	}

	function CloseFile( )
	{
		if ( $this->Pipe == "f" )
		{
			@fclose( $this->Fp );
		}
		else
		{
			@pclose( $this->Fp );
		}
	}

	function getFileData( )
	{
		@flock( $this->Fp, 1 );
		$size = filesize( $this->File );
		if ( !empty( $size ) )
		{
			$Content = fread( $this->Fp, $size );
		}
		return $Content;
	}

	function CheckFile( )
	{
		if ( file_exists( $this->File ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function WriteFile( $Data, $Mode = 3 )
	{
		@flock( $this->Fp, $Mode );
		fwrite( $this->Fp, $Data );
		$this->CloseFile();
		return true;
	}

}

?>
