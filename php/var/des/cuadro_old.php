<?

class cuadro
//###########################################################################################
//
// Muestra un cuadro a partir de un ARRAY de dos dimensiones con datos y otro con titulos
// El resultado se puede ordenar y exportar
//
// Definicion de FORMATOS especiales para COLUNAS
// -----------------------------------------------
// clave array: seleccionar columna
// valor array:funcion a disparar
//
// Hay que crear la CALLBACK tambien
//
// Definicion de las COLUMNAS OCULTAS
// ----------------------------------
// Array con los numeros de columna que se quiere mostrar
// (empezando de 0)
//
// ----> Falta poder definir el aspecto de cada fila (ej: estilo:alineado)
//
//###########################################################################################
{
	var $titulo;
	var $descripcion;
	var $datos;
	var $col_titulos;
	var	$col_formato;
	var $col_ver;
	var $ancho;
	var $exportar;
	var $ordenar;
	var $n_filas;
	var $n_columnas;
	var $orden_columna;
	var $orden_sentido;
	
	function cuadro($titulo,$descripcion,$datos,$col_titulos,$col_formato,$col_ver,$ancho,$exportar,$ordenar)
	{
		$this->titulo = $titulo;
		$this->descripcion = $descripcion;
		$this->datos = $datos;
		$this->col_titulos = $col_titulos;
		$this->col_formato = $col_formato;
		$this->col_ver = $col_ver;
		$this->ancho = $ancho;
		$this->exportar = $exportar;
		$this->ordenar = $ordenar;
		//Obtengo cantidad de filas y columnas
		$this->n_columnas = count($this->datos[0]);
		$this->n_filas = count($this->datos);
		//Ordeno los datos
 		if($this->ordenar)$this->ordenar();
		//Si la salida es XLS o PDF lo genero y devuelvo directamente,
		//Si es HTML espero a que sa llame la funcion 'salida_html()' 
		//(El cuadro podria estar en un contexto)
		if ($_GET['salida']=="xls"){
			$this->salida_xls();
		}
	}
	
	function ordenar()
	//Ordenamiento de array de dos dimensiones
	{
		if(!$this->orden_columna=$_GET['columna'])$this->orden_columna=0;
		//El patron del ordenamiento
		foreach ($this->datos as $fila) { 
			$ordenamiento[] = $fila[$this->orden_columna]; 
		}
		//Ordeno segun el sentido
		if(!$this->orden_sentido=$_GET['sentido']) $this->orden_sentido = "asc";
		if($this->orden_sentido == "asc"){
			array_multisort($ordenamiento, SORT_ASC , $this->datos);
		} elseif ($this->orden_sentido == "des"){
			array_multisort($ordenamiento, SORT_DESC , $this->datos);
		}
	}

//--------------------------------------------------------------------------------
//------------------------------------ Salida HTML -------------------------------
//--------------------------------------------------------------------------------

	function links_exportar()
	{
		if($this->exportar)
		{
			$exportar[0][0] = "xls";
			$exportar[0][1] = "Procesar con MS Excel";
			//$exportar[1][0] = "pdf";
			//$exportar[1][1] = "Imprimir con Acrobat Reader";
			foreach($exportar as $exp){
				$r.= "<a href='".$pagina."&columna=". $this->orden_columna ."&sentido=". $this->orden_sentido . "&salida=". $exp[0] ."'>";
				$r.= "<img src='".$canal->imagen_general("cuadro/exp_". $exp[0] . ".gif")."' alt='". $exp[1] ."' border='0'></a>&nbsp;";
			}
			return $r;
		}
	}

	//--------------------------------------------------------------------------------
	function links_ordenar($col,$titulo)
	{
		if($this->ordenar)
		{
			global $solicitud;
			$sentido[0][0]="asc";
			$sentido[0][1]="Orden ascendente";
			$sentido[1][0]="des";
			$sentido[1][1]="Orden descendente";

			$html.= "<TD align='center' class='columna-titulo-1'>\n";
			$html.= "<table width='100%' border='0' cellspacing='0' cellpadding='1'>\n";
			$html.= "<tr>\n";
			$html.= "<td width='95%' align='center' class='columna-titulo-1-b'>&nbsp;" . $titulo . "&nbsp;</td>\n";
			$html.= "<td width='5%'>";
			foreach($sentido as $sen){
				$sel="";
				if (($col==$this->orden_columna)&&($sen[0]==$this->orden_sentido)) $sel = "_sel";//orden ACTIVO
				$html.= "<a href='".$solicitud->vinculador->generar_url(null,array("columna"=>$col,"sentido"=>$sen[0]))."'>";
				$html.= "<img src='".recurso::imagen_apl("cuadro/sentido_". $sen[0] . $sel . ".gif")."' alt='". $sen[1] ."' border='0'></a>";
			}
			$html.= "</td>\n";		
			$html.= "</tr>\n";
			$html.= "</table>\n";
			$html.= "</TD>\n";	
			return $html;
		}else{
			return $titulo;
		}
	}

	//--------------------------------------------------------------------------------
	function generar_tabla_html()
	{
		if(count($this->datos)<1) return ei_mensaje("info","No hay registros");
		//Genero la tabla madre y la cabecera general
		$html="<table width='{$this->ancho}' align='center' class='cuadro-tabla'>";
/*
		if($this->titulo != ""){
		$html.= "<tr>
				    <td class='cabecera'>
					<table width='100%' border='0' cellspacing='5' cellpadding='1'>
			        	<tr>
			          		<td width='75%' class='cabecera'>{$this->titulo}</td>
			        		<td width='5%'>&nbsp;</td>
			          		<td width='20%' align='right' valign='middle'>".$this->links_exportar()."</td>
			        	</tr>
		      		</table>
					</td>
			  </tr>";
		}*/
		//Genero la descripcion - si exite...
		if($this->titulo<>"") $html.="<tr><td class='cuadro-titulo'>". $this->descripcion ."&nbsp;&nbsp;</td></tr>";
		if($this->descripcion<>"") $html.="<tr><td class='cuadro-subtitulo'>". $this->descripcion ."&nbsp;&nbsp;</td></tr>";
		$html.="<tr>
			    <td class='cuadro-subtitulo'>
				<TABLE width='100%' class='tabla-0'>\n";
		//Genero la cabecera del as columnas
		$html.="  <TR>\n";
			foreach($this->col_titulos as $col)
			{
				$html.="<td class='cuadro-col-titulo'>$col</td>\n";
			}
			//Titulo de los VINCULOS


		$html.="  </TR>\n";
		// Genero las FILAS de datos
		for ($f=0; $f<($this->n_filas);$f++)
		{
			$html.="  <TR>\n";
			foreach($this->col_ver as $col => $estilo)
			{
				//Genero las COLUMAS
				if(isset($this->col_formato[$col])){
					$funcion = $this->col_formato[$col];
					$dato = $funcion($this->datos[$f],$col,$this->col_titulos);
				}else{
					$dato = $this->datos[$f][$col];
				}
				$html.= "    <TD class='cuadro-$estilo'>". $dato . "</TD>\n";
			}
			//VINCULOS


			$html.="  </TR>\n";		
		}		
		$html.= "</table></td>
				</tr>
				</table>";
		return $html;
	}
	
//--------------------------------------------------------------------------------
//------------------------------------ Exportacion -------------------------------
//--------------------------------------------------------------------------------

	function salida_xls()
	//Provee salida tipo EXCEL
	{
		// Creo los HEADERs para que el browser abra el archivo con el EXCEL
		//header("Content-type: application/vnd.ms-excel");
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=consulta.xls");
		$html= "<TABLE border='1'>\n";
		//Genero la cabecera
		$html.="  <TR>\n";
		for ($i=0; $i<$this->n_columnas; $i++){
			$html.= "<TD>" . $this->col_titulos[$i] . "</td>\n";
		}
		$html.="  </TR>\n";
		// Genero las filas de datos
		for ($f=0; $f<($this->n_filas);$f++)
		{
			$html.="  <TR>\n";
			for ($i=0; $i<$this->n_columnas; $i++)
			{
				$html.= "    <TD>". $this->datos[$f][$i] . "</TD>\n";				
			}
			$html.="  </TR>\n";		
		}		
		$html.= "</table>";
		echo $html;	
	}

	function salida_xml(){}//Provee salida tipo XML
} 

//###########################################################################################
//###########################################################################################

class cuadro_db extends cuadro
//Muestra un cuadro a partir de un recordeset
//El titulo de cada columna esta dado por es AS '...' del SELECT
//El caracter '_' se reemplaza por ' ' y se pone la primera letra de cada palabra en mayuscula
{
	function cuadro_db($titulo,$descripcion,$sql,$col_formato,$col_ver,$ancho,$exportar,$ordenar,$fuente="apl",$objeto)
	{
		global $db, $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		$ver_todo = false;

		if($rs->EOF) $datos = array();
		$datos = $rs->getArray();
		if(!isset($col_ver))$ver_todo = true;
		for ($i=0;$i<count($datos[0]);$i++)
		{
			$col = $rs->FetchField($i);
			$col_titulos[] = $col->name;
			if($ver_todo)$col_ver[]="t";
		}
		parent::cuadro($titulo,$descripcion,$datos,$col_titulos,$col_formato,$col_ver,$ancho,$exportar,$ordenar,$objeto);
	}
}

//###########################################################################################
//###########################################################################################

class cuadro_db_paginado extends cuadro_db
{
//Soporta paginacion a partir de los modificadores del SELECT
//Solo se dedica a reescribir el SELECT

//Falta: indice de paginas, selector de registros por pagina

//Atencion, el manejo de conexiones secundarias es lamentable...
//Esto se va a mejorar en la nueva infraestructura depurada

	var $registros_pagina;
	var $registros_total;
	var $registros_offset;
	var $pagina;
	var $paginas_total;
	
	function cuadro_db_paginado($titulo,$descripcion,$sql,$col_formato,$col_ver,$ancho,$exportar,$ordenar,$fuente=0,$objeto=null)
	{
		global $canal;
		if (!$this->pagina=$canal->protegidos["cuadro_paginacion"]) $this->pagina=0; //--> En que pagina estoy?
		$this->registros_pagina = 50;//--> registros por pagina
		$this->registros_offset = $this->pagina * $this->registros_pagina;//OFFSET, cuantos registros hay que saltar
		$this->registros_total = $this->calcular_registros($sql,$fuente);//Total registros SELECT
		$this->paginas_total = (int)($this->registros_total / $this->registros_pagina);	//Total de paginas
		$sql = $this->reescribir_sql($sql);
		//echo $sql;
		parent::cuadro_db($titulo,$descripcion,$sql,$col_formato,$col_ver,$ancho,$exportar,$ordenar,$fuente,$objeto);
	}
	//--------------------------------------------------------------------------//

	function calcular_registros($sql,$fuente)
	//Esto no funciona en SELECTS que tienen GROUP BY!!!!!!!!!!!
	{
		global $db, $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;		
		//echo "ORIGINAL: $sql<br>";
		//--> Armo el SQL que deduce la cantidad de registros de la tabla
		//1) tomo todo el SQL menos las columnas de SELECT
		$sql_cant_registros = "SELECT COUNT(*) " . stristr($sql,"FROM");
		//2) Le saco el ORDER BY si tiene
		//echo "NUEVO 1: $sql_cant_registros<br>";
		if($final=stristr($sql_cant_registros,"order by")){
			$sql_cant_registros=substr($sql_cant_registros,0,strlen($sql_cant_registros)-strlen($final));
			$sql_cant_registros .= ";";
		}
		//echo "NUEVO 2: $sql_cant_registros<br>";
		//Ejecuto el SQL que me dice cuantos registros tengo
		if($fuente==0){
			$rs = $db->Execute($sql_cant_registros);
		}else{
			$db2 = abrir_db_secundaria($fuente);
			$rs = $db2->Execute($sql_cant_registros);
			$db2->close();
		}
		return $rs->fields[0];
	}
	//--------------------------------------------------------------------------//

	function reescribir_sql($sql)
	//PostgreSQL y MySQL poseen la misma sintaxis, si se agrega un motor hay que hacer un SWITCH
	{
		//echo "ORIGINAL: $sql<br>";
		$sql_paginado = $sql ." LIMIT ". $this->registros_pagina ." OFFSET ". $this->registros_offset;
		//echo "NUEVO: $sql_paginado<br>";
		return $sql_paginado;
	}
	//--------------------------------------------------------------------------//
	
	function generar_tabla_html()
	//Le agrega al display de la tabla las barras de paginacion
	{
		if($this->paginas_total>0){
			$html .= $this->barra_paginacion();	//Interface paginacion SUPERIOR
		}
		$html .=  parent::generar_tabla_html();							//Datos
		if($this->paginas_total>0){
			$html .= $this->barra_paginacion();	//Interface paginacion INFERIOR
		}
		return $html;
	}

 	//--------------------------> Interface de paginacion <---------------------//
	
	function barra_paginacion()
	//Barra paginacion
	{
		global $canal;
		$html = "<TABLE width='500' align='center' cellPadding='2' cellspacing='1' class='cabecera'><tr>\n";
		//--> Pagina ANTERIOR?
		$html .= "<td class='columna-titulo-1' width='35%'>\n";
		if ($this->pagina >= 1) {
			$html .= "<a href='".$canal->generar_vinculo_pers(null,array("cuadro_paginacion"=>($this->pagina-1))) ."'>Página Anterior</a>\n";
		}
		$html .= "</td>\n";
		//Leyenda pagina
		$html .= "<td class='columna-titulo-1' width='35%'>Página ". ($this->pagina + 1) . " de ". ($this->paginas_total + 1) . "</td>\n";		
		//--> PROXIMA pagina?
		$html .= "<td class='columna-titulo-1' width='35%'>\n";
		if ($this->pagina < $this->paginas_total) {
			$html .= "<a href='" . $canal->generar_vinculo_pers(null,array("cuadro_paginacion"=>($this->pagina+1)))."'>Proxima Página</a>\n";
		}
		$html .= "</td>\n";
		$html .= "</tr></table>\n";
		return $html;
	}
	//--------------------------------------------------------------------------//
/*	
	function indice_paginas($actual,$total,$registros)
	//Indice de paginas
	{
		for ($i=0;$i<$total+1;$i++)
		{
			if ($actual!=$i){
				$indice .= "<a href='" . $_SERVER['PHP_SELF'] . "?pagina=" . $i . "&registros=". $registros ."'>". ($i+1) ."</a>";
			}else{
				$indice	.= "<b>".($i+1)."</b>";
			}
			if ($i<$total) $indice .= " - ";
		}
		return $indice;
	}
*/
 	//--------------------------------------------------------------------------//

	function dump()
	{
		$dump["pagina"]=$this->pagina;
		$dump["registros_offset"]=$this->registros_offset;
		$dump["registros_pagina"]=$this->registros_pagina;
		$dump["registros_total"]=$this->registros_total;
		$dump["paginas_total"]=$this->paginas_total;
		dump_arbol($dump);
	}
}
//###########################################################################################
//###########################################################################################
/*
  <form name="formulario" method="post" action="<? echo $_SERVER['PHP_SELF'] . "?pagina=" . $paginaActual ?>">
    <tr> 
      <td align="center"> <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="36%" class="texto">&nbsp;<strong><? echo "Pagina " . ($paginaActual+1) . " de " . ($totalPaginas+1) ?></strong></td>
            <td width="64%" align="right"><span class="texto"> <? echo comboPaginados($registrosPorPagina) ?> 
              </span> <input type="submit" name="Submit2" value="Cambiar"></td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#000000"> 
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="23%" class="texto">&nbsp;&nbsp;<? echo $html_anterior ?></td>
            <td width="49%" align="center" class="letra"><? echo indiceDePaginas($paginaActual,$totalPaginas,$registrosPorPagina) ?></td>
            <td width="28%" align="right"><? echo $html_proxima ?>&nbsp;&nbsp;</td>
          </tr>
        </table></td>
    </tr>
  </form>
*/
?>