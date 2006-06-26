<?php
require_once('nucleo/componentes/objeto.php');

//La generacion dinamica de PHP genera varios NOTICE
error_reporting(E_ALL ^ E_NOTICE);

class objeto_hoja extends objeto
{

	var $contenido;				//Objeto que representa el contenido de la hoja
	var $clave_get;
	var $navegar;
	
	function objeto_hoja($id)
	{
		parent::objeto($id);
		//$this->clave_get = apex_objeto_hoja_get . $this->id;
		$this->clave_get = $this->id;
		$this->planificar_navegacion();
 	}

    function planificar_navegacion()
    {
        $this->navegar = false;
    }
    //----------------------------------------------------------------------------------

	function obtener_dimensiones_asociadas()
	//Devuelve un array con la definicion de las dimensiones asociadas.
	//La idea es comunicarle al objeto filtro la utilizacion de dimensiones que implican perfil
	//Estas dimensiones van a ser consideradas no interactivas.
	{
		for($a=0;$a<count($this->info_hoja_dir);$a++){
			//Solo devuelvo los registros de las dimensiones que estan limitadas para el usuario...
			if(isset($this->info_hoja_dir[$a]["dimension_control_perfil"])){
				$dimension[$this->info_hoja_dir[$a]["dimension"]]["tabla"] = $this->info_hoja_dir[$a]["dimension_tabla"];
				$dimension[$this->info_hoja_dir[$a]["dimension"]]["columna"] = $this->info_hoja_dir[$a]["dimension_columna"];
			}
		}
		if (is_array($dimension)) return $dimension;
	}
    //----------------------------------------------------------------------------------
    
    function obtener_info_secuenciador()
    {
		$temp["id"] = $this->id;
		$temp["clave_get"] = $this->clave_get;
		$temp["nombre"] = $this->info["nombre"];
		$temp["columna_entrada"] = $this->info_hoja["columna_entrada"];
		return $temp;
    }

    //----------------------------------------------------------------------------------
	function crear_sql($where=null,$from=null)
	{
		$sql = sql_agregar_clausulas_where($this->info_hoja['sql'],$where);
		$sql = sql_agregar_tablas_from($sql, $from);
		return $sql;
	}	

    //----------------------------------------------------------------------------------
	
	function cargar_datos($where=null,$from=null)
	//Creo el objeto CONTENIDO
	{
		global $cronometro;
        $cronometro->marcar('basura',apex_nivel_objeto);
        //echo "$where_filtro<br>";ei_arbol($from_filtro);
    	global $db,$ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;

		$sql = $this->crear_sql($where, $from);
		if(!isset($db[$this->info["fuente_datos"]][apex_db_con])){
			$this->registrar_info_proceso("La conexion necesaria para utilizar el objeto [".$this->info["fuente_datos"]."] NO EXISTE - id[". ($this->id) ."]","error");
			return false;
		}
		$db[$this->info["fuente_datos"]][apex_db_con]->SetFetchMode(ADODB_FETCH_NUM);
		$rs = $db[$this->info["fuente_datos"]][apex_db_con]->Execute($sql);
		if(!$rs){
			$this->registrar_info_proceso("La consulta definida en la HOJA de DATOS no genero un RECORDSET - id[". ($this->id) ."] -- " . $db[$this->info["fuente_datos"]][apex_db_con]->ErrorMsg()." -- SQL: $sql -- ","error");
			return false;
		}
		if($rs->EOF){
			$this->registrar_info_proceso("La consulta no devolvio registros","info");
			return false;
		}
		$datos = $rs->getArray();
		$rs->close();
		include_once("objeto_hoja_contenido.php");
		$this->contenido =& new objeto_hoja_contenido($datos, $this->info_hoja, $this->info_hoja_dir,$this->navegar,$this);
		$cronometro->marcar('OBJETO HOJA ['. $this->id .'] : Consulta a la base',apex_nivel_objeto);
		return true;
	}
    //----------------------------------------------------------------------------------

	function obtener_html($get=null)
	//Genero la salida HTML
	{
        //global $cronometro;
		//$cronometro->marcar('< HOJA entrada',apex_nivel_objeto);
		$this->generar_js_navegacion($get);
        $this->contenido->obtener_html();
        enter();
		//$cronometro->marcar('> HOJA: Generar HTML',apex_nivel_objeto);
	}
    //----------------------------------------------------------------------------------

    function generar_js_navegacion()
    {
        $url = $this->solicitud->vinculador->generar_solicitud(null,null,null,true);
?>
<script language='javascript'>

function drillDown(valor){
	var url,parametro;
	url ='<? echo $url ?>';
	parametro = '<? echo $this->clave_get ?>';
	location.href=url + '&' + parametro + '=' + valor;
}

</script>
<?        
    }
}
?>
