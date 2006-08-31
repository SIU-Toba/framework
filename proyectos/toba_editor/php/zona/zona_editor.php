<?php
require_once('nucleo/lib/toba_zona.php');

class zona_editor extends toba_zona
{
	protected function get_editable_id()
	{
		return $this->editable_id[1];
	}	
}

?>