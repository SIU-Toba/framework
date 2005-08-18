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

	function elemento_toba()
	{
		require_once('api/elemento_objeto_ei_filtro.php');
		return new elemento_objeto_ei_filtro();
	}

	function get_lista_eventos()
	{
		$eventos = parent::get_lista_eventos();
		//En caso que no se definan eventos, filtrar n es el por defecto y no se incluye como botón
		if (count($eventos) == 0) {
			$eventos += eventos::filtrar(null, false);		
			$this->set_evento_defecto('filtrar');
		}
		return $eventos;
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
	
}
?>
