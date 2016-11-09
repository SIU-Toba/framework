<?php
/*
	Faltan controles javascript
		- La tabla padre e hija no puede ser la misma
*/

class eiform_relacion extends toba_ei_formulario
{
	function generar_formulario()
	{
		$escapador = toba::escaper();
		echo "<table class='tabla-0'  width='". $escapador->escapeHtmlAttr($this->info_formulario['ancho'])."'>";
		echo "<tr><td class='abm-fila'>\n";
		$this->ef('identificador')->obtener_interface_ei();    
		echo "</td></tr>\n";
		echo "<tr><td class='abm-fila'>\n";
		$this->ef('orden')->obtener_interface_ei();    
		echo "</td></tr>\n";
		echo "<tr><td class='abm-fila'>\n";
		$this->ef('cascada')->obtener_interface_ei();    
		echo "</td></tr>\n";
		echo "<tr><td class='abm-fila'>\n";
			echo "<table class='tabla-0'  width='100%'>";
			echo "<tr><td class='abm-fila'>\n";
				echo "<fieldset style='padding: 10px'><legend>Tabla MADRE</legend>";
				echo "<table class='tabla-0'  width='100%'>";
				echo "<tr><td class='abm-fila' style='text-align: left;'>\n";
				$this->ef('padre')->obtener_interface_ei(true);    
				echo "</td></tr>\n";
				echo "<tr><td class='abm-fila' style='text-align: left;'>\n";
				$this->ef('padre_columnas')->obtener_interface_ei(true);    
				echo "</td></tr>\n";
				echo "</table>\n";
				echo '</fieldset>';
				echo "</td><td class='abm-fila'>\n";
				echo "<fieldset style='padding: 10px'><legend>Tabla HIJA</legend>";
				echo "<table class='tabla-0'  width='100%'>";
				echo "<tr><td class='abm-fila' style='text-align: left;'>\n";
				$this->ef('hija')->obtener_interface_ei(true);    
				echo "</td></tr>\n";
				echo "<tr><td class='abm-fila' style='text-align: left;'>\n";
				$this->ef('hija_columnas')->obtener_interface_ei(true);    
				echo "</td></tr>\n";
				echo "</table>\n";
				echo '</fieldset>';
			echo "</td></tr>\n";
			echo "</table>\n";
		echo "</td></tr>\n";
		echo "<tr><td class='ei-base'>\n";
		$this->obtener_botones();
		echo "</td></tr>\n";
		echo "</table>\n";
	}

	/*function extender_objeto_js()
	{

		echo "	{$this->objeto_js}.evt__validar_datos = function (){
				var salida = true;
				for (elementos in this._efs) {
					if(this.ef(elementos).activo())
					{
						if(this.ef(elementos).valor() == 'nopar')
						{
							notificacion.agregar('Debe seleccionar el elemento '+elementos);
							salida = false;
						}
					}
				}
				return salida;
			}
			";

	}*/
}
?>