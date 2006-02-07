<?
require_once("nucleo/browser/clases/objeto.php");						//Ancestro de todos los OE

class objeto_plan extends objeto
/*
	@@acceso: publico
	@@desc: Permite representar planeamientos en el eje del tiempo
*/
{
	
//################################################################################
//###########################                         ############################
//###########################      INICIALIZACION     ############################
//###########################                         ############################
//################################################################################
    	
	function objeto_plan($id)
/*
	@@acceso: publico
	@@desc: Constructor de la clase
*/
	{
		parent::objeto($id);
	}

	function obtener_definicion_db()
/*
 	@@acceso:
	@@desc: 
*/
	{
		$sql = parent::obtener_definicion_db();
		//---- Plan -----------------------
		$sql["info_plan"]["sql"] = 		"SELECT	descripcion      
									FROM	apex_objeto_plan
									WHERE	objeto_plan_proyecto='".$this->id[0]."'
               				AND     objeto_plan='".$this->id[1]."';";
		$sql["info_plan"]["estricto"]="1";
		$sql["info_plan"]["tipo"]="1";
		//---- Plan ACTIVIDAD -------------
		$sql["info_plan_actividad"]["sql"] = "SELECT 	posicion,
											descripcion_corta as titulo,
											descripcion ,
											fecha_inicio,
											fecha_fin, 
											anotacion,
											altura
									FROM 	apex_objeto_plan_activ
									WHERE	objeto_plan_proyecto = '".$this->id[0]."'
					     			AND	objeto_plan = '".$this->id[1]."'
									ORDER BY posicion;";
		$sql["info_plan_actividad"]["estricto"]="1";
		$sql["info_plan_actividad"]["tipo"]="x";
		//---- Plan HITO -------------------
		$sql["info_plan_hito"]["sql"] = "SELECT 	posicion,	
											descripcion_corta as titulo,
											descripcion,
											fecha,
											anotacion
									FROM 	apex_objeto_plan_hito
									WHERE	objeto_plan_proyecto = '".$this->id[0]."'
					 				AND	objeto_plan = '".$this->id[1]."'
									ORDER BY posicion;";
		$sql["info_plan_hito"]["tipo"]="x";
		$sql["info_plan_hito"]["estricto"]="1";
		//---- Plan LINEA -------------------
		$sql["info_plan_linea"]["sql"] = "SELECT 	linea,
											descripcion_corta as titulo,
											descripcion,
											fecha,
											color,
											ancho,
											estilo	
									FROM 	apex_objeto_plan_linea
									WHERE	objeto_plan_proyecto = '".$this->id[0]."'
				          		AND	objeto_plan = '".$this->id[1]."';";
		$sql["info_plan_linea"]["tipo"]="x";
		$sql["info_plan_linea"]["estricto"]="1";
		return $sql;
	}

//################################################################################
//###########################                         ############################
//###########################         INTERFACE       ############################
//###########################                         ############################
//################################################################################


	function obtener_html()
/*
	@@acceso: publico
	@@desc: Genera la interface de este elemento
*/
	{
		//Preparo la definicion
		$definicion['tipo'] = "gantt";
		$definicion['debug'] = 0;
		$definicion['actividades'] = $this->info_plan_actividad;
		$definicion['hitos'] = $this->info_plan_hito;
		$definicion['lineas'] = $this->info_plan_linea;
	 
		include_once("nucleo/browser/interface/grafico.php");
		$grafico =& new grafico();
		$grafico->cargar_definicion($definicion);
		echo "<br>";
		$grafico->incrustar_imagen();
		//ei_arbol($definicion,"Definicion");
	}
}
//################################################################################
?>
