<?
require_once('nucleo/componentes/runtime/objeto.php');

class objeto_cuadro extends objeto
/*
    @@acceso: publico
    @@desc: Esta clase implementa un listado ordenable y paginable.
    
    
    Falta un metodo que devuelva el ID del registro que se eligio...
    
*/
{
    var $cantidad_columnas;                 //protegido | int | Cantidad de columnas a mostrar
	var $filas;
    var $orden_columna;                     //protegido | int | Columna utilizada para realizar el orden
    var $orden_sentido;                     //protegido | string | Sentido del orden ('asc' / 'desc')
    var $ordenar;                           //protegido | int | orden activado?
    var $datos;                             //protegido | array | Los datos que constituyen el contenido del cuadro
    var $columnas_clave;                    //protegido | 
    var $columnas_clave_posicion;
    var $indice_posicion_col_rec;
	var $datos_pdf;							//protegido | array | Los datos que constituyen el contenido del PDF
	var $propiedades_pdf;					//protegido | array | Los datos que constituyen las propiedades del cuadro
    //Paginacion
    var $pag_get_cantidad_registros;    
    var $pag_cantidad_paginas;
    var $pag_tamano;
    var $pag_actual;
	var $saltear_paginacion;
	var $sql;									//SQL del cuadro
    
//################################################################################
//###########################                         ############################
//###########################      INICIALIZACION     ############################
//###########################                         ############################
//################################################################################
        
    function objeto_cuadro($id)
/*
    @@acceso: constructor
    @@desc: 
*/
    {
        parent::objeto($id);
        $this->cantidad_columnas = count($this->info_cuadro_columna);
        //Nombre de los propagadores de estado interno
        $this->propagador_orden_columna = $this->id[1] . "orden_columna";
        $this->propagador_orden_sentido = $this->id[1] . "orden_sentido";
        $this->propagador_pagina = $this->id[1] . "pagina";
        //----------------------------------------------------------------------
        //---------  Manejo de CLAVES  -----------------------------------------
        //----------------------------------------------------------------------
        if(isset($this->info_cuadro["columnas_clave"])){
            $this->columnas_clave = explode(",",$this->info_cuadro["columnas_clave"]);
            $this->columnas_clave = array_map("trim",$this->columnas_clave);
        }else{
            $this->columnas_clave = null;
        }
        //----------------------------------------------------------------------
        //----------------> Manejo de INFORMACION autopropagada <---------------
        //----------------------------------------------------------------------

        //**************** Parametros ORDENAMIENTO ***********************
        //Cargo el ORDEN_COLUMNA de vinculo o memoria
        if(!$this->orden_columna = $this->solicitud->hilo->obtener_parametro($this->propagador_orden_columna)){
            if(isset($this->memoria[$this->propagador_orden_columna])){
                $this->orden_columna = $this->memoria[$this->propagador_orden_columna];
            }else{
                $this->orden_columna = null;
            }
        }else{
            $this->memoria[$this->propagador_orden_columna]=$this->orden_columna;
        }
        //Cargo el SENTIDO_COLUMNA de vinculo o memoria
        if(!$this->orden_sentido = $this->solicitud->hilo->obtener_parametro($this->propagador_orden_sentido)){
            if(isset($this->memoria[$this->propagador_orden_sentido])){
                $this->orden_sentido = $this->memoria[$this->propagador_orden_sentido];
            }else{
                $this->orden_sentido = null;
            }
        }else{
            $this->memoria[$this->propagador_orden_sentido]=$this->orden_sentido;
        }
        //************* Parametros de PAGINACION **************************
        //Pagina ACTUAL
        if(!$this->pag_actual = $this->solicitud->hilo->obtener_parametro($this->propagador_pagina)){
            if(isset($this->memoria[$this->propagador_pagina])){
                $this->pag_actual = $this->memoria[$this->propagador_pagina];
            }else{
                $this->pag_actual = 1;
            }
        }else{
            $this->memoria[$this->propagador_pagina]=$this->pag_actual;
        }

        //----------------------------------------------------------------
        //-------------------------> INICIALIZACION <---------------------
        //----------------------------------------------------------------
        // Tamao de pagina, por defecto es 80       
        $this->pag_tamano = isset($this->info_cuadro["tamano_pagina"]) ? 
                                $this->info_cuadro["tamano_pagina"] : 80;
        // Ordenamiento
        if($this->orden_sentido && $this->orden_columna){
            $this->ordenar_datos = true;
        }else{
            $this->ordenar_datos = false;
        }
    }
   
//################################################################################
//###########################                         ############################
//###########################         UTILERIA        ############################
//###########################                         ############################
//################################################################################
    
	 function crear_sql($where=null,$from=null)
	 {
        //Concateno el WHERE y el FROM pasado por el consumidor
        $sql = sql_agregar_clausulas_where( stripslashes($this->info_cuadro["sql"]),$where);
        $sql = sql_agregar_tablas_from($sql,$from);
		  return $sql;	 
	 }
	 
    function cargar_datos($where=null,$from=null,$saltear_paginacion=false,$memorizar=true)
/*
    @@acceso: publico
    @@desc: Carga los datos del cuadro desde la base
    @@param: array | sentencias WHERE a acoplar
    @@param: array | Sentencias FROM a acoplar
    @@param: boolean | Desactivar la paginacion
    @@retorno: boolean | Estado resultante de la operacion
*/
    {
        //Generacion directa de clausulas WHERE a traves de vinculos al objeto
        //ATENCION: se confia en un paso correcto de parametros
        if(isset($this->canal_recibidos))
        {
            //La entrada por el CANAL  fuerza el estado SM, mas alla de la memoria
            $clave = explode(apex_qs_separador,$this->canal_recibidos);     
            $columnas = explode(",",$this->info_cuadro['asociacion_columnas']);
            for($a=0;$a<count($columnas);$a++){
                $where_vinculo[]= "{$columnas[$a]} = '{$clave[$a]}'";
            }
            if(is_array($where)){
                $where = array_merge($where,$where_vinculo);
            }else{
                $where = $where_vinculo;
            }
        }

        global $db,$ADODB_FETCH_MODE;
        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
    
        //Setear MEMORIA
        if(isset($where)){
            $this->memoria["where"] = $where;
            $this->memoria["from"] = $from;
        }else{
            if(isset($this->memoria["where"])){
                //Recuperar MEMORIA
                $where = $this->memoria["where"];
                $from = $this->memoria["from"];
            }
        }
		if(!$memorizar){
	   	     $this->borrar_memoria();
		}

        //Concateno el WHERE y el FROM pasado por el consumidor
		$sql = $this->crear_sql($where, $from);

        //----------------- PAGINACION ----------------
		$this->saltear_paginacion = $saltear_paginacion;
        if(($this->info_cuadro["paginar"]) && !($this->saltear_paginacion)){
            // 1) Calculo la cantidad de registros
            $this->pag_get_cantidad_registros = $this->calcular_get_cantidad_registros($where, $from);
            //echo "REGISTROS: " . $this->pag_get_cantidad_registros;
            if($this->pag_get_cantidad_registros > 0){
                // 2) Calculo la cantidad de paginas
                $this->pag_cantidad_paginas = ceil($this->pag_get_cantidad_registros/$this->pag_tamano);
               // echo "PAGINAS: " . $this->pag_cantidad_paginas;
               // echo "PAGINA ACTUAL: ".$this->pag_actual;
                if ($this->pag_actual > $this->pag_cantidad_paginas) 
                {
                    $this->pag_actual = 1;
//                    $this->memoria[$this->propagador_pagina]= $this->pag_actual;
                }
                
                $sql = $this->obtener_sql_paginado($sql);
            }else
            {
                $this->pag_cantidad_paginas = 0;            
            }
        }
        $this->sql = $sql;
        $rs = toba::get_db($this->info["fuente"])->Execute($sql);
        if(!$rs){//SQL mal formado
            $this->observar("error","OBJETO CUADRO [cargar_datos] - No se genero un recordset [SQL] $sql - [ERROR] " . 
                            $db[$this->info["fuente"]][apex_db_con]->ErrorMsg(),true,true,true);
            return false;
        }
        if($rs->EOF){//NO existe el registro
            $this->observar("info","OBJETO CUADRO [cargar_datos] - No hay registros");
            return false;
        }
        $this->datos = $rs->getArray();
        //ei_arbol($this->datos,"DATOS");
        if($this->hay_ordenamiento()){
            $this->ordenar();
        }
		$this->filas = count($this->datos);
        return true;
    }
//--------------------------------------------------------------------------

    function ordenar()
/*
    @@acceso: protegido
    @@desc: 
*/
    //Ordenamiento de array de dos dimensiones
    {
        //echo "ordenar: " . $this->orden_columna;
        foreach ($this->datos as $fila) { 
            $ordenamiento[] = $fila[$this->orden_columna]; 
        }
        //Ordeno segun el sentido
        if($this->orden_sentido == "asc"){
            array_multisort($ordenamiento, SORT_ASC , $this->datos);
        } elseif ($this->orden_sentido == "des"){
            array_multisort($ordenamiento, SORT_DESC , $this->datos);
        }
    }
//--------------------------------------------------------------------------
	function hay_ordenamiento()
	{
        return $this->ordenar_datos;
	}
	
//--------------------------------------------------------------------------
    function obtener_datos()
    {
        return $this->datos;    
    }
//--------------------------------------------------------------------------
    
    function obtener_sql()
    {
        return $this->sql;    
    }
//--------------------------------------------------------------------------
    function calcular_get_cantidad_registros($where, $from)
/*
    @@acceso: protegido
    @@desc: Calcular la cantidad de registros
*/
    {
        global $db,$ADODB_FETCH_MODE;
        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		//Si el SELECT esta AGRUPADO, o tiene subquerys, se ejecuta completo 
		//y se hace un RecordCount para ver cuantos registros tiene
		//(El parseo es mas complejo, esto hay que mejorarlo...)
		//ATENCION!!! No tengo que agregar en esta lista al DISTINCT
        if( (preg_match("/GROUP BY/i",$this->info_cuadro["sql"])) ||
        	(preg_match_all("/(from)/i",$this->info_cuadro["sql"],$x) >=2 ) ){
	        $sql = sql_agregar_clausulas_where($this->info_cuadro["sql"], $where);
    	    $sql = sql_agregar_tablas_from($sql, $from);
	        $rs = $db[$this->info["fuente"]][apex_db_con]->Execute($sql);
	        return $rs->RecordCount();
        }else{
	        //Genero SQL de calcula de registros
  	  	    //Elimino los nombres de las columnas
            $sql = "SELECT COUNT(*) as x_x_x_x  " . stristr($this->info_cuadro["sql"],"FROM");
	        // 2) Elimino el ORDER BY final
	        if($final=stristr($sql,"order by")){
	            $sql=substr($sql,0,strlen($sql)-strlen($final));
	            $sql .= ";";
	        }
	        $sql = sql_agregar_clausulas_where($sql, $where);
	        $sql = sql_agregar_tablas_from($sql, $from);
	        //echo "SQL para calcular registros: " . $sql . "<br>";
	        $rs = $db[$this->info["fuente"]][apex_db_con]->Execute($sql);
	        if(!$rs){//SQL mal formado
	            $this->observar("error","OBJETO CUADRO [calcular_get_cantidad_registros] - No se genero un recordset [SQL] $sql - [ERROR] " . 
	                            $db[$this->info["fuente"]][apex_db_con]->ErrorMsg(),true,true,true);
	            return -1;
	        }
	        if($rs->EOF){//NO existe el registro
/*
	            $this->observar("error","OBJETO CUADRO [calcular_get_cantidad_registros] - EOF [SQL] $sql - [ERROR] " . 
	                            $db[$this->info["fuente"]][apex_db_con]->ErrorMsg(),true,true,true);
*/
	            return -1;
	        }
	        return $rs->fields["x_x_x_x"];
        }
    }
//--------------------------------------------------------------------------

    function obtener_sql_paginado($sql)
/*
    @@acceso: protegido
    @@desc: 
    @@pendiente: Prepadado solo para POSTGRESQL
*/
    {
        if($sobra=stristr($sql,";")){
            $sql=substr($sql,0,strlen($sql)-strlen($sobra));
        }
        $limit = " LIMIT " . $this->pag_tamano . " ";
        //OFFSET
        $offset = " OFFSET " . (($this->pag_actual - 1) * $this->pag_tamano);
        return $sql . $limit . $offset . " ;";
    }
//--------------------------------------------------------------------------

    function obtener_clave_fila($fila)
    {
        $id_fila = "";
        foreach($this->columnas_clave as $clave){
            $id_fila .= $this->datos[$fila][$clave] . apex_qs_separador;
        }
        $id_fila = substr($id_fila,0,(strlen($id_fila)-(strlen(apex_qs_separador))));   
        return $id_fila;
    }

//################################################################################
//###########################                         ############################
//#################################    SALIDA   ##################################
//###########################                         ############################
//################################################################################
    
    function barra_superior_especifica()
/*
    @@acceso: protegido
    @@desc: Barra especifica de la clase
*/
    {
        $id_prop_objeto = array( 'proyecto'=> $this->id[0], 'objeto'=>$this->id[1] );
        //echo $this->solicitud->vinculador->generar_solicitud("toba","/basicos/generador_pdf", $id_objeto);

        if($this->info_cuadro["exportar_pdf"]){
            $id_prop_objeto["saltear_paginacion"] = 1;
			echo $this->solicitud->vinculador->obtener_vinculo_a_item("toba","/basicos/generador_pdf", $id_prop_objeto, true);
        }
        if($this->info_cuadro["exportar_xls"]){
            $id_prop_objeto["saltear_paginacion"] = 1;
			echo $this->solicitud->vinculador->obtener_vinculo_a_item("toba","/basicos/generador_excel", $id_prop_objeto, true);
        }
    }

//--------------------
//------- HTML -------
//--------------------

    function obtener_html($mostrar_cabecera=true, $titulo=null)
    //Genera el HTML del cuadro
    {
		//Reproduccion del titulo
		if(isset($titulo)){
			$this->memoria["titulo"] = $titulo;
		}else{
			if(isset($this->memoria["titulo"])){
				$titulo = $this->memoria["titulo"];
			}
		}
		//Manejo del EOF
        if($this->filas == 0){
            //La consulta no devolvio datos!
            if ($this->info_cuadro["eof_invisible"]!=1){
                if(trim($this->info_cuadro["eof_customizado"])!=""){
                    echo ei_mensaje($this->info_cuadro["eof_customizado"]);
                }else{
                    echo ei_mensaje("La consulta no devolvio datos!");
                }
            }
        }else{
            if(!($ancho=$this->info_cuadro["ancho"])) $ancho = "80%";
            //echo "<br>\n";
            echo "\n\n<div align='center'>\n";
            echo "<table class='objeto-base' width='$ancho'>\n\n\n";

            if($mostrar_cabecera){
                echo "<tr><td>";
                $this->barra_superior($titulo);
                echo "</td></tr>\n";
            }
            if($this->info_cuadro["subtitulo"]<>""){
                echo"<tr><td class='lista-subtitulo'>". $this->info_cuadro["subtitulo"] ."</td></tr>\n";
            }
//            $this->generar_html_barra_paginacion();

            echo "<tr><td>";
            echo "<TABLE width='100%' class='tabla-0'>";
            //------------------------ Genero los titulos
            echo "<tr>\n";
            for ($a=0;$a<$this->cantidad_columnas;$a++)
            {
                if(isset($this->info_cuadro_columna[$a]["ancho"])){
                    $ancho = " width='". $this->info_cuadro_columna[$a]["ancho"] . "'";
                }else{
                    $ancho = "";
                }
                echo "<td class='lista-col-titulo' $ancho>\n";
                $this->cabecera_columna(    $this->info_cuadro_columna[$a]["titulo"],
                                            $this->info_cuadro_columna[$a]["valor_sql"],
                                            $a );
                echo "</td>\n";
            }
            echo "</tr>\n";
            //----------------------- Genero las filas
            for ($f=0; $f< $this->filas; $f++)
            {
                echo "<tr>\n";
                for ($a=0;$a< $this->cantidad_columnas;$a++)
                {
                    //----------> Comienzo una CELDA!!
                    //*** 1) Recupero el VALOR
                    if(isset($this->info_cuadro_columna[$a]["valor_sql"])){
                        $valor = $this->datos[$f][$this->info_cuadro_columna[$a]["valor_sql"]];
                        //Hay que formatear?
                        if(isset($this->info_cuadro_columna[$a]["valor_sql_formato"])){
                            $funcion = "formato_" . $this->info_cuadro_columna[$a]["valor_sql_formato"];
                            //Formateo el valor
                            $valor = $funcion($valor);
                        }
                    }elseif(isset($this->info_cuadro_columna[$a]["valor_fijo"])){
                        $valor = $this->info_cuadro_columna[$a]["valor_fijo"];
                    }else{
                        $valor = "";
                    }
                    //*** 2) PRoceso la columna
                    //Esto no se utiliza desde el instanciador
                    if(!$this->solicitud->hilo->entorno_instanciador()){
                        if(isset($this->info_cuadro_columna[$a]["valor_proceso"])){
                            $metodo_procesamiento = $this->info_cuadro_columna[$a]["valor_proceso"];
                            $valor = $this->$metodo_procesamiento($f, $valor);
                        }
                    }
                    //*** 3) Generacion de vinculos
                    if(trim($this->info_cuadro_columna[$a]["vinculo_indice"])!=""){
                        $id_fila = $this->obtener_clave_fila($f);
                        //Genero el VINCULO
                        $vinculo = $this->solicitud->vinculador->obtener_vinculo_de_objeto( $this->id,
                                                                                $this->info_cuadro_columna[$a]["vinculo_indice"],
                                                                                $id_fila, true, $valor);
                        //El vinculador puede no devolver nada en dos casos: 
                        //No hay permisos o el indice no existe
                        if(isset($vinculo)){
                            $valor = $vinculo;
                        }
                    }
                    //*** 4) Genero el HTML
                    echo "<td class='".$this->info_cuadro_columna[$a]["estilo"]."'>\n";
                    echo $valor;
                    echo "</td>\n";
                    //----------> Termino la CELDA!!
                }
                echo "</tr>\n";
            }
            //----------------------- Genero totales??
			$this->generar_html_totales();
            echo "</table>\n";
            echo "</td></tr>\n";
            $this->generar_html_barra_paginacion();
            echo "</table>\n";
            echo "</div>";          
            //echo "<br>\n";
        }
    }
//--------------------------------------------------------------------------

	function generar_html_totales()
	{
		//Selecciono registros a sumarizar
		$total = array();
		for ($a=0;$a<$this->cantidad_columnas;$a++){
		    if(isset($this->info_cuadro_columna[$a]["total"])){
				$total[$this->info_cuadro_columna[$a]["valor_sql"]]=0;
				$pie_columna[$a] =& $total[$this->info_cuadro_columna[$a]["valor_sql"]];
				$pie_columna_estilo[$a] = $this->info_cuadro_columna[$a]["estilo"];
				if(isset($this->info_cuadro_columna[$a]["valor_sql_formato"])){
					$total_funcion[$this->info_cuadro_columna[$a]["valor_sql"]] =
						$this->info_cuadro_columna[$a]["valor_sql_formato"];
				}
		    }else{
		    	$pie_columna[$a] = "&nbsp;";
		    	$pie_columna_estilo[$a] = 'lista-col-titulo';
			}
		}		
		if(count($total)==0) return;
		//Sumarizo
		for ($f=0; $f< $this->filas; $f++){
			foreach(array_keys($total) as $columna){
				$total[$columna] +=  $this->datos[$f][$columna];
			}
		}
		//Aplico el formato de la columna a la sumarizacion
		if(is_array($total_funcion)){
			foreach(array_keys($total_funcion) as $tot){
				$funcion = "formato_" . $total_funcion[$tot];
				$total[$tot] = $funcion($total[$tot]);
			}		
		}
		//Genero el HTML
		echo "<tr>\n";
		for($a=0; $a<count($pie_columna);$a++){
			echo "<td class='".$pie_columna_estilo[$a]."'><b>\n";
			echo $pie_columna[$a];
			echo "</b></td>\n";
		}
		echo "</tr>\n";
	}
//--------------------------------------------------------------------------


    function cabecera_columna($titulo,$columna,$indice)
    //Genera la cabecera de una columna
    {
        //Solo son ordenables las columnas extraidas del recordse!!!
        //Las generadas de otra forma llegan con el nombre vacio
        if(trim($columna)!=""){
            if($this->info_cuadro["ordenar"])
            {
                if($this->info_cuadro_columna[$indice]["no_ordenar"]!=1)
                {
                    $sentido[0][0]="asc";
                    $sentido[0][1]="Orden ascendente";
                    $sentido[1][0]="des";
                    $sentido[1][1]="Orden descendente";

                    echo  "<table class='tabla-0'>\n";
                    echo  "<tr>\n";
                    echo  "<td width='95%' align='center' class='lista-col-titulo'>&nbsp;" . $titulo . "&nbsp;</td>\n";
                    echo  "<td width='5%'>";
                    foreach($sentido as $sen){
                        $sel="";
                        if (($columna==$this->orden_columna)&&($sen[0]==$this->orden_sentido)) $sel = "_sel";//orden ACTIVO
                        $imagen = recurso::imagen_apl("sentido_". $sen[0] . $sel . ".gif", true, null, null,$sen[1]);
                        echo $this->autovinculacion(array($this->propagador_orden_sentido=>$sen[0],
                                                        $this->propagador_orden_columna=>$columna),$imagen);
                    }
                    echo  "</td>\n";        
                    echo  "</tr>\n";
                    echo  "</table>\n";
                }else{
                    echo $titulo;
                }
            }else{
                echo $titulo;
            }
        }
        else            //Modificacion para que muestre los titulos de los vinculos
        {
            if(trim($this->info_cuadro_columna[$indice]["vinculo_indice"])!="")
            {           
                echo $titulo;
            }
        }
    }
//--------------------------------------------------------------------------

    function generar_html_barra_paginacion()
    //Barra para navegar la paginacion
    //Por ahora solo conoce la sintaxis de PostgreSQL para paginar
    {
        if( !($this->pag_tamano >= $this->pag_get_cantidad_registros) ){
            //Calculo los posibles saltos
            //Primero y Anterior
            if($this->pag_actual == 1){
                $anterior = recurso::imagen_apl("paginacion/no_anterior.gif",true);
                $primero = recurso::imagen_apl("paginacion/no_primero.gif",true);       
            }else{
                $anterior = $this->autovinculacion( array( $this->propagador_pagina => ($this->pag_actual - 1) ),
                            recurso::imagen_apl("paginacion/si_anterior.gif",true,null,null,"Pï¿½ina Anterior") );
                $primero = $this->autovinculacion(array( $this->propagador_pagina => 1),
                                recurso::imagen_apl("paginacion/si_primero.gif",true,null,null,"Pï¿½ina Inicial") );
            }
            //Ultimo y Siguiente
            if( $this->pag_actual == $this->pag_cantidad_paginas ){
                $siguiente = recurso::imagen_apl("paginacion/no_siguiente.gif",true);
                $ultimo = recurso::imagen_apl("paginacion/no_ultimo.gif",true);     
            }else{
                $siguiente = $this->autovinculacion( array( $this->propagador_pagina => ($this->pag_actual + 1) ),
                                 recurso::imagen_apl("paginacion/si_siguiente.gif",true,null,null,"Pï¿½ina Siguiente") );
                $ultimo = $this->autovinculacion(array( $this->propagador_pagina => $this->pag_cantidad_paginas ),
                                recurso::imagen_apl("paginacion/si_ultimo.gif",true,null,null,"Pï¿½ina Final") );
            }
            //Creo la barra de paginacion
            if($this->info_cuadro["paginar"]){
                echo "\n\n\n<tr><td  class='lista-obj-titcol'>";
                echo "<div align='center'><hr>\n";
                echo "<table class='tabla-0'><tr>";
                echo "<td  class='lista-pag-bot'>&nbsp;</td>";
                echo "<td  class='lista-pag-bot'>$primero</td>";
                echo "<td  class='lista-pag-bot'>$anterior</td>";
                echo "<td  class='lista-pag-bot'>&nbsp;Página&nbsp;<b>{$this->pag_actual}</b>&nbsp;de&nbsp;<b>{$this->pag_cantidad_paginas}</b>&nbsp;</td>";
                echo "<td  class='lista-pag-bot'>$siguiente</td>";
                echo "<td class='lista-pag-bot' >$ultimo</td>";
                echo "<td  class='lista-pag-bot'>&nbsp;</td>";
                echo "<td  class='lista-pag-bot'>";
		        $id_prop_objeto = array( 'proyecto'=> $this->id[0], 'objeto'=>$this->id[1] );
		        //echo $this->solicitud->vinculador->generar_solicitud("toba","/basicos/generador_pdf", $id_objeto);

		        if($this->info_cuadro["exportar_pdf"]){
					if(($this->info_cuadro["paginar"]) && (!$this->saltear_paginacion)){
		                $id_prop_objeto["saltear_paginacion"] = 0;
						echo $this->solicitud->vinculador->obtener_vinculo_a_item("toba","/basicos/generador_pdf_pag", $id_prop_objeto, true,null,null,null,"exp_pdf_pag.gif","apex");
					}
		        }
		        if($this->info_cuadro["exportar_xls"]){
					if(($this->info_cuadro["paginar"]) && (!$this->saltear_paginacion)){
		                $id_prop_objeto["saltear_paginacion"] = 0;
						echo $this->solicitud->vinculador->obtener_vinculo_a_item("toba","/basicos/generador_excel_pag", $id_prop_objeto, true);
					}
		        }
                echo "</td>";
                echo "</tr></table>";
                echo "</div>";              
				if (true || $this->info_cuadro["pag_mostrar_resumen"]) //ATENCION: Agregar el parametro al admin
				{
					$rango_inicial = ($this->pag_tamano * ($this->pag_actual - 1)) + 1;
					$rango_final_teorico = ($rango_inicial + $this->pag_tamano) - 1;
					$rango_final = ($this->pag_actual != $this->pag_cantidad_paginas) ? $rango_final_teorico : $this->pag_get_cantidad_registros;
					echo "<div class='lista-pag-descr'>Resultados $rango_inicial al $rango_final de {$this->pag_get_cantidad_registros}</div>";
				}
                echo "</td></tr>\n\n\n";

				//Exportacion de PAGINAS SEPARADAS		
            }
        }
    }

//--------------------
//------- PDF --------
//--------------------
	function procesar_pdf()
	{
		//Obtengo propiedades de cuadro y columnas
		if (!$this->procesar_propiedades_pdf()) {
		    $this->observar("info","No se han podido cargar las PROPIEDADES del PDF");
		}

		$filas = count($this->datos);
		for ($f=0; $f<$filas; $f++){
			for ($a=0;$a<$this->cantidad_columnas;$a++){
                //*** 1) Recupero el VALOR y verifico que la columna deba ser incluida en el PDF
				if(isset($this->info_cuadro_columna[$a]["valor_sql"]) && ($this->info_cuadro_columna[$a]["mostrar_pdf"] == 1)){
					if (!is_null($this->datos[$f][$this->info_cuadro_columna[$a]["valor_sql"]])){
						$valor = $this->datos[$f][$this->info_cuadro_columna[$a]["valor_sql"]];
						//return $valor;
						//$this->propiedades_pdf[$this->info_cuadro_columna[$a]["valor_sql"]] = parsear_propiedades($this->info_cuadro_columna[$a]["pdf_propiedades"]);
					}else{
						$valor = "";
					}
					//Hay que formatear?
                   	if(isset($this->info_cuadro_columna[$a]["valor_sql_formato"]) && $this->info_cuadro_columna[$a]["mostrar_pdf"] == 1){
                   		$funcion = "formato_" . $this->info_cuadro_columna[$a]["valor_sql_formato"];
						//Formateo el valor
                   		$valor = ereg_replace("&nbsp;","",$funcion($valor));
                	}
                }elseif(isset($this->info_cuadro_columna[$a]["valor_fijo"]) && $this->info_cuadro_columna[$a]["mostrar_pdf"] == 1){
                    $valor = $this->info_cuadro_columna[$a]["valor_fijo"];
                }else{
                	$valor = null;
                }
                //*** 2) PRoceso la columna
                //Esto no se utiliza desde el instanciador
				if(!$this->solicitud->hilo->entorno_instanciador()){
					if(isset($this->info_cuadro_columna[$a]["valor_proceso"])){
						$metodo_procesamiento = $this->info_cuadro_columna[$a]["valor_proceso"];
                		$valor = $this->$metodo_procesamiento($f, $valor);
                	}
                }
                //Si valor viene seteado lo guardo
			    if (!is_null($valor)){
            		$this->datos_pdf[$f][$this->info_cuadro_columna[$a]["valor_sql"]] = $valor;
        		}
        	}
        }
		return true;
	}    

	function procesar_propiedades_pdf()
	{
		//Recupero las propiedades del cuadro
		$this->propiedades_pdf = parsear_propiedades($this->info_cuadro["pdf_propiedades"]);
		//Recupero las propiedades de las columnas
		for ($a=0;$a<$this->cantidad_columnas;$a++){
			if ($this->info_cuadro_columna[$a]["pdf_propiedades"]!="") {
				$this->propiedades_pdf["cols"][$this->info_cuadro_columna[$a]["valor_sql"]] = parsear_propiedades($this->info_cuadro_columna[$a]["pdf_propiedades"]);
			}
		}
		return true;	
	}
	
	function obtener_pdf()
    {
		ini_set("max_execution_time",0);
		//Obtengo el ARRAY de propiedades de la TABLA, obtengo ARRAY de propiedades de cuadro y ARRAY de propiedades de las columnas
		if (!$this->procesar_pdf()) {
			$this->observar("info","No se han podido cargar los DATOS del PDF");
		}
	   	
		chdir($this->solicitud->hilo->obtener_path() . "/php/3ros/ezpdf");
		include("class.ezpdf.php");

       	//Busca propiedades basicas, sino las setea por default
		if (is_array($this->propiedades_pdf)){
			if (!array_key_exists("width",$this->propiedades_pdf)){
			    $this->propiedades_pdf["width"] = 800;
			}
			if (!array_key_exists("orientacion_papel",$this->propiedades_pdf)){
			    $this->propiedades_pdf["orientacion_papel"] = 'landscape';
			}
		}else{
			$this->propiedades_pdf = array("width"=>800,"orientacion_papel"=>'landscape');
		}
        $pdf =& new Cezpdf('a4',$this->propiedades_pdf["orientacion_papel"]);
        //Control de acceso al archivo
        $pdf->setEncryption('','',array('print'));
        $pdf->selectFont("./fonts/Courier.afm");

        //$pdf->addText( 20, 575, 14, "<b>" . $this->info_cuadro["titulo"] ."</b>");
        //Escribo el detalle de la fatura
        //$pdf->ezSetY(780);

		$pos_total_d = $pdf->ezTable($this->datos_pdf, null,$this->info["titulo"],$this->propiedades_pdf);
/*
        Escribo el numero
        $pdf->addText( $pos_numero["x"], $pos_numero["y"], 20, $id);
        //Escribo el nombre
        $pdf->addText( $pos_nombre["x"], $pos_nombre["y"], 10, "<b>" . $agente["nombre"] ."</b>" );

        
        //Imprimo el TOTAL
        $pdf->addText( $pos_total["x"], ($pos_total_d - 50), 12, "TOTAL: <b>" .$agente["total"] . "</b>");

        //Salto de PAGINA
        $actual++;
        if($actual < $total) $pdf->ezNewPage();

        //Imprimo salida
*/
        $pdf->ezStream();
    }

//--------------------
//------- XLS --------
//--------------------

    function obtener_xls()
    //Genera el cuadro como XLS
    {
        header("Content-type: application/vnd.ms-excel");
        //header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=consulta.xls");
        $filas = count($this->datos);
        echo "<TABLE border='1'>";
        //------------------------ Genero los titulos
        echo "<tr>\n";
        for ($a=0;$a<$this->cantidad_columnas;$a++)
        {
            if( !(trim($this->info_cuadro_columna[$a]["vinculo_indice"])!="") && ($this->info_cuadro_columna[$a]["mostrar_pdf"] == 1)){
                echo "<td>\n";
                echo $this->info_cuadro_columna[$a]["titulo"];
                echo "</td>\n";
            }
        }
        echo "</tr>\n";
        //----------------------- Genero las filas
        for ($f=0; $f< $filas; $f++)
        {
            echo "<tr>\n";
            for ($a=0;$a<$this->cantidad_columnas;$a++)
            {
                //----------> Comienzo una CELDA!!
                //*** 1) Recupero el VALOR
                if(isset($this->info_cuadro_columna[$a]["valor_sql"])  && $this->info_cuadro_columna[$a]["mostrar_xls"] == 1){
                    if (!is_null($this->datos[$f][$this->info_cuadro_columna[$a]["valor_sql"]])){
	                    $valor = $this->datos[$f][$this->info_cuadro_columna[$a]["valor_sql"]];    
                    }else{
						$valor = "";
					}
                    //Hay que formatear?
                    if(isset($this->info_cuadro_columna[$a]["valor_sql_formato"])){
                            $funcion = "formato_" . $this->info_cuadro_columna[$a]["valor_sql_formato"];
                        //Formateo el valor
                        $valor = $funcion($valor);
                    }
                }elseif(isset($this->info_cuadro_columna[$a]["valor_fijo"]) && $this->info_cuadro_columna[$a]["mostrar_xls"] == 1){
                    $valor = $this->info_cuadro_columna[$a]["valor_fijo"];
                }else{
                    $valor = null;
                }
                //*** 4) Genero el HTML
                if(!(trim($this->info_cuadro_columna[$a]["vinculo_indice"])!="") && (!is_null($valor))){
                    echo "<td>\n";
                    echo $valor;
                    echo "</td>\n";
                }
                //----------> Termino la CELDA!!
            }
            echo "</tr>\n";
        }
        //----------------------- Genero totales??
        echo "</table>\n";
    }
//------------------------------------------------------
/*
	function mostrar_desplegable()
	{
		$maximizar = recurso::imagen_pro("maximizar.gif", false);
		$minimizar = recurso::imagen_pro("minimizar.gif", false);
		echo "
			<SCRIPT type='text/javascript' language='JavaScript'>
				var toggle_mensaje_max = 'Mostrar el contenido del cuadro';
				var toggle_mensaje_min = 'Ocultar el contenido del cuadro';
				var toggle_mensaje = toggle_mensaje_max;
				
				function toggle_cuadro(id)
				{
					var cuadro = document.getElementById('cuadro_' + id);
					var boton = document.getElementById('toggle_' + id);					
					if (cuadro && boton)
					{
						var estado_actual = cuadro.style.display;
						if (estado_actual == 'none')
						{
							cuadro.style.display = '';
							boton.src = '$minimizar';
							toggle_mensaje = toggle_mensaje_min;
						}
						else
						{
							cuadro.style.display = 'none';
							boton.src = '$maximizar';
							toggle_mensaje = toggle_mensaje_max;							
						}
					}
				}
			</SCRIPT>
			<div align='center'><input type='image' src='$maximizar' style='cursor: hand;' id='toggle_{$this->id[1]}' 
				 onClick=\"javascript: toggle_cuadro('{$this->id[1]}');\" 
				 onMouseover='ddrivetip(toggle_mensaje)' onMouseout='hideddrivetip()'>
			</div>
			";	
	}


	function obtener_html()
	{
		.....
            echo "<tr><td>";
            //------------------------ CODIGO DEL DESPLEGABLE --------------------------/	
			$mostrar_desplegable = true;
			if ($mostrar_desplegable) {
				$this->mostrar_desplegable();
				echo "<TABLE width='100%' class='tabla-0' id='cuadro_{$this->id[1]}' style='display: none'>";
			} else {
	            echo "<TABLE width='100%' class='tabla-0'>";
			}
            //----------------------------------------------------------
            echo "<tr>\n";
		....
	}
*/

}
?>
