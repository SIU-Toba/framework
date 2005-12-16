<?
require_once("objeto_ei_formulario.php");	//Ancestro de todos los	OE

/**
 * Un filtro presenta una grilla de campos similar al formulario, pero con el objetivo de reducir el conjunto de datos mostrados por otro objeto. 
 * @package Objetos
 * @subpackage Ei
 */
class objeto_ei_filtro extends objeto_ei_formulario
{
	function inicializar_especifico()
	{
		$this->set_grupo_eventos_activo('no_cargado');
	}

	function get_lista_eventos()
	{
		$eventos = parent::get_lista_eventos();
		return $eventos;
		/*

		CAMBIO_EVT

		//En caso que no se definan eventos, filtrar n es el por defecto y no se incluye como botón
		if (count($eventos) == 0) {
			$eventos += eventos::filtrar(null, false);		
			$this->set_evento_defecto('filtrar');
		}
		*/
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
				$id_ef = $this->elemento_formulario[$ef]->obtener_id_form();			
				echo "<tr><td class='abm-fila' id='nodo_$id_ef'>\n";
				$this->elemento_formulario[$ef]->obtener_interface_ei_filtro();
				echo "</td></tr>\n";
			}
			echo "<tr><td class='ei-base'>\n";
			$this->obtener_botones();
			echo "</td></tr>\n";
			echo "</table>\n";
		}
	}
	
}
?>
