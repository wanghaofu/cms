<?php


class PluginSetting extends iData
{

	function update( )
	{
		global $plugin_table;
		if ( $this->dataUpdate( $plugin_table['bbsi']['setting'], "" ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function getInfo( )
	{
		global $plugin_table;
		global $db;
		$sql = "SELECT * FROM {$plugin_table['bbsi']['setting']} ";
		return $db->getRow( $sql );
	}

}

?>
