<?php
include("lib/ei.php");		//Elementos de interface (para construir parametros)
//-----------------------------------------------
class hoja_de_datos{

	var $identificador;		//ID de la hoja

	var $def_nucleo;		//Nucleo de la definicion
	var $def_directivas;	//Directivas (Como interpretar los campos del recordset devuelto)
	var $def_parametros;	//Parametros de la hoja
	var $def_vinculos;		//Vinculos de la hoja

	var $parametros;		//Array de objetos parametro
	var $vinculos;			//Array de vinculos
	var $where;
	var $contenido;			//Objeto que representa el contenido de la hoja
	var $html;				//BUFFER de html --> me da un segundo tiempo (Las estructuras de graficos se crean cuando se genera la pagina)

	var $mostrar_contenido;	//
	var $mensaje;
	
	function hoja_de_datos($identificador)
	{
		global $cronometro;
		$this->identificador = $identificador;
		$this->mostrar_contenido = true;
		$this->where = "";
		$this->cargar_definicion();
		$cronometro->marcar('HOJA de DATOS: Cargo definicion');
		$this->procesar_parametros();
		$this->procesar_vinculos();
		$cronometro->marcar('HOJA de DATOS: Procesar parametros');
	}

	function cargar_definicion()
	//Cargo la definicion de la hoja desde la base
	{
		global $db, $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		//-----------------------------------------------------------------------------
		//----------------------------> NUCLEO de la HOJA <----------------------------
		//-----------------------------------------------------------------------------
		$sql = 	"	SELECT	h.titulo as 					titulo,
							h.descripcion as				descripcion,
							h.sql as 						sql,
							h.total_y as					total_y,
							h.total_x as					total_x,
							hcf.funcion as					total_x_formato,
							h.ordenable as					ordenable,
							h.filtro as						filtro,
							h.hoja_grafico as 				hoja_grafico,
							h.graf_columnas as				graf_columnas,
							h.graf_filas as					graf_filas,
							h.graf_gen_invertir as			graf_gen_invertir,
							h.graf_gen_invertible as		graf_gen_invertible,
							h.graf_gen_ancho as				graf_gen_ancho,
							h.graf_gen_alto as				graf_gen_alto,
							hd.connect_string as			cs
					FROM	apex_hoja h,
							apex_hoja_columna_formato hcf,
							apex_hoja_db hd
					WHERE 	h.total_x_formato = hcf.hoja_columna_formato
					AND		h.hoja_db = hd.hoja_db
					AND		hoja='".$this->identificador."';";
		$rs = $db->Execute($sql);
		if(!$rs) die("(DEF1) No se genero un recordset (Hoja - DEFINICION) - ".$db->ErrorMsg());
		$temp = $rs->getArray();
		$this->def_nucleo = $temp[0];
		//-----------------------------------------------------------------------------
		//--------------------------------> DIRECTIVAS <-------------------------------
		//-----------------------------------------------------------------------------
		$sql = "	SELECT	d.hoja_directiva_tipo as 		tipo,
							d.nombre as 					nombre,
							hcf.funcion as 					formato,
							d.hoja_columna_estilo as 		estilo
					FROM	apex_hoja_directiva d 
					LEFT OUTER JOIN apex_hoja_columna_formato hcf USING(hoja_columna_formato)
					WHERE 	hoja='".$this->identificador."'
					ORDER BY	columna;";
		$rs = $db->Execute($sql);
		if(!$rs) die("(DEF2) No se genero un recordset (Hoja - DIRECTIVAS) - ".$db->ErrorMsg());
		$this->def_directivas = $rs->getArray();
		//-----------------------------------------------------------------------------
		//-------------------------------> PARAMETROS <--------------------------------
		//-----------------------------------------------------------------------------
		$sql = "	SELECT 	p.parametro as 					parametro,
							p.nombre as 					nombre,
							p.parametro_clase as 			clase,
							p.categoria as 					categoria,
							p.inicializacion as 			inicializacion,
							hp.tabla as 					tabla,
							hp.columna as 					columna,
							hp.requerido as 				requerido
					FROM	apex_parametro p, apex_hoja_parametro hp
					WHERE	p.parametro = hp.parametro
					AND		hp.hoja = '".$this->identificador."'
					ORDER BY hp.orden;";
		$rs = $db->Execute($sql);
		if(!$rs) die("(DEF2) No se genero un recordset (Hoja - PARAMETROS) - ".$db->ErrorMsg());
		$this->def_parametros = $rs->getArray();
		//-----------------------------------------------------------------------------
		//--------------------------------> VINCULOS <---------------------------------
		//-----------------------------------------------------------------------------
		$sql = "	SELECT	v.hoja_vinculo_tipo as 			tipo,
							v.orden as 						orden,
							c.catalogo as					catalogo,
							c.nombre as						nombre
					FROM 	apex_hoja_vinculo v, apex_catalogo c, apex_usuario_catalogo u
					WHERE	v.catalogo = u.catalogo AND v.catalogo = c.catalogo
					AND		v.hoja = '".$this->identificador."'
					ORDER BY 1,2";
		$rs = $db->Execute($sql);
		if(!$rs) die("(DEF2) No se genero un recordset (Hoja - VINCULOS) - ".$db->ErrorMsg());
		$this->def_vinculos = $rs->getArray();
		//-----------------------------------------------------------------------------
		$rs->close();
	}

	function procesar_parametros()
	{
		for($a=0;$a<count($this->def_parametros);$a++){
			//Creo el parametro
			eval("\$this->parametros[$a] =& new param_sql_{$this->def_parametros[$a]["clase"]}(
														'{$this->def_parametros[$a]["parametro"]}',
														'{$this->def_parametros[$a]["nombre"]}',
														'{$this->def_parametros[$a]["tabla"]}',
														'{$this->def_parametros[$a]["columna"]}',
														'{$this->def_parametros[$a]["inicializacion"]}');");
			//Lo inicializo
			$this->parametros[$a]->cargar_estado();
			//Le pregunto su estado
			$estado = $this->parametros[$a]->obtener_estado();
			//Es un requerido no inicializado?
//			if(($def["requerido"]==1)&&(isset($estado)||($estado=""))){
//				$this->parametros[$def["parametro"]]->notificar_error("El parametro "{$def["nombre"]}" es obligatorio");
//				$this->mostrar_contenido = false;
//			}
			//Infringe una regla de perfil de datos?
			//---> FALTA
			//Proceso el WHERE
			$this->where .= $this->parametros[$a]->obtener_where() . " ";
		}
		//-----------> Muestro el filtro? <-------------
		//1: (Condicion: Hay parametros)
		//2: Excepcion: Un parametro requerido no esta seteado

		//3: Accion forzada: El campo 'filtro' de la definicion no es null
		//4: Accion por defecto: Mostrar un filtro si todavia no se mostro ninguno (FLAG $canal)
	}

	function procesar_vinculos()
	{
		global $canal;
		$indice = 0;
		foreach($this->def_vinculos as $vinculo){
			$this->vinculos[$vinculo["tipo"]][$vinculo["orden"]]["destino"] = $canal->navegar_hojas($vinculo["catalogo"]);
			$this->vinculos[$vinculo["tipo"]][$vinculo["orden"]]["nombre"] = $vinculo["nombre"];
			$this->vinculos[$vinculo["tipo"]][$vinculo["orden"]]["indice"] = $indice++;
		}
	}

	function cargar_contenido()
	//Creo el objeto CONTENIDO
	{
		if($datos_db=$this->consultar_db()){
			include("hoja_contenido.php");
			$this->contenido =& new contenido($datos_db, $this->def_nucleo, $this->def_directivas);
			return true;
		}else{
			return false;
		}
	}

	function consultar_db()
	//Ejecuto el query central de la consulta
	{
		global $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		//Averiguo en que motor se resuelve esta consulta.
		if($this->def_nucleo["cs"]=="apl"){
			global $db;
		}else{
			die("ATENCION, esto no paso por el ultimo arreglo");
			$db = abrir_db($this->def_nucleo["cs"]);
		}
		$rs = $db->Execute($this->generar_sql());
		if(!$rs){
			$this->mensaje = "La consulta definida en la HOJA de DATOS no genero un RECORDSET - ".$db->ErrorMsg();
			$this->mostrar_contenido = false;
		}
		if($rs->EOF){
			$this->mensaje = "La consulta definida no devolvio registros";
			$this->mostrar_contenido = false;
		}
		$r = $rs->getArray();
		//Si la la coneccion no es la APL la cierro...
		if($this->def_nucleo["cs"]!="apl"){
			$db->close();
		}
		$rs->close();
		return $r;
 	}

	function generar_sql()
	{
		//Le agrego al SQL el WHERE generado a partir de los PARAMETROS
		if(!stristr($this->def_nucleo['sql'],"where")){
			//Si no hay un WHERE
			$this->where = " WHERE " . substr($this->where,4);
		}
		//----> FALTA mejorar PARSEO!!!!
		if($sql_b = stristr($this->def_nucleo['sql'],"group by")){//Hay que agregarlo antes de GROUP BY
			$sql_a = substr($this->def_nucleo['sql'],0,strlen($this->def_nucleo['sql'])- strlen($sql_b));
		}
		elseif($sql_b = stristr($this->def_nucleo['sql'],"order by")){//O antes del ORDER BY
			$sql_a = substr($this->def_nucleo['sql'],0,strlen($this->def_nucleo['sql'])- strlen($sql_b));
		}else{
			echo "ATENCION, fallo el parseo del SQL";
			return $this->def_nucleo['sql'];		
		}
//		echo "<br><br><br><br><br>" . $this->def_nucleo['sql'];
//		echo "<br><br>A: " . $sql_a;
//		echo "<br>agregado: " . $this->where;
//		echo "<br>B: " . $sql_b;
		return $sql_a . $this->where . $sql_b;	
	}
	
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%   SALIDA !!!   %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function generar_salida(){
		global $canal;
		if($destinatario=$canal->protegidos["formato"]){
			switch ($destinatario){
				case "xls":
					$this->salida_xls();
					break;
				case "wddx":
					$this->salida_wddx();
					break;
				default:
					die("Se solicito un tipo de salida NO DEFINIDO");
					break;
			}
		}else{
			$this->salida_html();
		}
	}

	//----------------------------------------------------------------------------------------------
	//---------------------------------------- HTML ------------------------------------------------	
	//----------------------------------------------------------------------------------------------	
	
	function salida_html()
	//Genero la salida de HTML
	{
		global $cronometro;														//-------------------> *BENCHMARK
		if($this->cargar_contenido())
		{
			$cronometro->setMarker('HOJA de DATOS: Creo el contenido');				//-------------------> BENCHMARK
			$this->html = $this->contenido->generar_html();
		}
		include("hoja.html.php");
		$cronometro->setMarker('HOJA de DATOS: generar pagina');				//-------------------> BENCHMARK
		//$this->contenido->dump();
		$this->dump();
	}

	function generar_filtro()
	{
		if($this->mostrar_contenido)	//Filtro chato
		{
			$html = "<table width='100' border='0' align='center' cellspacing='0' cellpadding='5'>";
			//$html .= "<tr><td align='center' class='parametro-tabla'>Filtro</td></tr>";
			$html .= "<tr><td class='parametro-tabla'><table cellspacing='0' cellpadding='0' border='0'>";
			for($a=0;$a<count($this->parametros);$a++)
			{
				$html .= "<tr><td class='parametro-fila'>";
				$html .= $this->parametros[$a]->obtener_interface();
				$html .= "</td></tr>";
			}
			$html .= "</table></td></tr>";
			$html .= "<tr><td align='center' class='parametro-tabla'><input type='submit' value='Filtrar'></td></tr>";
			$html .= "</td></tr></table>";		
		}
		else //Filtro vertical
		{
			$html = "<table width='100' align='center' cellspacing='0' cellpadding='5' border='0'>";
			//$html .= "<tr><td align='center' class='parametro-tabla'>Filtro</td></tr>";
			$html .= "<tr><td class='parametro-tabla'><table cellspacing='0' cellpadding='0' border='0'>";
			for($a=0;$a<count($this->parametros);$a++)
			{
				$html .= "<tr><td>";
				$html .= $this->parametros[$a]->obtener_interface();
				$html .= "</td></tr>";
			}
			$html .= "</table></td></tr>";
			$html .= "<tr><td align='center' class='parametro-tabla'><input type='submit' value='Filtrar'></td></tr>";
			$html .= "</td></tr></table>";		
		}
		return $html;
	}

	function generar_popup_vinculos()
	{
		$html ="<div ID='navegar' style='position:absolute; top:300px; left:400px; z-index:99;  visibility:hidden; width:200px;'>
			<table border='1' cellspacing='0' cellpadding='2' width='100%'>
			<tr><td class='hoja-vinculo-cabecera'>
					<table border='0' cellspacing='0' cellpadding='1' width='100%'>
					<tr>
						<td class='hoja-vinculo-titulo' width='100%'>Seleccionar Destino</td>
						<td><a href='#' onClick=\"toggleBox('navegar',0);return false\"><img src='". img_global ."cerrar.gif' border='0'></a></td>
					</tr>
					</table>
			</td></tr>
			<tr><td class='hoja-vinculo-cuerpo'>
			<table border='0' cellspacing='1' cellpadding='2' width='100%'>";
		foreach($this->vinculos["zoom"] as $vinculo){
			$html .= "<tr><td><a href=\"javascript:navegar_zoom('{$vinculo['indice']}')\" class='hoja-vinculo-zoom'>{$vinculo['nombre']}</a></td></tr>";
		}
		foreach($this->vinculos["popup"] as $vinculo){
			$html .= "<tr><td><a href=\"javascript:navegar_popup('{$vinculo['indice']}')\" class='hoja-vinculo-popup'>{$vinculo['nombre']}</a></td></tr>";
		}
		$html .= "</table></td></tr></table></div>";
		return $html;
	}
	
	function generar_array_vinculos()
	{
		$js = "array_vinculos = new Array();\n";
		foreach($this->vinculos["zoom"] as $vinculo){
			$js .= "array_vinculos[{$vinculo['indice']}]=\"{$vinculo['destino']}\";\n";
		}
		foreach($this->vinculos["popup"] as $vinculo){
			$js .= "array_vinculos[{$vinculo['indice']}]=\"{$vinculo['destino']}\";\n";
		}
		return 	$js;	
	}

	//----------------------------------------------------------------------------------------------
	//----------------------------------------- XLS ------------------------------------------------	
	//----------------------------------------------------------------------------------------------	

	function salida_xls()
	{
		$this->cargar_contenido();
		$id_grupo = $_GET["grupo"];
		$id_tabla = $_GET["tabla"];
		//Creo los HEADERs para que el browser abra el archivo con el EXCEL
		//header("Content-type: application/vnd.ms-excel");//Abre el EXCEL embebido en el explorer
		//header("Content-type: application/octet-stream");//Abre el popup de 'guardar archivo'
		//header("Content-Disposition: attachment; filename=consulta.xls");
		echo "<HTML><BODY>";
		echo $this->contenido->generar_xls($id_grupo,$id_tabla);
		echo "</BODY></HTML>";
	}

	//----------------------------------------------------------------------------------------------
	//---------------------------------------- WDDX ------------------------------------------------	
	//----------------------------------------------------------------------------------------------	

	function salida_wddx()
	//Hace un DUMP de la hoja en formato WDDX (Web Distributed Data Exchange)
	{
		header("Content-type: application/octet-stream");//Abre el popup de 'guardar archivo'
		header("Content-Disposition: attachment; filename=".trim($this->identificador).".wddx");
		$output[]=$this->def_nucleo;
		$output[]=$this->def_directivas;
		$output[]=$this->def_parametros;
		$output[]=$this->def_vinculos;
		echo(wddx_serialize_value($output,"hoja_de_datos"));
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function dump()
	{
//		echo dump_arbol($this->def_nucleo,"Definicion de la HOJA");
//		echo dump_arbol($this->def_directivas,"directivas");
//		echo dump_arbol($this->def_parametros,"Definicion de Parametros");
//		echo dump_arbol($this->vinculos,"vinculos");
//		echo dump_arbol($this->def_vinculos,"Definicion de Vinculos");
//		echo dump_arbol($this->parametros,"PARAMETROS");
//		echo dump_arbol($this->parametros_req,"Parametros REQUERIDOS");
	}
}
?>