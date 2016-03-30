<?php


class KF_Util_Observable
{

	var $changed = false;
	var $observers = array( );

	function KF_Util_Observable( )
	{
		}

	function addObserver( &$o )
	{
		if ( $o == null )
		{
			return false;
		}
		$i = 0;
		for ( ;	$i < count( $this->observers );	++$i	)
		{
			if ( $this->observers[$i] == $o )
			{
				return false;
			}
		}
		$this->observers[] =& $o;
		return true;
	}

	function removeObserver( &$o )
	{
		$i = 0;
		for ( ;	$i < count( $this->observers );	++$i	)
		{
			if ( $this->observers[i] == $o )
			{
				array_splice( $this->observers, $i, 1 );
				return true;
			}
		}
		return false;
	}

	function notifyObservers( &$infoObj )
	{
		if ( !$this->changed )
		{
			return;
		}
		$this->clearChanged( );
		$i = count( $this->observers ) - 1;
		for ( ;	0 <= $i;	--$i	)
		{
			$tmp =& $this->observers[$i];
			$tmp->update( $this, $infoObj );
		}
	}

	function clearObservers( )
	{
		$this->observers = array( );
	}

	function setChanged( )
	{
		$this->changed = true;
	}

	function clearChanged( )
	{
		$this->changed = false;
	}

	function hasChanged( )
	{
		return $this->changed;
	}

	function countObservers( )
	{
		return count( $this->observers );
	}

}

?>
