<?php
require_once("nucleo/browser/clases/objeto_cuadro.php"); 

class objeto_cuadro_tabla extends objeto_cuadro
/*
    @@acceso: publico
    @@desc: Esta clase implementa un listado ordenable y paginable
    *       para visualizar los logs de auditoria.
*/
{
    var $tabla;
//################################################################################
//###########################                         ############################
//###########################      INICIALIZACION     ############################
//###########################                         ############################
//################################################################################
        
    function objeto_cuadro_tabla($id)
/*
    @@acceso: constructor
    @@desc: 
*/
    {
        parent::objeto_cuadro($id);
    }
//--------------------------------------------------------------------------

	function cargar_definicion()
/*
 	@@acceso:
	@@desc: 
*/
	{
		global $db, $ADODB_FETCH_MODE, $cronometro;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
      
      $this->tabla = (isset($_SESSION['tabla_dinamica'])? $_SESSION['tabla_dinamica']: 'pg_tables');
      
		//$cronometro->marcar('basura',apex_nivel_nucleo);
		$tipo_de_carga="db";
		if($tipo_de_carga=="db"){
      
   		$pre_definicion["info"]["sql"] = "	SELECT	o.*,
   									c.editor_proyecto as		clase_editor_proyecto,
   									c.editor_item as			clase_editor_item,
   									c.archivo as				clase_archivo,
   									c.plan_dump_objeto as		clase_dump,
   									c.vinculos as 				clase_vinculos,
   									c.editor_item as			clase_editor,
   									d.fuente_datos as			fuente,
   									d.fuente_datos_motor as		fuente_motor,
   									d.host as					fuente_host,
   									d.usuario as				fuente_usuario,
   									d.clave as					fuente_clave,
   									d.base as					fuente_base,
   									d.link_instancia as		fuente_link_instancia,
   									oi.objeto as				objeto_existe_ayuda
   							FROM	apex_objeto o
   										LEFT OUTER JOIN apex_objeto_info oi 
   											ON (o.objeto = oi.objeto AND o.proyecto = oi.objeto_proyecto),
   									apex_fuente_datos d,
   									apex_clase c
   							WHERE	o.fuente_datos = d.fuente_datos
   							AND     o.fuente_datos_proyecto = d.proyecto
   							AND		o.clase_proyecto = c.proyecto
   							AND			o.clase = c.clase
   							AND		o.proyecto='".$this->id[0]."'
   							AND		o.objeto='".$this->id[1]."';";

         $rs = $db["instancia"][apex_db_con]->Execute($pre_definicion["info"]["sql"]);
         $temp = $rs->getArray();
         $this->info = $temp[0];
         $this->conectar_fuente();
         
         $definicion = array();
         $this->obtener_definicion_tabla($definicion);
         $this->obtener_definicion_columnas();
         
			foreach(array_keys($definicion) as $parte)
			{
            $this->definicion_partes[] = $parte;
            
            if ($parte != 'info_cuadro_columna')
            {
               $rs = $db["instancia"][apex_db_con]->Execute($definicion[$parte]["sql"]);
				
   				if((!$rs)){
   					monitor::evento("bug","Error cargando la DEFINICION del OBJETO [objeto: $parte] Error cargando la definicion del OBJETO '{$this->id[0]}, {$this->id[1]}'. ".$db["instancia"][apex_db_con]->ErrorMsg());
   				}
   				if($rs->EOF){
   					if($definicion[$parte]["estricto"]=="1"){
   						monitor::evento("bug","Error cargando la DEFINICION del OBJETO [objeto:$parte] '{$this->id[0]},{$this->id[1]}'. No hay registros.");
   					}else{
   						//El parametro no es estricto, lo inicializo como ARRAY vacio
   						$this->$parte = array();
   					}
   				}else{
   					$temp = $rs->getArray();
   					//Registro UNICO o GRUPO
   					if($definicion[$parte]["tipo"]=="1"){	
   						$this->$parte = $temp[0];
   					}else{
   						$this->$parte = $temp;
   					}
   				}
            }   
			}
		}else{
			//-- Cargo la DEFINICION el PHP autogenerado
			//ATENCION, un include_once no sirve para objetos ANIDADOS
			include( $this->exportacion_archivo );
			//ei_arbol( $definicion_objeto ,"DEFINICION");
			foreach(array_keys($definicion_objeto) as $parte){
				$this->$parte =  $definicion_objeto[$parte];
				$this->definicion_partes[] = $parte;
			}
		}
		//$cronometro->marcar('OBJETO: Cargar INFO basica',apex_nivel_objeto);
	}

   
	function obtener_definicion_tabla(&$definicion)
   //Tranforma la consulta a la base para recuperar la definicion de la tabla.
	{
      $campos = $this->comentario_campos($this->tabla);
      $consulta = "SELECT " . (isset($_SESSION['tabla_dinamica'])? '': '* ');
      foreach (array_keys($campos) as $orden)
      {
         $consulta .= $campos[$orden]['campo'] . ',';
      }
      $consulta = substr($consulta, 0, strlen($consulta) - 1);
      $consulta = trim($consulta) . " FROM " . $this->tabla;
      $subtitulo = $this->comentario_tabla($this->tabla);
      
		$definicion["info_cuadro"]["sql"] = 
                "SELECT titulo                     as titulo,
								'Tabla: $subtitulo'			as	subtitulo,
								'$consulta'						as	sql,
								columnas_clave					as	columnas_clave,
								archivos_callbacks			as	archivos_callbacks,
								ancho						   	as	ancho,
								ordenar							as	ordenar,
								exportar						   as	exportar_xls,
								exportar_rtf					as	exportar_pdf,
								paginar							as	paginar,
								tamano_pagina					as	tamano_pagina,
								eof_invisible					as	eof_invisible,
								eof_customizado				as	eof_customizado,
								pdf_respetar_paginacion		as	pdf_respetar_paginacion,
								pdf_propiedades				as	pdf_propiedades,
								asociacion_columnas			as	asociacion_columnas
					 FROM		apex_objeto_cuadro
					 WHERE	objeto_cuadro_proyecto='".$this->id[0]."'	
					 AND		objeto_cuadro='".$this->id[1]."';";
		$definicion["info_cuadro"]["estricto"]="1";
		$definicion["info_cuadro"]["tipo"]="1";
      return null;
	}
   
	function obtener_definicion_columnas()
   //Genera la definicion de los campos de la tabla.
	{
/* esto es lo que se reemplaza.  
		$definicion["info_cuadro_columna"]["sql"] = 
                "SELECT	c.orden	                  as orden,		
								c.titulo						   as titulo,		
								e.css							   as estilo,	 
								c.columna_ancho			   as ancho,	 
								c.valor_sql					   as valor_sql,		
								f.funcion					   as valor_sql_formato,	 
								c.valor_fijo				   as valor_fijo,	 
								c.valor_proceso_esp		   as valor_proceso,	
								c.vinculo_indice			   as vinculo_indice,	
								c.par_dimension_proyecto	as par_dimension_proyecto,	 
								c.par_dimension				as par_dimension,
								c.par_tabla						as par_tabla,		
								c.par_columna					as par_columna,
								c.no_ordenar					as no_ordenar,
								c.mostrar_xls					as	mostrar_xls,
								c.mostrar_pdf					as	mostrar_pdf,
								c.pdf_propiedades				as	pdf_propiedades,
								c.total							as total
					 FROM		apex_columna_estilo e,
								apex_objeto_cuadro_columna	c
								LEFT OUTER JOIN apex_columna_formato f	
								ON	f.columna_formato	= c.valor_sql_formato
					 WHERE	objeto_cuadro_proyecto = '".$this->id[0]."'
					 AND		objeto_cuadro = '".$this->id[1]."'
					 AND		c.columna_estilo = e.columna_estilo	
					 AND		( c.desabilitado != '1' OR c.desabilitado IS NULL )
					 ORDER BY orden;";
		$definicion["info_cuadro_columna"]["tipo"]="x";
		$definicion["info_cuadro_columna"]["estricto"]="1";
*/
      $campos = $this->comentario_campos($this->tabla);
      $desc_campos = array();
      foreach (array_keys($campos) as $orden)
      {
         $desc_campos[$orden - 1]['orden'] = $orden;
         $desc_campos[$orden - 1]['titulo'] = $campos[$orden]['com_campo'];
         $desc_campos[$orden - 1]['ancho'] = null; 
         $desc_campos[$orden - 1]['valor_sql'] = $campos[$orden]['campo'];
         $desc_campos[$orden - 1]['valor_fijo'] = null;
         $desc_campos[$orden - 1]['valor_proceso'] = null;
         $desc_campos[$orden - 1]['vinculo_indice'] = null;
         $desc_campos[$orden - 1]['par_dimension_proyecto'] = null;
         $desc_campos[$orden - 1]['par_dimension'] = null;
         $desc_campos[$orden - 1]['par_tabla'] = null;
         $desc_campos[$orden - 1]['par_columna'] = null;
         $desc_campos[$orden - 1]['no_ordenar'] = null;
         $desc_campos[$orden - 1]['mostrar_xls'] = null;
         $desc_campos[$orden - 1]['mostrar_pdf'] = null;
         $desc_campos[$orden - 1]['pdf_propiedades'] = null;
         $desc_campos[$orden - 1]['total'] = null;
         
         if (($campos[$orden]['tipo'] == 'date'))
         {
            $desc_campos[$orden - 1]['estilo'] = 'col-cen-s2';
            $desc_campos[$orden - 1]['valor_sql_formato'] = 'fecha';
         }
         elseif (($campos[$orden]['tipo'] == 'timestamp'))
         {
            $desc_campos[$orden - 1]['estilo'] = 'col-cen-s2';
            $desc_campos[$orden - 1]['valor_sql_formato'] = 'NULO';
            $desc_campos[$orden - 1]['valor_proceso'] = 'procesar_timestamp';
         }
         elseif(($campos[$orden]['tipo'] == 'bpchar') || 
                ($campos[$orden]['tipo'] == 'varchar') ||
                ($campos[$orden]['tipo'] == 'text'))
         {
            $desc_campos[$orden - 1]['estilo'] = 'col-tex-p1';
            $desc_campos[$orden - 1]['valor_sql_formato'] = 'NULO';
         }
         elseif($campos[$orden]['tipo'] == 'int2')
         {
            $desc_campos[$orden - 1]['estilo'] = 'col-cen-s2';
            $desc_campos[$orden - 1]['valor_sql_formato'] = 'checkbox';
         }
         elseif($campos[$orden]['tipo'] == 'int4')
         {
            $desc_campos[$orden - 1]['estilo'] = 'col-num-p1';
            $desc_campos[$orden - 1]['valor_sql_formato'] = 'NULO';
         }
         elseif($campos[$orden]['tipo'] == 'numeric')
         {
            $desc_campos[$orden - 1]['estilo'] = 'col-num-p1';
            $desc_campos[$orden - 1]['valor_sql_formato'] = 'decimal';
         }
         else
         {
            $desc_campos[$orden - 1]['estilo'] = 'col-tex-p1';
            $desc_campos[$orden - 1]['valor_sql_formato'] = 'NULO';
         }
      }
      $this->info_cuadro_columna = null;
      $this->info_cuadro_columna = $desc_campos;
   
      return null;
	}

   function buscar_tablas($prefijo = '')
   {
      global $db,$ADODB_FETCH_MODE;
      $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

	   $rs1 = $conexion->Execute("SELECT tablename FROM pg_tables
	                              WHERE tablename NOT LIKE 'pg_%'
	                              AND tablename NOT LIKE 'sql_%' " .
                                 ($prefijo == ''? '' : "AND tablename LIKE '$prefijo%'"));
      $rs = $db[$this->info["fuente"]][apex_db_con]->Execute($consulta);
      $retorno = array();
      while(! $rs->EOF)
      {
         array_push($retorno, $rs->fields['tablename']);
         $rs->MoveNext();
      }
      return $retorno;
   }
   
   function comentario_tabla($tabla = '')
   {
      global $db,$ADODB_FETCH_MODE;
      $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
   
      $consulta = "SELECT COALESCE(obj_description(c.oid, 'pg_class'), c.relname) as com_tabla
                   FROM pg_class c
                   WHERE c.relkind = 'r'
                   AND (c.relname='$tabla' OR c.relname = lower('$tabla'))";
      $rs = $db[$this->info["fuente"]][apex_db_con]->Execute($consulta);
      if(! $rs->EOF)
      {
         return $rs->fields['com_tabla'];
      }
      return '';
   }
   
   function comentario_campos($tabla = '', $campo = '')
   {
      global $db,$ADODB_FETCH_MODE;
      $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
   
      $consulta = "SELECT a.attname as campo, t.typname as tipo, a.attnum as orden, 
                         COALESCE(col_description(c.oid, a.attnum), a.attname) as com_campo
                   FROM pg_class c, pg_attribute a, pg_type t
                   WHERE relkind = 'r'
                   AND (c.relname='$tabla' OR c.relname = lower('$tabla')) " .
                   ($campo == ''? '': "AND a.attname = '$campo' ") . 
                  "AND a.attnum > 0
                   AND a.atttypid = t.oid
                   AND a.attrelid = c.oid
                   ORDER BY a.attnum";
      $rs = $db[$this->info["fuente"]][apex_db_con]->Execute($consulta);
      $retorno = array();
      while(! $rs->EOF)
      {
         $retorno[$rs->fields['orden']] = array();
         $retorno[$rs->fields['orden']]['campo'] = $rs->fields['campo'];
         $retorno[$rs->fields['orden']]['tipo'] = $rs->fields['tipo'];
         $retorno[$rs->fields['orden']]['com_campo'] = $rs->fields['com_campo'];                  
         $rs->MoveNext();
      }
      return $retorno;
   }

   function procesar_timestamp($f = '',$fecha = '')
   {
      $exitoso = ereg ("([0-9]{4})-([0-9]{2})-([0-9]{2})(.*)", $fecha, $resultado);
      if ($exitoso)
      {
          return("$resultado[3]/$resultado[2]/$resultado[1]$resultado[4]");
      }
      else
      {
          return '';
      }
   }

   
}
?>