<?
require_once("objeto_ei_formulario.php");	//Ancestro de todos los	OE

/*
	Esta clase hereda del formulario, pero el formulario ya se supune como formulario de carga,
	El filtro deberia heredar de un formulario sin ninguna suposicion de uso, y esa clase deberia
	ser tambien el ancestro del formulario de carga.
	El ancestro deberia estar encargado solo de los EF.
	Esta refactorizacion queda PENDIENTE

	ATENCION: 	El filtro declara una funcion con un nomnbre coloquial para los EF,
				esto hace que no pueda haber dos filtros en la misma etapa del CI
				porque se redeclararia la funcion!
*/

class objeto_ei_filtro extends objeto_ei_formulario
{

	function get_lista_eventos()
	{
		$evento = array();
		//--- Limpiar
		if($this->etapa=="modificar"){
			if($this->info_formulario['ev_mod_limpiar']){
				//Evento LIMPIAR
				if($this->info_formulario['ev_mod_limpiar_etiq']){
					$evento['limpiar']['etiqueta'] = $this->info_formulario['ev_mod_limpiar_etiq'];
				}else{
					$evento['limpiar']['etiqueta'] = "&Limpiar";
				}
				$evento['limpiar']['validar'] = "false";
				$evento['limpiar']['estilo'] = "abm-input";
			}
		}
		//--- Filtrar
		if($this->info_formulario['ev_agregar']){
			//Evento ALTA
			if($this->info_formulario['ev_agregar_etiq']){
				$evento['filtrar']['etiqueta'] = $this->info_formulario['ev_agregar_etiq'];
			}else{
				$evento['filtrar']['etiqueta'] = "&Filtrar";
			}
			$evento['filtrar']['validar'] = "true";
			$evento['filtrar']['estilo'] = "abm-input-eliminar";
		}
		return $evento;
	}

	function generar_formulario()
	{
		//Genero	la	interface
		if($this->estado_proceso!="INFRACCION")
		{
			//A los ocultos se les deja incluir javascript
			foreach ($this->lista_ef_ocultos as $ef) {
				echo $this->elemento_formulario[$ef]->obtener_javascript_general();
			}
			echo "<table class='tabla-0'  width='{$this->info_formulario['ancho']}'>";
			foreach ($this->lista_ef_post	as	$ef){
				echo "<tr><td class='abm-fila'>\n";
				$this->elemento_formulario[$ef]->obtener_interface_ei_filtro();
				echo "</td></tr>\n";
			}
			echo "<tr><td class='ei-base'>\n";
			$this->obtener_botones();
			echo "</td></tr>\n";
			echo "</table>\n";
		}
	}
	
	function iniciar_objeto_js()
	{
		//-- EVENTO por DEFECTO: FILTRAR--
		//Si no hay eventos, el componente debe disparar el evento filtrar
		if(count($this->eventos) == 0){
			echo "{$this->objeto_js}.set_evento_defecto(new evento_ei('filtrar', true, ''));\n";
			//Para que en la proxima vuelta el evento sea reconocido...
			$this->eventos['filtrar']['validar'] = "true";
		}
		echo "{$this->objeto_js}.iniciar();\n";	
	}

}
?>
