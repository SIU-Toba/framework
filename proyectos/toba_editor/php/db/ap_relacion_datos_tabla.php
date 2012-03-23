<?php
class ap_relacion_datos_tabla extends ap_relacion_objeto
{
	function evt__post_sincronizacion()
	{
		parent::evt__post_sincronizacion();
		$props_basicas = $this->objeto_relacion->tabla('prop_basicas')->get();
		$cond =	isset($props_basicas['ap'])	// Puede que no esté si no se hacen cambios en el dt
				&& $props_basicas['ap'] != toba_ap_tabla_db_mt::id_ap_mt	// si es mt hay que hacerlo
				&& $this->objeto_relacion->existe_tabla('fks');	// puede que no exista si nunca lo tuvo
		
		if ($cond) {
			$cant_filas = $this->objeto_relacion->tabla('fks')->get_cantidad_filas();
			if ($cant_filas > 0) {
				$obj_proy   = quote($props_basicas['objeto_proyecto']);
				$obj		= quote($props_basicas['objeto']);

				$sql = "DELETE FROM apex_objeto_db_columna_fks WHERE objeto_proyecto=$obj_proy AND objeto=$obj";
				ejecutar_fuente($sql);
			}
		}
	}
}
?>
