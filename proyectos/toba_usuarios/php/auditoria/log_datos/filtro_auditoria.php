<?php 
class filtro_auditoria extends toba_ei_formulario
{
	function generar_layout()
	{
		echo "<style type='text/css'>
			.ef-fecha {
				display: inline;
			}
		</style>";
		foreach ($this->get_efs_activos() as $id_ef) {
			if (! in_array($id_ef, array('fecha_hasta', 'valor'))) {
				$this->generar_html_ef($id_ef);
			}
		}
	}
	
	function generar_input_ef($id_ef)
	{
		parent::generar_input_ef($id_ef);
		if ($id_ef == 'fecha_desde') {
			echo '&nbsp; hasta el ';
			$this->generar_input_ef('fecha_hasta');
		}
		if ($id_ef == 'campo') {
			echo ' igual a ';
			$this->generar_input_ef('valor');
		}		
	}
	

}

?>