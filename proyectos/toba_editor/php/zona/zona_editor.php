<?php

class zona_editor extends toba_zona
{
    protected $editable_cargado;
    
	protected function get_editable_id()
	{
		return $this->editable_id[1];
	}	
}

?>