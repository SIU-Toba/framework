<?
require_once("objeto_ei_formulario.php");	//Ancestro de todos los	OE

/*
	Esta clase hereda del formulario, pero el formulario ya se supune como formulario de carga,
	El filtro deberia heredar de un formulario sin ninguna suposicion de uso, y esa clase deberia
	ser tambien el ancestro del formulario de carga.
	El ancestro deberia estar encargado solo de los EF.
	Esta refactorizacion queda PENDIENTE
*/

class objeto_ei_filtro extends objeto_ei_formulario
{
	
	function __construct($id)
	{
		parent::__construct($id);
	}

	function inicializar_especifico()
	{
		//Filtrar
		if($this->info_formulario['ev_agregar_etiq']){
			$this->submit_filtrar = $this->info_formulario['ev_agregar_etiq'];
		}else{
			$this->submit_filtrar = "Filtrar";
		}
		//Limpiar
		if($this->info_formulario['ev_mod_limpiar_etiq']){
			$this->submit_limpiar = $this->info_formulario['ev_mod_limpiar_etiq'];
		}else{
			$this->submit_limpiar = "Limpiar";
		}
	}
	
	function obtener_evento()
	{
		if(isset($_POST[$this->submit])){
			if( trim($_POST[$this->submit]) == trim($this->submit_filtrar) ){
				return "filtrar";	
			}
		}
		if(isset($_POST[$this->submit])){
			if( trim($_POST[$this->submit]) == trim($this->submit_limpiar) ){
				return "limpiar";	
			}
		}
		return null;
	}

	function obtener_botones()
	{
		//----------- Generacion
		echo "<table class='tabla-0' align='center' width='100%'>\n";
		echo "<tr><td align='right'>";
		if($this->etapa=="modificar"){
			if($this->info_formulario['ev_mod_limpiar']){
				echo form::submit($this->submit,$this->submit_limpiar,"abm-input");
			}
		}
		if($this->info_formulario['ev_agregar']){
			echo form::submit($this->submit,$this->submit_filtrar,"abm-input-eliminar");
		}
		echo "</td></tr>\n";
		echo "</table>\n";
	}

	function generar_formulario()
/*
	Esto lo tengo que redefinir porque
*/
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
			echo "\n<!-- ------------ Funciones JAVASCRIPT (". $this->id[1] .")	--------------	-->\n\n";
		}
	}

	function obtener_funciones_javascript()
	//Funcion de validacion de los EFs
	{
		echo "<script>\n";
		echo "//-------- Validacion del ei_formulario --------\n";
		echo "\nfunction validacion_ei_form_{$this->id[1]}(formulario) {\n";
		//Si no existe evento agregar, lo tengo que chequear siempre
		echo "//-------- Validacion especifica EF --------\n";
		foreach ($this->lista_ef_post	as	$ef){
			echo $this->elemento_formulario[$ef]->obtener_javascript();
		}
		echo "\nreturn true;\n";
		echo "\n}\n</script>\n";
	}

	function obtener_javascript()
	//Incluir javascript en la validacion del formulario
	{
		echo "if(!validacion_ei_form_{$this->id[1]}(formulario)){\n";
		echo "return false;\n";
		echo " }\n";			
	}
}
?>
