<?php
class PluginSetting extends iData
{

	function update( )
	{
		global $plugin_table;
		if ( $this->dataReplace( $plugin_table['base']['setting'] ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function getAll( )
	{
		global $plugin_table;
		global $db;
		global $table;
		$sql = "SELECT s.*,c.* FROM {$table->content_table} c LEFT JOIN {$plugin_table['base']['setting']} s ON c.TableID=s.TableID";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function getInfo( $TableID )
	{
		global $plugin_table;
		global $db;
		global $table;
		$sql = "SELECT s.*,c.* FROM {$table->content_table} c LEFT JOIN {$plugin_table['base']['setting']} s ON c.TableID=s.TableID WHERE c.TableID={$TableID}";
		$result = $db->getRow( $sql );
		return $result;
	}

}

?>
