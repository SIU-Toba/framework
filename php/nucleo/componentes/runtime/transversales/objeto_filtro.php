<?php
require_once('nucleo/componentes/runtime/objeto.php');
include("nucleo/browser/interface/dimensiones.php");	//Objetos dimension (Especializacion de elementos de FORMULARIO)
include("nucleo/browser/interface/dimensiones_restric.php");
define("apex_filtro_separador","%-%");

//error_reporting(E_ALL ^ E_NOTICE);

class objeto_filtro extends objeto
{
	var $dimensiones;		//Array de objetos dimension creados
	var $grupos;			//Grupos de dimensiones
	var $hay_grupos;		//Flag que indica si existen grupos
	var $indice_dim_grupo;	//Indice de dimensiones por grupo  y orden en el mismo [grupo][orden]
	var $indice_nombre;		//Indice de dimensiones por nombre
	var $indice_id;
	var $indice_dim_acopladas;
	var $nombre_formulario;
	var $form_submit;
	var $form_submit_nombre;
	var $form_submit_limpiar;
	
    function objeto_filtro($id)
	{
		parent::objeto($id);
		$this->generar_grupos();
        $this->nombre_formulario = "filtro_" . $this->id[1];
        $this->form_submit = "submit_" . $this->id;
        $this->form_submit_nombre = "Filtrar";
        $this->form_submit_limpiar = "Limpiar FILTRO";
        //$cronometro->marcar('OBJETO FILTRO ['. $this->id .'] Cargar DEFINICION' ,apex_nivel_objeto);
		$this->crear_dimensiones();
		//Controlar activacion EXTERNA
		$this->procesar_activacion_externa();
		//Si no se activo el filtro recupero el estado del mismo desde la memoria
		if( !$this->controlar_activacion() ){
			$this->recuperar_estado(); 
		}
		$this->persistir_estado(); 
	}
//--------------------------------------------------------------------------------------------

	function procesar_activacion_externa()
	{
		return;
	}
//--------------------------------------------------------------------------------------------


	function obtener_definicion_db()
/*
 	@@acceso:
	@@desc: 
*/
	{
		$sql = parent::obtener_definicion_db();		
		$sql["info_dimensiones"]["sql"] = "SELECT	g.dimension_grupo as	grupo,
						g.nombre as		 				grupo_nombre,
						g.descripcion as 				grupo_des,
						d.dimension as 				dimension,
						d.fuente_datos as 			fuente,
						d.nombre as 					nombre,
						d.descripcion as				descripcion,
						d.dimension_tipo as		 	tipo,
						d.inicializacion as			inicializacion,
						f.etiqueta as					etiqueta,
						f.tabla as 						tabla,
						f.columna as 					columna,
						f.requerido as 				obligatorio,
						f.no_interactivo as			no_interactivo,
						f.predeterminado as 			predeterminado,
						u.usuario_perfil_datos as	perfil
				FROM 	apex_objeto_filtro f,
						apex_dimension d
						LEFT OUTER JOIN apex_dimension_grupo g ON d.dimension_grupo = g.dimension_grupo
						LEFT OUTER JOIN apex_dimension_perfil_datos u ON (d.dimension = u.dimension) 
						AND (u.usuario_perfil_datos = '". $this->solicitud->hilo->obtener_usuario_perfil_datos()."')
				WHERE	f.dimension = d.dimension
				AND		f.dimension_proyecto = d.proyecto
				AND		objeto_filtro_proyecto = '".$this->id[0]."'
                AND     objeto_filtro = '".$this->id[1]."'
				ORDER BY g.orden, f.orden;";
		$sql["info_dimensiones"]["tipo"]="x";
		$sql["info_dimensiones"]["estricto"]="1";
		return $sql;
	}

//##########################################################################################
//##########################################################################################
//##############   TRABAJO sobre DIMENSIONES   #############################################
//##########################################################################################
//##########################################################################################

	function generar_grupos()
	//Los grupos se utilizan solo para el DISPLAY.
	{
		for($a=0;$a<count($this->info_dimensiones);$a++)
		{
			//Excluyo las dimensiones NO INTERACTIVAS
			if(!($this->info_dimensiones[$a]["no_interactivo"])){
				//---- Genero los indices a este objeto
				if (array_key_exists("grupo_nombre", $this->info_dimensiones[$a])){
					$this->grupos[$this->info_dimensiones[$a]['grupo']]["nombre"] = $this->info_dimensiones[$a]['grupo_nombre'];
				}
				if (array_key_exists("grupo_desc", $this->info_dimensiones[$a])){	
					$this->grupos[$this->info_dimensiones[$a]['grupo']]["desc"] = $this->info_dimensiones[$a]['grupo_desc'];
				}
			}
        }
		//Se estan utilizando los grupos, o son todos NULL?
        if( (isset($this->grupos[""])) && ((count($this->grupos))==1) ){
            $this->hay_grupos = false;
        }else{
            $this->hay_grupos = true;        
        }
    }
	//----------------------------------------------------------------------------------------
	
	function crear_dimensiones()
	//Creo la interface de las dimensiones que conforman el filtro.
	{
		global $cronometro;
        //$cronometro->marcar('basura',apex_nivel_objeto);
		//Inicializo las dimensiones definidas en tiempo de diseo
		for($a=0;$a<count($this->info_dimensiones);$a++)
		{
			//---- Genero los indices a este objeto
			if(!($this->info_dimensiones[$a]["no_interactivo"])){
				$this->indice_dim_grupo[$this->info_dimensiones[$a]['grupo']][] = $a;
			}
			$this->indice_nombre[$this->info_dimensiones[$a]["dimension"]] = $a;
			$this->indice_id[$a] = $this->info_dimensiones[$a]["dimension"];
			//Tengo que controlar que la fuente de datos del filtro sea la misma que la de la dimension.
			if($this->info["fuente"]!=$this->info_dimensiones[$a]["fuente"]){
				$this->observar("bug","La fuente de datos del filtro y la de la dimension no coinciden: 
										OBJETO ('".$this->info["fuente"]."') - DIMENSION ('".$this->info_dimensiones[$a]["fuente"]."')",false,true,true);
			}
			//Creo el ARRAY que inicializa la DIMENSION
         $parametros = parsear_propiedades($this->info_dimensiones[$a]["inicializacion"]);
			//Si la dimension impone una restriccion al usuario, se le pasa en el array
			//De parametros especificos porque solo afecta a algunas dimensiones
			$parametros["perfil"] = $this->info_dimensiones[$a]["perfil"];
			$parametros["fuente"] = $this->info_dimensiones[$a]["fuente"]; 
			//Manejo el tema de las columnas (pueden ser varias)
            if(ereg(apex_filtro_separador,$this->info_dimensiones[$a]["columna"])){
                $dato = explode(apex_filtro_separador,$this->info_dimensiones[$a]["columna"]);
				for($d=0;$d<count($dato);$d++){//Elimino espacios en las claves
					$dato[$d]=trim(stripslashes($dato[$d]));
				}
            }else{
                $dato = trim(stripslashes($this->info_dimensiones[$a]["columna"]));
            }
			//Manejo de PREDETERMINADOS
			if(isset($this->info_dimensiones[$a]["predeterminado"])){
				$parametros["predeterminado"] = $this->info_dimensiones[$a]["predeterminado"];
			}
			//Seteo el nombre de la dimension en el filtro
			if(isset($this->info_dimensiones[$a]["etiqueta"])){
				$nombre = $this->info_dimensiones[$a]["etiqueta"];
			}else{
				$nombre = $this->info_dimensiones[$a]["nombre"];
			}
			//---- Creo las dimensiones
			$sentencia_creacion_dim = "\$this->dimensiones[$a]=& new ".
										"dimension_".$this->info_dimensiones[$a]['tipo'].
												"(	\$this->id, '" .
													$this->nombre_formulario ."', '". 
                                                    $this->info_dimensiones[$a]["dimension"] ."',". 
	                                                "\$nombre, '". 
                                                    $this->info_dimensiones[$a]["descripcion"] ."',". 
                                                    "\$dato,'".
                                                    $this->info_dimensiones[$a]["obligatorio"] ."',".
                                                    "\$parametros);";
			//echo $sentencia_creacion_dim . "<br>";
			eval($sentencia_creacion_dim);
            //Si se apreto el boton del filtro cargo el estado.
			//El isset es por si viene el POST de otro formulario
            if((acceso_post()) && (isset($_POST[$this->form_submit]))){
				if ($_POST[$this->form_submit] == $this->form_submit_nombre){
					$this->dimensiones[$a]->cargar_estado();
				}
            }
		}
        //$cronometro->marcar('OBJETO FILTRO ['. $this->id .'] : CREAR objetos DIMENSION',apex_nivel_objeto);
	}
    //----------------------------------------------------------------------------------------
	
	function controlar_activacion()
	//responde si se activo el filtro
	{
		if(acceso_post()){
			if(isset($_POST[$this->form_submit])){
				if(($_POST[$this->form_submit] == $this->form_submit_nombre) ||
	       	   ($_POST[$this->form_submit] == $this->form_submit_limpiar)){
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

    //----------------------------------------------------------------------------------------	
	function esta_filtrando()
	//responde si alguna dimensión está activa
	{
		foreach (array_keys($this->grupos) as $grupo)
		{
            for($a=0;$a<count($this->indice_dim_grupo[$grupo]);$a++)
            {		
				if ($this->dimensiones[$this->indice_dim_grupo[$grupo][$a]]->activado())
					return true;
			}
		}
		return false;
	}
    //----------------------------------------------------------------------------------------

	function resetear_estado()
	//Carga el estado del filtro con la informacion de la memoria
	{
		for($a=0;$a<count($this->dimensiones);$a++)
		{
			//echo $this->dimensiones[$a]->obtener_id() . "<br>";
			$this->dimensiones[$a]->resetear_estado();
		}
	}
    //----------------------------------------------------------------------------------------

	function recuperar_estado()
	//Carga el estado del filtro con la informacion de la memoria
	{
        if(is_array($this->memoria))
        {
    	    foreach($this->memoria as $clave => $valor)
            {
                if(isset($this->indice_nombre[$clave]))
                {
    	            $this->dimensiones[$this->indice_nombre[$clave]]->cargar_estado($valor);
                }
            }
        }		
	}
    //----------------------------------------------------------------------------------------

	function persistir_estado()
	//Devuelve un ARRAY con el estado de todos los filtros
	{
		foreach (array_keys($this->grupos) as $grupo)
		{
            for($a=0;$a<count($this->indice_dim_grupo[$grupo]);$a++)
            {
                $indice = $this->indice_dim_grupo[$grupo][$a];
                if($this->dimensiones[$indice]->activado()){
                    $estado = $this->dimensiones[$indice]->obtener_estado();
                    $dimension = $this->dimensiones[$indice]->obtener_id();
		        	if(!is_null($estado)){
			        	$this->memoria[$dimension]=$estado; 
        			}
                }
            }
		}
        //ei_arbol($this->memoria,"PERSISTENCIA FILTRO");
    }
    //----------------------------------------------------------------------------------------

	function validar_estado()
	{
		//Estan los dimensiones requeridos seteados?
		//Se infringe una regla del perfil de datos???
		$estado = array(true,"Validacion correcta");
		for($a=0;$a<count($this->dimensiones);$a++){
			$temp = $this->dimensiones[$a]->validar_estado();
    		if( (!$temp[0]) ){
				$this->registrar_info_proceso($temp[1]);
				$estado = array(false,"Error en la validacion");
			}
        }
		return $estado;
	}
//----------------------------------------------------------------------------------------

	function acoplar_dimensiones($dimensiones)
	//El consumidor del FILTRO desea incorporar mas dimensiones en tiempo de ejecucion.
	//Estas dimensiones son consideradas NO INTERACTIVAS (no aparecen el la interface del filtro)
	//EL parametro es un array asociativo con la clave de la dimension como indice y con dos
	//subindices: 'tabla' y 'columna' que indican a que tabla esta vinculada la dimension en cuestion.
	{
		//global $cronometro;
        //$cronometro->marcar('basura',apex_nivel_objeto);
		//ei_arbol($dimensiones,"DIMENSIONES");
		
		//1) La dimension que se solicita acoplar ya esta creada
		foreach (array_keys($dimensiones) as $clave){
			if(isset($this->indice_nombre[$clave]))	{
				//Saco la duplicada de la definicion.
				unset($dimensiones[$clave]);
			}
		}
		
		if(count($dimensiones)>0)//Si quedo alguna dimension despues de la limpieza
		{
			//2) Busco la definicion de las DIMENSIONEs a asociar.
			$sql_claves = "'" . implode("','",array_keys($dimensiones)) . "'";//echo $sql_claves;
			global $db, $ADODB_FETCH_MODE;
			$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
			$sql = "
				SELECT 	d.dimension as 				dimension,
						d.fuente_datos as 			fuente,
						d.nombre as 				nombre,
						d.dimension_tipo as		 	tipo,
						d.inicializacion as			inicializacion,
						d.tabla_ref as				tab_ref,
						'0' as		 				obligatorio,
						'1' as						no_interactivo,
						u.usuario_perfil_datos as	perfil
				FROM 	apex_dimension_perfil_datos u,
						apex_dimension d
				WHERE	( d.dimension = u.dimension) 
				AND 	( u.usuario_perfil_datos = '{$this->solicitud->info['usuario_perfil_datos']}')
				AND		( d.dimension IN ($sql_claves) );";
			//dump_SQL($sql);
			$rs = $db["instancia"][apex_db_con]->Execute($sql);
			if(!$rs){
				monitor::evento("bug","OBJETO FILTRO (Acoplar dimensiones): No se genero el recordset. id[". ($this->id) ."] clase[". $this->info["clase"] ."]. -- <b>" . $db["instancia"][apex_db_con]->ErrorMsg(). " </b> -- SQL: $sql --");
			}
			if($rs->EOF){
				monitor::evento("bug","OBJETO FILTRO (Acoplar dimensiones): No es posible acceder a las dimensiones (EOF)");
			}
			$definiciones = $rs->getArray();
	
			//3) Le agrego las tablas y columnas a las que estan vinculadas
			for($a=0;$a<count($definiciones);$a++){
				$definiciones[$a]["tabla"] = $dimensiones[$definiciones[$a]["dimension"]]["tabla"];
				$definiciones[$a]["columna"] = $dimensiones[$definiciones[$a]["dimension"]]["columna"];
			}
			//ei_arbol($definiciones);
	
 			//4) Creo las dimensiones.
			$offset = count($this->dimensiones);
			for($a=0;$a<count($definiciones);$a++)
			{
				if($this->info["fuente"]!=$definiciones[$a]["fuente"]){
					$this->observar("bug","ACOPLAR DIMENSIONES: La fuente de datos del filtro y la de la dimension no coinciden.",false,true,true);
				}


	            $parametros = parsear_propiedades($this->info_dimensiones[$a]["inicializacion"]);
				//Si la dimension impone una restriccion al usuario, se le pasa en el array
				//De parametros especificos porque solo afecta a algunas dimensiones
				$parametros["perfil"] = $this->info_dimensiones[$a]["perfil"];
				//---- Creo las dimensiones
				$sentencia_creacion_dim = "\$this->dimensiones[".($a + $offset)."]=& new ".
											"dimension_".$this->info_dimensiones[$a]['tipo'].
												"(	\$this->id, '" .
													$this->nombre_formulario ."', '". 
                                                    $this->info_dimensiones[$a]["dimension"] ."', '". 
	                                                $this->info_dimensiones[$a]["nombre"] ."', '". 
                                                    $this->info_dimensiones[$a]["descripcion"] ."', '". 
                                                    $this->info_dimensiones[$a]["obligatorio"] ."', '".
                                                    $this->info_dimensiones[$a]["no_interactivo"] ."', '".
                                                    $this->info_dimensiones[$a]["tabla"] ."', '".
                                                    $this->info_dimensiones[$a]["columna"] ."', ".
                                                    "\$parametros);";
				//echo $sentencia_creacion_dim . "<br>";
				eval($sentencia_creacion_dim);
				$this->indice_dim_acopladas[] = $a + $offset;
			}		
	        //$cronometro->marcar('OBJETO FILTRO ['. $this->id .'] : Acoplar dimensiones',apex_nivel_objeto);
  		}
	}
//----------------------------------------------------------------------------------------


	function obtener_where_dim_acopladas()
	//Devuelve el WHERE generado a partir de las dimensiones ACOPLADAS
	{
		for($a=0;$a<count($this->indice_dim_acopladas);$a++){
    		if($where = $this->dimensiones[$this->indice_dim_acopladas[$a]]->obtener_where()) $temp[] = $where;
        }
		return $temp;
	}
//----------------------------------------------------------------------------------------

	function obtener_from_dim_acopladas()
	//Devuelve el FROM generado a partir de las dimensiones ACOPLADAS
	{
		for($a=0;$a<count($this->indice_dim_acopladas);$a++){
    		if($from = $this->dimensiones[$this->indice_dim_acopladas[$a]]->obtener_from()) $temp[] = $from;
        }
		return $temp;
	}
//----------------------------------------------------------------------------------------

	function obtener_where()
	//Devuelve el WHERE generado a partir de los dimensiones establecidos
	{
		$temp = array();
		for($a=0;$a<count($this->dimensiones);$a++){
    		if($where = $this->dimensiones[$a]->obtener_where()) $temp[$this->indice_id[$a]] = $where;
        }
		return $temp;
	}
//----------------------------------------------------------------------------------------

	function obtener_from()
	//Algunas dimensiones devuelven tambien una porcion de FROM.
	// Es el caso de las restricciones por tablas de asociacion que filtran a travez de JOINs
	{
		$temp = array();
		for($a=0;$a<count($this->dimensiones);$a++){
    		if($from = $this->dimensiones[$a]->obtener_from()) $temp[$this->indice_id[$a]] = $from;
        }
		return $temp;
	}
//----------------------------------------------------------------------------------------

	function obtener_info()
	//Devuelve una descripcion legible de los dimensiones establecidos
	{
		for($a=0;$a<count($this->dimensiones);$a++)
		{
    		if($temp = $this->dimensiones[$a]->obtener_info()){
    			$info[]=$temp;	
    		}
		}
		if (isset($info)){
			return $info;
		}else{
			return null;
		}
	}
//----------------------------------------------------------------------------------------

	function obtener_estado()
	{
		for($a=0;$a<count($this->dimensiones);$a++)
		{
    		$temp = $this->dimensiones[$a]->obtener_estado();
    		if(isset($temp)){
	    		if($temp != "NULL"){
	    			$estado[$this->indice_id[$a]]=$temp;	
	    		}
    		}
		}
		return $estado;
	}

//##########################################################################################
//##########################################################################################
//##############   INTERFACE  GRAFICA  #####################################################
//##########################################################################################
//##########################################################################################

	function obtener_interface_vertical($mostrar_cabecera=true)
	//Interpreta el array de dimensiones para obtener una interface
	{
		$vinculo = $this->solicitud->vinculador->generar_solicitud(null,null,null,true);
        echo form::abrir($this->nombre_formulario, $vinculo );
		echo "<div align='center'>\n";		
		echo "<table  class='objeto-base'>";

		if($mostrar_cabecera){
			echo "<tr><td>";
			$this->barra_superior();
			echo "</td></tr>\n";
		}
		foreach (array_keys($this->grupos) as $grupo)
		{
			if (array_key_exists("descripcion",$this->grupos[$grupo])) {
				if(trim($this->grupos[$grupo]["descripcion"])!=""){
					echo "<tr><td class='filtro-item'>&nbsp;&nbsp;".$this->grupos[$grupo]["descripcion"]."</td></tr>";
				}
			}	
            for($a=0;$a<count($this->indice_dim_grupo[$grupo]);$a++)
            {
                echo "<tr><td class='filtro-item'>";
                $this->dimensiones[$this->indice_dim_grupo[$grupo][$a]]->obtener_interface();
				echo "</td></tr>";
            }
		}
    	echo "<tr><td  class='abm-zona-botones' height='30' align='left'>";
		echo form::submit($this->form_submit, $this->form_submit_nombre, "abm-input-eliminar");
		echo form::submit($this->form_submit, $this->form_submit_limpiar, "abm-input");
//    	echo form::button($this->form_submit,"Limpiar FILTRO","onclick=\"javascript:document.location.href='$vinculo'\"","abm-input");
        echo "</td></tr>";
		echo "</table>";
		echo "</div>";
        echo form::cerrar();
	}
//##########################################################################################
//##########################################################################################
//##########################################################################################

}
?>
