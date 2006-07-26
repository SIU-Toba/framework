<?php

	if($editable = $this->zona->get_editable()){
		$this->zona->obtener_html_barra_superior();	

		echo "NO";
/*	
		if( !( ($this->zona->editable_info["fuente_datos"]=="instancia")
			&& ($this->zona->editable_info["proyecto"]=='admin') ) )
		{
			abrir_base( 	$this->zona->editable_info["fuente_datos"], array(
							apex_db_motor => $this->zona->editable_info["fuente_datos_motor"],
							apex_db_profile => $this->zona->editable_info["host"],
							apex_db_usuario => $this->zona->editable_info["usuario"],
							apex_db_clave => $this->zona->editable_info["clave"],
							apex_db_base => $this->zona->editable_info["base"],
							apex_db_link => $this->zona->editable_info["link_instancia"],
							apex_db_link_id => $this->zona->editable_info["instancia_id"])						);
		}
		dump_conexiones();

*/
	}else{
		echo ei_mensaje("No se especifico una FUENTE de DATOS");
	}
?>