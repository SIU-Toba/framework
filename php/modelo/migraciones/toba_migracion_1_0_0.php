<?php

class toba_migracion_1_0_0 extends toba_migracion
{
	//--------------------------------------------------------------
	//-------------------------- INSTANCIA --------------------------
	//--------------------------------------------------------------

	/**
	 *	Ahora el anterior proyecto 'toba' se lo conoce como proyecto 'toba_editor'
	 */
	function instancia__cambio_proyecto_editor()
	{
		$ini = $this->elemento->get_ini();		
		if ($ini->existe_entrada('proyectos')) {
			$actuales = $ini->get_datos_entrada('proyectos');
			$actuales = str_replace('toba ', 'toba_editor ', $actuales); 
			$actuales = str_replace('toba,', 'toba_editor,', $actuales); 
			$actuales = preg_replace('/toba$/', 'toba_editor', $actuales);
			$ini->set_datos_entrada('proyectos', $actuales);
			$ini->guardar();
		}
	}	
	
	function instancia__cambio_estructura()
	{
		//--- Cambios a efs
		$sql[] = "ALTER TABLE apex_elemento_formulario	   ADD COLUMN 	obsoleto							smallint";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN 	estado_defecto 						varchar(255)";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN 	solo_lectura 						smallint";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN 	carga_metodo						varchar(100)";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN 	carga_clase 						varchar(100)";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN 	carga_include 						varchar(255)";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN 	carga_col_clave 					varchar(100)";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN 	carga_col_desc 						varchar(100)";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	carga_sql							varchar";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	carga_fuente						varchar(30)";	
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	carga_lista							varchar(255)";	
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	carga_maestros						varchar(255)";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	carga_cascada_relaj					smallint";	
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	carga_no_seteado					varchar(100)";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	edit_tamano							smallint";		
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	edit_maximo							smallint";		
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	edit_mascara						varchar(100)";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	edit_unidad							varchar(255)";	
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	edit_rango							varchar(100)";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	edit_filas							smallint";	
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	edit_columnas						smallint";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	edit_wrap							varchar(20)";	
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	edit_resaltar						smallint";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	edit_ajustable						smallint";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	edit_confirmar_clave				smallint";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	popup_item							varchar(60)";	
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	popup_proyecto						varchar(15)";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	popup_editable						smallint";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	popup_ventana						varchar(50)";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	fieldset_fin						smallint";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	check_valor_si						varchar(40)";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	check_valor_no						varchar(40)";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	check_desc_si						varchar(100)";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	check_desc_no						varchar(100)";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	fijo_sin_estado						smallint";	
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	editor_ancho						varchar(10)";	
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	editor_alto							varchar(10)";		
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	editor_botonera						varchar(50)";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	selec_cant_minima					smallint";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	selec_cant_maxima					smallint";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	selec_utilidades					smallint";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	selec_tamano						smallint";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	selec_serializar					smallint";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN	upload_extensiones					varchar(256)";
		
		//-- El tipo de solicitud no es mas obligatorio
		$sql[] = "ALTER TABLE apex_item ALTER COLUMN solicitud_tipo DROP NOT NULL;";
		
		//--- La fuente de datos no es mas obligatoria
		$sql[] = "ALTER TABLE apex_objeto DROP COLUMN fuente_datos_proyecto";
		$sql[] = "ALTER TABLE apex_objeto DROP COLUMN fuente_datos";		
		$sql[] = "ALTER TABLE apex_objeto ADD COLUMN fuente_datos_proyecto	varchar(20)";
		$sql[] = "ALTER TABLE apex_objeto ADD COLUMN fuente_datos				varchar(20)";
		
		//--- Cambios al proyecto
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN	requiere_validacion				smallint		";
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN	usuario_anonimo					varchar(15)		";
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN	validacion_intentos				smallint		";
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN	validacion_intentos_min			smallint		";
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN	validacion_debug				smallint		";
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN	sesion_tiempo_no_interac_min	smallint		";
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN	sesion_tiempo_maximo_min		smallint		";
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN	sesion_subclase					varchar(40)		";
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN	sesion_subclase_archivo			varchar(255)	";
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN	usuario_subclase				varchar(40)		";
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN	usuario_subclase_archivo		varchar(255)	";
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN	encriptar_qs					smallint		";
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN	registrar_solicitud				varchar(1)		";
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN	registrar_cronometro			varchar(1)		";
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN	item_inicio_sesion      		varchar(60)		";
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN	item_pre_sesion		          	varchar(60)		";
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN	log_archivo						smallint		";
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN	log_archivo_nivel				smallint		";
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN	fuente_datos					varchar(20)		";	
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN	version							varchar(20)		";	
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN	version_fecha					date			";	
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN	version_detalle					varchar(255)	";	
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN	version_link					varchar(60)		";	
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN	usuario_anonimo_desc			varchar(60)		";
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN	usuario_anonimo_grupos_acc		varchar(255)	";
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN	contexto_ejecucion_subclase		varchar(255)	";
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN	contexto_ejecucion_subclase_archivo		varchar(255)	";
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN	item_set_sesion		varchar(255)	";

		//--- Cambios a la zona
		$sql[] = "ALTER TABLE apex_item_zona ADD COLUMN	consulta_archivo				varchar(255)	";
		$sql[] = "ALTER TABLE apex_item_zona ADD COLUMN	consulta_clase					varchar(60)		";
		$sql[] = "ALTER TABLE apex_item_zona ADD COLUMN	consulta_metodo					varchar(60)		";	
		
		//--- Cambios al item
		$sql[] = "ALTER TABLE apex_item ADD COLUMN	redirecciona					smallint		";			

		//--- Cambios a los eventos
		$sql[] = "ALTER TABLE apex_objeto_eventos ADD COLUMN	defecto					smallint		";
		
		//--- Nueva tabla
		$sql [] = '
				CREATE TABLE apex_admin_param_previsualizazion	(
					proyecto							varchar(15)		NOT NULL, 
					usuario								varchar(60)		NOT NULL,
					grupo_acceso						varchar(20)		NOT NULL,
					punto_acceso						varchar(100)	NOT NULL,
				  CONSTRAINT "apex_admin_param_prev_pk" PRIMARY KEY("proyecto", "usuario"),
				  CONSTRAINT "apex_admin_param_prev_fk_proy" 	FOREIGN KEY ("proyecto", "usuario")
				    											REFERENCES "apex_usuario_proyecto" ("proyecto", "usuario") ON	DELETE CASCADE ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE
				);
		';
		
		//-- Cambio pantalla del ci
		$sql[] = "ALTER TABLE apex_objeto_ci_pantalla ADD COLUMN subclase			varchar(80)";
		$sql[] = "ALTER TABLE apex_objeto_ci_pantalla ADD COLUMN subclase_archivo	varchar(80)";
		
		//-- Cambio en las solicitudes
		$sql[] = "ALTER TABLE apex_solicitud_cronometro 	ADD COLUMN proyecto					varchar(15)";
		$sql[] = "ALTER TABLE apex_solicitud_observacion 	ADD COLUMN proyecto					varchar(15)";
		$sql[] = "ALTER TABLE apex_solicitud_consola 		ADD COLUMN proyecto					varchar(15)";
		$sql[] = "ALTER TABLE apex_solicitud_browser 		ADD COLUMN solicitud_proyecto		varchar(15)";
		$sql[] = "ALTER TABLE apex_solicitud_browser 		ADD COLUMN proyecto					varchar(15)";
		
		$this->elemento->get_db()->ejecutar($sql);
	
	}
	
	function instancia__cambios_metadatos_nucleo()
	{
		$sql[]  = "INSERT INTO apex_elemento_formulario (elemento_formulario, padre, descripcion, parametros, proyecto, exclusivo_toba) VALUES ('ef_editable_textarea', 'ef_editable', 'Campo editable de varias líneas de alto.', NULL, 'toba', NULL)";
		$this->elemento->get_db()->ejecutar($sql);		
	}

	//--------------------------------------------------------------
	//-------------------------- PROYECTO --------------------------
	//--------------------------------------------------------------
	
	/**
	 * La estructura interna para separar los parámetros de los ef cambia así es posible contener
	 * los caracteres ';' y ':' entre ellos.
	 */
	function proyecto__parametros_efs()
	{
		$cant = 0;
		$sql = "
			SELECT 
				objeto_ei_formulario_proyecto,
				objeto_ei_formulario,
				objeto_ei_formulario_fila,
				inicializacion
			FROM
				apex_objeto_ei_formulario_ef
			WHERE 
				objeto_ei_formulario_proyecto='{$this->elemento->get_id()}'
		";
		$rs = $this->elemento->get_db()->consultar($sql);
		foreach ($rs as $ef) {
			$param = parsear_propiedades($ef['inicializacion'], '');
			if (is_array($param)) {
				$prop = addslashes(empaquetar_propiedades($param,'_'));
				$sql = "
					UPDATE apex_objeto_ei_formulario_ef SET inicializacion = '$prop'
					WHERE
							objeto_ei_formulario_proyecto = '{$ef['objeto_ei_formulario_proyecto']}' 
						AND	objeto_ei_formulario = '{$ef['objeto_ei_formulario']}'  
						AND objeto_ei_formulario_fila = '{$ef['objeto_ei_formulario_fila']}'
					";
				$cant += $this->elemento->get_db()->ejecutar($sql);
			}
		}
		return $cant;		
	}
	
	/**
	 * Los parametros del combo lista cambiaron, para brindar mayor ortogonalidad a la definición
	 * Ahora los valores en la lista se separan siempre por coma y los clave-valor (si clave != valor) se separan por /
	 *
	 */
	function proyecto__parametros_combo_lista_c()
	{
		$cant = 0;
		$sql = "
			SELECT 
				objeto_ei_formulario_proyecto,
				objeto_ei_formulario,
				objeto_ei_formulario_fila,
				inicializacion
			FROM
				apex_objeto_ei_formulario_ef
			WHERE 
				elemento_formulario = 'ef_combo_lista_c' AND
				objeto_ei_formulario_proyecto='{$this->elemento->get_id()}'
		";
		$rs = $this->elemento->get_db()->consultar($sql);
		foreach ($rs as $ef) {
			$param = parsear_propiedades($ef['inicializacion'],'_');
			if (isset($param['lista'])) {
				$lista = str_replace("/", "^", $param['lista']);
				$lista = str_replace("," ,"/", $lista);
				$param['lista'] = str_replace("^", ",", $lista);
				$prop = addslashes(empaquetar_propiedades($param,'_'));
				$sql = "
					UPDATE apex_objeto_ei_formulario_ef SET inicializacion = '$prop'
					WHERE
							objeto_ei_formulario_proyecto = '{$ef['objeto_ei_formulario_proyecto']}' 
						AND	objeto_ei_formulario = '{$ef['objeto_ei_formulario']}'  
						AND objeto_ei_formulario_fila = '{$ef['objeto_ei_formulario_fila']}'
					";
				$cant += $this->elemento->get_db()->ejecutar($sql);
			}
		}
		return $cant;		
	}
	
	/**
	 * El parametro 'valores' del multiseleccion pasa a llamarse 'lista' para unificarlo con los combos
	 */
	function proyecto__parametros_multiseleccion()
	{
		$cant = 0;
		$sql = "
			SELECT 
				objeto_ei_formulario_proyecto,
				objeto_ei_formulario,
				objeto_ei_formulario_fila,
				inicializacion
			FROM
				apex_objeto_ei_formulario_ef
			WHERE 
					elemento_formulario IN ('ef_multi_seleccion', 'ef_multi_seleccion_lista', 'ef_multi_seleccion_check')
				AND objeto_ei_formulario_proyecto='{$this->elemento->get_id()}'
		";
		$rs = $this->elemento->get_db()->consultar($sql);
		foreach ($rs as $ef) {
			$param = parsear_propiedades($ef['inicializacion'],'_');
			if (isset($param['valores'])) {
				$param['lista'] = $param['valores'];
				unset($param['valores']);
				$prop = addslashes(empaquetar_propiedades($param,'_'));
				$sql = "
					UPDATE apex_objeto_ei_formulario_ef SET inicializacion = '$prop'
					WHERE
							objeto_ei_formulario_proyecto = '{$ef['objeto_ei_formulario_proyecto']}' 
						AND	objeto_ei_formulario = '{$ef['objeto_ei_formulario']}'  
						AND objeto_ei_formulario_fila = '{$ef['objeto_ei_formulario_fila']}'
					";
				$cant += $this->elemento->get_db()->ejecutar($sql);
			}
		}
		return $cant;		
	}
	
	/**
	 * Los parametros de inicializacion de la ventana pasan de ser 3 e implicitos a n y explicitos,
	 * asi se pueden definir cualquiera y ademas pueden ser modificados como los parametros de los 
	 * eventos
	 */
	function proyecto__parametros_ef_popup()
	{
		$cant = 0;
		$sql = "
			SELECT 
				objeto_ei_formulario_proyecto,
				objeto_ei_formulario,
				objeto_ei_formulario_fila,
				inicializacion
			FROM
				apex_objeto_ei_formulario_ef
			WHERE 
				elemento_formulario = 'ef_popup' AND
				objeto_ei_formulario_proyecto='{$this->elemento->get_id()}'
		";
		$rs = $this->elemento->get_db()->consultar($sql);
		foreach ($rs as $ef) {
			$param = parsear_propiedades($ef['inicializacion'],'_');
			if (isset($param['ventana'])) {
				$ventana = explode(',', $param['ventana']);
				if (isset($ventana[0])) {
					$ventana[0] = "width: {$ventana[0]}";	
				}
				if (isset($ventana[1])) {
					$ventana[1] = "height: {$ventana[1]}";	
				}
				if (isset($ventana[2])) {
					$ventana[2] = "scroll: {$ventana[2]}";	
				}
				$param['ventana'] = implode(',', $ventana);
				$prop = addslashes(empaquetar_propiedades($param,'_'));
				$sql = "
					UPDATE apex_objeto_ei_formulario_ef SET inicializacion = '$prop'
					WHERE
							objeto_ei_formulario_proyecto = '{$ef['objeto_ei_formulario_proyecto']}' 
						AND	objeto_ei_formulario = '{$ef['objeto_ei_formulario']}'  
						AND objeto_ei_formulario_fila = '{$ef['objeto_ei_formulario_fila']}'
					";
				$cant += $this->elemento->get_db()->ejecutar($sql);
			}
		}
		return $cant;		
	}
	
	/**
	 * Las clases de combo se unificaron en una unica
	 */
	function proyecto__arbol_herencia_efs()
	{
		$sql = "
			UPDATE 
				apex_objeto_ei_formulario_ef
			SET
				elemento_formulario = 'ef_combo'
			WHERE 
				objeto_ei_formulario_proyecto='{$this->elemento->get_id()}' AND
				elemento_formulario IN ('ef_combo_dao', 'ef_combo_db', 'ef_combo_db_proyecto', 
										'ef_combo_lista', 'ef_combo_lista_c')
		";		
		$cant = $this->elemento->get_db()->ejecutar($sql);
		return $cant;		
	}
	
	/**
	 * El ef_editable_multilinea pasa a ser ef_editable_textarea
	 */
	function proyecto__ef_textarea()
	{
		$sql = "
			UPDATE 
				apex_objeto_ei_formulario_ef
			SET
				elemento_formulario = 'ef_editable_textarea'
			WHERE 
				objeto_ei_formulario_proyecto='{$this->elemento->get_id()}' AND
				elemento_formulario = 'ef_editable_multilinea'
		";		
		$cant = $this->elemento->get_db()->ejecutar($sql);	
		return $cant;	
	}
	
	/**
	 * El editable numero migra su parametro CIFRAS por TAMANO, que es en realidad lo que es!
	 */
	function proyecto__ef_parametros_editable_numero()
	{
		$cant = 0;
		$sql = "
			SELECT 
				objeto_ei_formulario_proyecto,
				objeto_ei_formulario,
				objeto_ei_formulario_fila,
				inicializacion
			FROM
				apex_objeto_ei_formulario_ef
			WHERE 
					elemento_formulario IN ('ef_editable_numero', 'ef_editable_numero_porcentaje', 'ef_editable_moneda')
				AND objeto_ei_formulario_proyecto='{$this->elemento->get_id()}'
		";
		$rs = $this->elemento->get_db()->consultar($sql);
		foreach ($rs as $ef) {
			$param = parsear_propiedades($ef['inicializacion'],'_');
			if (isset($param['cifras'])) {
				$param['tamano'] = $param['cifras'];
				unset($param['cifras']);
				$prop = addslashes(empaquetar_propiedades($param,'_'));
				$sql = "
					UPDATE apex_objeto_ei_formulario_ef SET inicializacion = '$prop'
					WHERE
							objeto_ei_formulario_proyecto = '{$ef['objeto_ei_formulario_proyecto']}' 
						AND	objeto_ei_formulario = '{$ef['objeto_ei_formulario']}'  
						AND objeto_ei_formulario_fila = '{$ef['objeto_ei_formulario_fila']}'
					";
				$rs = $this->elemento->get_db()->ejecutar($sql);
				$cant++;
			}
		}
		return $cant;
	}

	function proyecto__ef_normalizacion_parametros()
	{
		$cant = 0;
		$correlacion = array(
			'estado' =>'estado_defecto',
			'predeterminado' => 'estado_defecto',
			'solo_lectura' => 'solo_lectura',
			'dao' =>'carga_metodo',
			'clase' =>'carga_clase',
			'include' =>'carga_include',
			'clave' =>'carga_col_clave',
			'sql' =>'carga_sql',
			'fuente' =>'carga_fuente',
			'lista' =>'carga_lista',
			'dependencias' =>'carga_maestros',
			'cascada_relajada' =>'carga_cascada_relaj',
			'no_seteado' =>'carga_no_seteado',
			'tamano' =>'edit_tamano',
			'cifras' =>'edit_tamano',
			'maximo' =>'edit_maximo',
			'mascara' =>'edit_mascara',
			'unidad' =>'edit_unidad',
			'rango' =>'edit_rango',
			'filas' =>'edit_filas',
			'columnas' =>'edit_columnas',
			'wrap' =>'edit_wrap',
			'resaltar' =>'edit_resaltar',
			'ajustable' =>'edit_ajustable',
			'ventana' =>'popup_ventana',
			'editable' =>'popup_editable',
			'valor_no_seteado' =>'check_valor_no',
			'valor_info' =>'check_desc_si',
			'valor_info_no_seteado' =>'check_desc_no',
			'sin_datos' =>'fijo_sin_estado',
			'ancho' =>'editor_ancho',
			'alto' =>'editor_alto',
			'botonera' =>'editor_botonera',
			'cant_minima'=>'selec_cant_minima',
			'cant_maxima'=>'selec_cant_maxima',
			'mostrar_utilidades'=>'selec_utilidades',
			'tamanio'=>'selec_tamano',
			'extensiones_validas' => 'upload_extensiones'
			//'item_destino' Migrar manualmente
			//'valor':carga_col_desc o check_valor_si
		);
		
		$sql = "
			SELECT 
				objeto_ei_formulario_proyecto,
				objeto_ei_formulario,
				objeto_ei_formulario_fila,
				elemento_formulario,
				identificador,
				inicializacion
			FROM
				apex_objeto_ei_formulario_ef
			WHERE 
				objeto_ei_formulario_proyecto='{$this->elemento->get_id()}'
		";
		$rs = $this->elemento->get_db()->consultar($sql);
		foreach ($rs as $ef) {
			$param = parsear_propiedades($ef['inicializacion'],'_');
			$nuevos = array();
			foreach ($param as $clave => $valor) {
				if (isset($correlacion[$clave])) {
					$nuevos[$correlacion[$clave]] = $valor;
				} elseif ($clave == 'tipo') {
					if (!is_numeric($valor)) {
						$valor = ($valor == 'i') ? 0 : 1;
					}
					$nuevos['fieldset_fin'] = $valor;
				} elseif ($clave == 'valor') {
					if ($ef['elemento_formulario'] == 'ef_checkbox') {
						$nuevos['check_valor_si'] = $valor;
					} else {
						$nuevos['carga_col_desc'] = $valor;
					}
				} elseif ($clave == 'item_destino') {
					$partes = explode(',', $valor);
					$p_item = $partes[0];
					if (count($partes)==2) {
						$p_proyecto = $partes[1];
					} else {
						$p_proyecto = $this->elemento->get_id();
					}
					$nuevos['popup_item'] = $p_item;
					$nuevos['popup_proyecto'] = $p_proyecto;
				} else  {
					$msg = "El parametro '$clave' de los EF no tiene correlacion en la migracion.";
					$extendido = "Se decarta el valor '$valor' del parámetro '$clave' en el ef '{$ef['identificador']}' del formulario".
								" '{$ef['objeto_ei_formulario']}'";
					toba_logger::instancia()->warning($extendido);
					$this->manejador_interface->mensaje("$msg (Ver log para más detalles)\n");	
				}
			}
			if (! empty($nuevos)) {
				$sql = "
					UPDATE apex_objeto_ei_formulario_ef 
					SET 
						inicializacion = NULL,
				";
				foreach($nuevos as $id => $valor) {
					$valor = addslashes($valor);
					$sql .= "$id = '$valor', ";
				}
				$sql = substr($sql, 0, -2);
				$sql .="	WHERE
							objeto_ei_formulario_proyecto = '{$ef['objeto_ei_formulario_proyecto']}' 
						AND	objeto_ei_formulario = '{$ef['objeto_ei_formulario']}'  
						AND objeto_ei_formulario_fila = '{$ef['objeto_ei_formulario_fila']}'
					";
				//echo $sql."\n";
				$rs = $this->elemento->get_db()->ejecutar($sql);
				$cant++;
			}
		}	
		
		
		//--- Pone las inicializaciones en NULL para no volver a migrarlas sin querer
		$sql =  "UPDATE apex_objeto_ei_formulario_ef SET inicializacion = NULL
					WHERE objeto_ei_formulario_proyecto='{$this->elemento->get_id()}'";
		$this->elemento->get_db()->ejecutar($sql);
		return $cant;
	}
	
	function proyecto__ef_clave_nuevo_parametro()
	{
		$sql = " 
			UPDATE apex_objeto_ei_formulario_ef SET edit_confirmar_clave=1
			WHERE
					elemento_formulario IN ('ef_editable_clave')
				AND objeto_ei_formulario_proyecto='{$this->elemento->get_id()}'
		";	
		$cant = $this->elemento->get_db()->ejecutar($sql);
		return $cant;
	}


	/*
		Eliminar requires a toba, ahora se carga todo con autoload
	*/
	function proyecto__eliminar_includes()
	{
		$editor = new toba_editor_archivos();
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/lib/asercion.php['\"]\)\s*;|"						,"");          
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/lib/cache_db.php['\"]\)\s*;|"              			,"");               
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/lib/editor_archivos.php['\"]\)\s*;|"       			,"");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/lib/encriptador.php['\"]\)\s*;|"           			,"");      
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/lib/ini.php['\"]\)\s*;|"                   			,"");          
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/lib/manejador_archivos.php['\"]\)\s*;|"	   			,"");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/lib/parseo.php['\"]\)\s*;|"                			,"");   
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/lib/sincronizador_archivos.php['\"]\)\s*;|"			,"");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/lib/sql.php['\"]\)\s*;|"                   			,"");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/lib/texto.php['\"]\)\s*;|"                 			,"");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/lib/varios.php['\"]\)\s*;|"							,"");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/lib/reflexion/toba_archivo_php.php['\"]\)\s*;|"		,"");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/lib/reflexion/clase_datos.php['\"]\)\s*;|" 			,"");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/lib/reflexion/clase_php.php['\"]\)\s*;|"   			,"");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/lib/refleccion.php['\"]\)\s*;|"              			,"");                     
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/lib/db.php['\"]\)\s*;|"                    			,"");                     
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/lib/salidas/html_impr.php['\"]\)\s*;|","");                     
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/componentes/info/interfaces.php['\"]\)\s*;|","");                     
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/browser/zona/zona.php['\"]\)\s*;|"					,"");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/browser/clases/objeto.php['\"]\)\s*;|"               ,"");		
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/browser/clases/objeto_ci.php['\"]\)\s*;|"            ,"");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/browser/clases/objeto_ci_abm.php['\"]\)\s*;|"          ,"");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/browser/clases/objeto_ci_me_tab.php['\"]\)\s*;|"          ,"");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/browser/clases/objeto_ei.php['\"]\)\s*;|"              ,"");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/browser/clases/objeto_ei_arbol.php['\"]\)\s*;|"        ,"");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/browser/clases/objeto_ei_archivos.php['\"]\)\s*;|"     ,"");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/browser/clases/objeto_ei_calendario.php['\"]\)\s*;|"   ,"");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/browser/clases/objeto_ei_cuadro.php['\"]\)\s*;|"       ,"");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/browser/clases/objeto_ei_filtro.php['\"]\)\s*;|"       ,"");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/browser/clases/objeto_ei_formulario.php['\"]\)\s*;|"   ,"");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/browser/clases/objeto_ei_formulario_ml.php['\"]\)\s*;|","");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/browser/clases/objeto_cuadro.php['\"]\)\s*;|"          ,"");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/browser/clases/objeto_cuadro_reg.php['\"]\)\s*;|"      ,"");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/browser/clases/objeto_filtro.php['\"]\)\s*;|"          ,"");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/browser/clases/objeto_grafico.php['\"]\)\s*;|"         ,"");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/browser/clases/objeto_hoja.php['\"]\)\s*;|"            ,"");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/browser/clases/objeto_html.php['\"]\)\s*;|"            ,"");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/browser/clases/objeto_lista.php['\"]\)\s*;|"           ,"");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/persistencia/ap_tabla_db.php['\"]\)\s*;|" ,"");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/negocio/objeto_cn_t.php['\"]\)\s*;|","");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/browser/subclases/ci_cn.php['\"]\)\s*;|","");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/negocio/objeto_cn_buffer.php['\"]\)\s*;|","");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/negocio/objeto_cn_ent_pd.php['\"]\)\s*;|","");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/negocio/objeto_cn_ent_se.php['\"]\)\s*;|","");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/negocio/objeto_cn_ent.php['\"]\)\s*;|","");
		$editor->agregar_sustitucion("|require_once\(['\"]nucleo/negocio/objeto_cn.php['\"]\)\s*;|","");
		$archivos = toba_manejador_archivos::get_archivos_directorio( $this->elemento->get_dir(), '|.php|', true);
		$editor->procesar_archivos($archivos);
	}

	/*
		Modificar los nombres de las clases
	*/
	function proyecto__cambiar_nombres_clases_componentes()
	{
		$editor = new toba_editor_archivos();
		$editor->agregar_sustitucion("|extends\s*objeto_ci_me_tab|","extends toba_ci");
		$editor->agregar_sustitucion("|extends\s*objeto_ci|","extends toba_ci");
		$editor->agregar_sustitucion("|extends\s*ci_cn|","extends toba_ci");
		$editor->agregar_sustitucion("|extends\s*objeto_cn_buffer|","extends toba_cn");
		$editor->agregar_sustitucion("|extends\s*objeto_cn_ent_pd|","extends toba_cn");
		$editor->agregar_sustitucion("|extends\s*objeto_cn_ent_se|","extends toba_cn");
		$editor->agregar_sustitucion("|extends\s*objeto_cn_ent|","extends toba_cn");
		$editor->agregar_sustitucion("|extends\s*objeto_cn_t|","extends toba_cn");
		$editor->agregar_sustitucion("|extends\s*objeto_cn|","extends toba_cn");
		$editor->agregar_sustitucion("|extends\s*objeto_ei_arbol|","extends toba_ei_arbol");
		$editor->agregar_sustitucion("|extends\s*objeto_ei_archivos|","extends toba_ei_archivos");
		$editor->agregar_sustitucion("|extends\s*objeto_ei_calendario|","extends toba_ei_calendario");
		$editor->agregar_sustitucion("|extends\s*objeto_ei_cuadro|","extends toba_ei_cuadro");
		$editor->agregar_sustitucion("|extends\s*objeto_ei_filtro|","extends toba_ei_filtro");
		$editor->agregar_sustitucion("|extends\s*objeto_ei_formulario|","extends toba_ei_formulario");
		$editor->agregar_sustitucion("|extends\s*objeto_ei_formulario_ml|","extends toba_ei_formulario_ml");
		$editor->agregar_sustitucion("|extends\s*html_impr|","extends toba_impr_html");
		$editor->agregar_sustitucion("|implements\s*recorrible_como_arbol|","implements toba_nodo_arbol");
		$archivos = toba_manejador_archivos::get_archivos_directorio( $this->elemento->get_dir(), '|.php|', true);
		$editor->procesar_archivos($archivos);
	}

	/**
	 * En toba 1.0 hay una unica plantilla de estilos: toba
	 */
	function proyecto__migracion_estilos()
	{
		$sql = "
			UPDATE apex_proyecto SET estilo='toba' WHERE proyecto='{$this->elemento->get_id()}'
		";
		$cant = $this->elemento->get_db()->ejecutar($sql);	
		return $cant;	
	}
	
	/**
	 * La clase lista-col-titulo paso a ser ei-cuadro-col-tit
	 */
	function proyecto__migracion_css_cuadro()
	{
		$sql = " 
			UPDATE apex_objeto_ei_cuadro_columna
			SET 
				estilo_titulo = 'ei-cuadro-col-tit'
			WHERE
					objeto_cuadro_proyecto = '{$this->elemento->get_id()}'
				AND	estilo_titulo = 'lista-col-titulo'
		";
		$cant = $this->elemento->get_db()->ejecutar($sql);	
		return $cant;	
	}
	
	/**
	 * Los estilos de los botones de los eventos cambiaron
	 */
	function proyecto__migracion_css_botones_evt()
	{
		$cant = 0;		
		$sql = " 
			UPDATE apex_objeto_eventos
			SET 
				estilo = 'ei-boton'
			WHERE
					proyecto = '{$this->elemento->get_id()}'
				AND	estilo = 'abm-input'
		";
		$cant += $this->elemento->get_db()->ejecutar($sql);	
		
		//--- BAJA
		$sql = " 
			UPDATE apex_objeto_eventos
			SET 
				estilo = 'ei-boton-baja'
			WHERE
					proyecto = '{$this->elemento->get_id()}'
				AND	estilo = 'abm-input-eliminar'
		";
		$cant += $this->elemento->get_db()->ejecutar($sql);		
		return $cant;
	}
	
	function proyecto__cambios_tipo_pagina()
	{
		$cant = 0;
		//--Consumidor_html, lista_admin y com_js migran a vacio
		$sql = "UPDATE apex_item
				SET 
					pagina_tipo = 'vacio'
				WHERE 
						proyecto = '{$this->elemento->get_id()}'
					AND	pagina_tipo IN ('com_js', 'consumidor_html', 'lista_admin')
			";
		$cant+= $this->elemento->get_db()->ejecutar($sql);
		
		//--Popup pasa a ef_popup		
		$sql = "UPDATE apex_item
				SET 
					pagina_tipo = 'popup_ef'
				WHERE 
						proyecto = '{$this->elemento->get_id()}'
					AND	pagina_tipo IN ('popup')
			";
		$cant+= $this->elemento->get_db()->ejecutar($sql);
		return $cant;
	}

	function proyecto__eliminar_tablas_objetos_obsoletos()
	{
		$sql = array();
		$sql[] = "DELETE FROM apex_objeto_filtro			 WHERE objeto_filtro_proyecto = '{$this->elemento->get_id()}'";
		$sql[] = "DELETE FROM apex_objeto_ut_formulario_ef 	 WHERE objeto_ut_formulario_proyecto = '{$this->elemento->get_id()}'";
		$sql[] = "DELETE FROM apex_objeto_hoja 				 WHERE objeto_hoja_proyecto = '{$this->elemento->get_id()}'";
		$sql[] = "DELETE FROM apex_objeto_hoja_directiva	 WHERE objeto_hoja_proyecto = '{$this->elemento->get_id()}'";
		$sql[] = "DELETE FROM apex_objeto_html				 WHERE objeto_html_proyecto = '{$this->elemento->get_id()}'";
		return $this->elemento->get_db()->ejecutar($sql);				
	}		
	
	function proyecto__eliminar_objetos_obsoletos()
	{
		$clases_obsoletas[] = 'objeto_cuadro';
		$clases_obsoletas[] = 'objeto_cuadro_reg';
		$clases_obsoletas[] = 'objeto_filtro';
		$clases_obsoletas[] = 'objeto_hoja';
		$clases_obsoletas[] = 'objeto_mt_abms';
		$clases_obsoletas[] = 'objeto_html';
		$clases_obsoletas[] = 'objeto_lista';
		$clases_obsoletas[] = 'objeto_mt';
		$clases_obsoletas[] = 'objeto_mt_mds';
		$clases_obsoletas[] = 'objeto_mt_s';
		$clases_obsoletas[] = 'objeto_ut_formulario';
		$clases = implode("','",$clases_obsoletas);
		
		$sql = "SELECT objeto FROM apex_objeto WHERE
						proyecto = '{$this->elemento->get_id()}' AND 
						clase IN ('$clases');";
		$rs = $this->elemento->get_db()->consultar($sql);
		$cant = 0;
		foreach ($rs as $obj) {
			$sql = array();
			$id_obj = $obj['objeto'];
			//--- Borra las tablas secundarias comunes de estos objetos
			$sql[] = "DELETE FROM apex_objeto_ut_formulario WHERE 
								objeto_ut_formulario = '$id_obj'
							AND objeto_ut_formulario_proyecto = '{$this->elemento->get_id()}'";					
			
			$sql[] = "DELETE FROM apex_objeto_cuadro WHERE 
								objeto_cuadro = '$id_obj'
							AND objeto_cuadro_proyecto = '{$this->elemento->get_id()}'";
			
			$sql[] = "DELETE FROM apex_objeto_lista WHERE 
								objeto_lista = '$id_obj'
							AND objeto_lista_proyecto = '{$this->elemento->get_id()}'";			
						
			
			//--- Borra los vinculos entrantes y salientes de estos objetos
			$sql[] = "DELETE FROM apex_vinculo WHERE 
							(origen_objeto_proyecto= '{$this->elemento->get_id()}' AND origen_objeto= '$id_obj')
						OR	(destino_objeto_proyecto= '{$this->elemento->get_id()}' AND destino_objeto= '$id_obj')";			
			
			//--- Borra los objetos y sus dependencias
			$sql[] = "DELETE FROM apex_objeto WHERE objeto = '$id_obj' AND proyecto = '{$this->elemento->get_id()}'";
			$sql[] = "DELETE FROM apex_objeto_dependencias WHERE 
								(objeto_proveedor = '$id_obj' 
							OR	objeto_consumidor = '$id_obj')
							AND proyecto = '{$this->elemento->get_id()}'";
			
	

			$cant += $this->elemento->get_db()->ejecutar($sql);
		}
		return $cant;
	}	

	
	function proyecto__migrar_solicitud_browser()
	{
		$sql = "UPDATE apex_item SET solicitud_tipo = 'web' 
				WHERE 
						proyecto = '{$this->elemento->get_id()}'
					AND	solicitud_tipo = 'browser' 
					AND carpeta <> 1 OR carpeta IS NULL;";
		return $this->elemento->get_db()->ejecutar($sql);
	}	

	function proyecto__migrar_tipo_solicitud_carpetas()
	{
		$sql = "UPDATE apex_item SET solicitud_tipo = NULL WHERE 
						carpeta = 1
					AND	proyecto = '{$this->elemento->get_id()}'";
		return $this->elemento->get_db()->ejecutar($sql);
	}
	
	/**
	 * Se cambia:
	 *	evt__id__carga por conf__id
	 * 	evt__entrada__id por evt__id__entrada
	 *  evt__inicializar por ini
	 */
	function proyecto__cambio_api_ci()
	{
		$editor = new toba_editor_archivos();
		$editor->agregar_sustitucion('/evt__(\w+)__carga\(/', 'conf__${1}(');
		$editor->agregar_sustitucion('/evt__entrada__(\w+)\(/', 'evt__${1}__entrada(');
		$editor->agregar_sustitucion('/evt__salida__(\w+)\(/', 'evt__${1}__salida(');
		$editor->agregar_sustitucion('/evt__inicializar\(/', 'ini(');
		$editor->agregar_sustitucion("|this->cn|","this->cn()");
		$editor->agregar_sustitucion("|evt__obtener_datos_cn|","get_datos_cn");
		$editor->agregar_sustitucion("|evt__entregar_datos_cn|","set_datos_cn");
		$editor->agregar_sustitucion("|evt__limpieza_memoria|","limpiar_memoria");
		$editor->agregar_sustitucion("|disparar_obtencion_datos_cn|","disparar_get_datos_cn");
		$editor->agregar_sustitucion("|disparar_entrega_datos_cn|","disparar_set_datos_cn");		
		$editor->agregar_sustitucion("|set_etapa_gi|","set_pantalla");
		$editor->agregar_sustitucion("|get_etapa_gi|","get_id_pantalla");
		$editor->agregar_sustitucion("|this\._ci|","this.controlador");
		$editor->agregar_sustitucion("|this->dependencias\[['\"](.*?)['\"]\]|","this->dep('$1')");
		$archivos = toba_manejador_archivos::get_archivos_directorio( $this->elemento->get_dir(), '|.php|', true);
		$editor->procesar_archivos($archivos);
	}
	
	function proyecto__fuente_no_obligatoria()
	{
		$sql = "
			UPDATE apex_objeto SET fuente_datos=NULL, fuente_datos_proyecto=NULL
			WHERE 
					proyecto = '{$this->elemento->get_id()}'
				AND clase NOT IN ('objeto_ei_formulario', 
								'objeto_ei_formulario_ml',
								'objeto_ei_filtro',
								'objeto_datos_tabla',
								'objeto_datos_relacion',
								'objeto_cn');
			";
		return $this->elemento->get_db()->ejecutar($sql);
	}
	
	/**
	*	Usa la primera fuente de datos como fuente por defecto.
	*/
	function proyecto__fuente_por_defecto()
	{
		$sql = "
			UPDATE apex_proyecto 
				SET fuente_datos = (SELECT fuente_datos 
									FROM apex_fuente_datos 
									WHERE proyecto = '{$this->elemento->get_id()}'
									LIMIT 1)
			WHERE proyecto = '{$this->elemento->get_id()}';
			";
		return $this->elemento->get_db()->ejecutar($sql);
	}

	/**
	 * Todas las clases del nucleo deben estar precedidas por toba_
	 */
	function proyecto__namespace_toba()
	{
		$editor = new toba_editor_archivos();
		$editor->agregar_sustitucion('/objeto_ci/', 			'toba_ci');
		$editor->agregar_sustitucion('/objeto_ei/', 			'toba_ei');
		$editor->agregar_sustitucion('/objeto_cn/', 			'toba_cn');
		$editor->agregar_sustitucion('/objeto_datos_tabla/',	'toba_datos_tabla');
		$editor->agregar_sustitucion('/objeto_datos_relacion/',	'toba_datos_relacion');
		$editor->agregar_sustitucion('/nucleo_toba/',			'toba_nucleo');
		$editor->agregar_sustitucion('/form::/',				'toba_form::');
		$editor->agregar_sustitucion('/recurso::/',				'toba_recurso::');
		$editor->agregar_sustitucion('/fuente_de_datos/',		'toba_fuente_datos');
		$editor->agregar_sustitucion('/info_instalacion/',		'toba_instalacion');
		$editor->agregar_sustitucion('/info_instancia/',		'toba_instancia');
		$editor->agregar_sustitucion('/info_proyecto/',			'toba_proyecto');
		$editor->agregar_sustitucion('/parser_ayuda/',			'toba_parser_ayuda');
		$editor->agregar_sustitucion('/excepcion_toba/',		'toba_error');
		$editor->agregar_sustitucion('/js::/',					'toba_js::');
		$editor->agregar_sustitucion('/zona.php/',				'toba_zona.php');
		$editor->agregar_sustitucion('/extends zona/',			'extends toba_zona');
		$editor->agregar_sustitucion('/ap_relacion_db/',		'toba_ap_relacion_db');
		$editor->agregar_sustitucion('/ap_tabla_db/',			'toba_ap_tabla_db');
		$editor->agregar_sustitucion('/mensaje::/',				'toba::mensajes()->');
		$archivos = toba_manejador_archivos::get_archivos_directorio($this->elemento->get_dir(), '/.php$/', true);
		$editor->procesar_archivos($archivos);
	}
	
	/**
	 * Debido a las nuevas convenciones, se prefiere que los metodos que no retornan tipos simples
	 * no se prefijen con get_, por ej. se usa toba::logger()
	 */
	function proyecto__cambio_convenciones_clase_toba()
	{
		$editor = new toba_editor_archivos();
		$editor->agregar_sustitucion('/toba::get_logger/', 			'toba::logger');
		$editor->agregar_sustitucion('/toba::get_nucleo/', 			'toba::nucleo');
		$editor->agregar_sustitucion('/toba::get_solicitud/', 		'toba::solicitud');
		$editor->agregar_sustitucion('/toba::get_zona/', 			'toba::zona');
		$editor->agregar_sustitucion('/toba::get_vinculador/', 		'toba::vinculador');
		$editor->agregar_sustitucion('/toba::get_hilo/', 			'toba::hilo');
		$editor->agregar_sustitucion('/toba::get_permisos/', 		'toba::permisos');
		$editor->agregar_sustitucion('/toba::get_cola_mensajes/',	'toba::notificacion');
		$editor->agregar_sustitucion('/toba::get_fuente/',			'toba::fuente');
		$editor->agregar_sustitucion('/toba::get_db/',				'toba::db');
		$editor->agregar_sustitucion('/toba::get_encriptador/',		'toba::encriptador');
		$editor->agregar_sustitucion('/toba::get_cronometro/',		'toba::cronometro');
		$editor->agregar_sustitucion('/toba::get_sesion/',			'toba::sesion');
		$editor->agregar_sustitucion('/toba::get_usuario/',			'toba::usuario');
		$archivos = toba_manejador_archivos::get_archivos_directorio($this->elemento->get_dir(), '/.php$/', true);
		$editor->procesar_archivos($archivos);
	}
	
	function proyecto__notificacion_js()
	{
		$editor = new toba_editor_archivos();		
		$editor->agregar_sustitucion('/cola_mensajes\./',			'notificacion.');
		$archivos = toba_manejador_archivos::get_archivos_directorio($this->elemento->get_dir(), '/.php$/', true);
		$editor->procesar_archivos($archivos);
	}
	
	/**
	 * Los obtener_X pasan a ser get_X 
	 * cargar_estado_ef pasa a ser set_datos_defecto
	 * Cambios en toba_recurso
	 */
	function proyecto__cambio_api_varios()
	{
 		
		$editor = new toba_editor_archivos();
		$editor->agregar_sustitucion('/obtener_clave_fila/', 			'get_clave_fila');
		$editor->agregar_sustitucion('/obtener_proyecto\(\)/', 			'get_proyecto()');
		$editor->agregar_sustitucion('/obtener_proyecto_path\(\)/', 	'get_proyecto_path()');
		$editor->agregar_sustitucion('/obtener_vinculo_de_objeto/', 	'get_vinculo_de_objeto');
		$editor->agregar_sustitucion('/obtener_path\(\)/', 				'get_path()');
		$editor->agregar_sustitucion('/obtener_usuario_nivel_acceso/',	'get_usuario_nivel_acceso');
		$editor->agregar_sustitucion('/obtener_usuario\(\)/',			'get_usuario()');
		$editor->agregar_sustitucion('/obtener_html_barra_superior/',	'generar_html_barra_superior');
		$editor->agregar_sustitucion('/obtener_html_barra_inferior/',	'generar_html_barra_inferior');
		$editor->agregar_sustitucion('/obtener_proyecto_descripcion/',	'get_proyecto_descripcion');
		$editor->agregar_sustitucion('/obtener_parametro/',				'get_parametro');
		$editor->agregar_sustitucion('/cargar_estado_ef/',				'set_datos_defecto');
		$editor->agregar_sustitucion('/toba_recurso::path_pro/',		'toba_recurso::url_proyecto');
		$editor->agregar_sustitucion('/toba_recurso::path_apl/',		'toba_recurso::url_toba');
		$editor->agregar_sustitucion('/toba_recurso::imagen_apl/',		'toba_recurso::imagen_toba');
		$editor->agregar_sustitucion('/toba_recurso::imagen_pro/',		'toba_recurso::imagen_proyecto');
		$editor->agregar_sustitucion("|ejecutar_sql\b.*?\(|","ejecutar_fuente(");
		$editor->agregar_sustitucion("|deshabilitar_efs|","set_solo_lectura");
		$editor->agregar_sustitucion("|\.valor\(|",".get_estado(");
		$editor->agregar_sustitucion("|\.cambiar_valor\(|",".set_estado(");		
		$archivos = toba_manejador_archivos::get_archivos_directorio($this->elemento->get_dir(), '/.php$/', true);
		$editor->procesar_archivos($archivos);		
	}
	
	/**
	 * Algunos metodos del hilo se derivan al toba_proyecto, toba_usuario, etc.
	 */
	function proyecto__cambio_api_hilo()
	{
		$editor = new toba_editor_archivos();
		$editor->agregar_sustitucion('/toba::hilo\(\)->get_proyecto_path/', 			'toba::proyecto()->get_path');
		$editor->agregar_sustitucion('/toba::hilo\(\)->get_path_temp/', 				'toba::instalacion()->get_path_temp');
		$editor->agregar_sustitucion('/toba::hilo\(\)->get_path/', 						'toba::instalacion()->get_path');
		$editor->agregar_sustitucion('/toba::hilo\(\)->get_proyecto/', 					'toba::proyecto()->get_id');		
		$editor->agregar_sustitucion('/toba::hilo\(\)->get_usuario/', 					'toba::usuario()->get_id');
		$editor->agregar_sustitucion('/toba::hilo\(\)->obtener_usuario_grupo_acceso/', 	'toba::usuario()->get_grupo_acceso');
		$editor->agregar_sustitucion('/persistir_dato_sincronizado/', 					'set_dato_sincronizado');
		$editor->agregar_sustitucion('/recuperar_dato_sincronizado/', 					'get_dato_sincronizado');
		$editor->agregar_sustitucion('/recuperar_dato_global/', 						'get_dato');
		$editor->agregar_sustitucion('/eliminar_dato_global/', 							'eliminar_dato');
		$editor->agregar_sustitucion('/existe_dato_global/', 							'existe_dato');
		$editor->agregar_sustitucion('/limpiar_memoria_global/', 						'limpiar_datos');
		$archivos = toba_manejador_archivos::get_archivos_directorio($this->elemento->get_dir(), '/.php$/', true);
		$editor->procesar_archivos($archivos);		
	}	
	
	function proyecto__cambio_memoria_por_hilo()
	{
		$editor = new toba_editor_archivos();
		$editor->agregar_sustitucion('/toba::hilo/', 		'toba::memoria');
		$archivos = toba_manejador_archivos::get_archivos_directorio($this->elemento->get_dir(), '/.php$/', true);
		$editor->procesar_archivos($archivos);				
	}
	
	
	/**
	 * El id del item raiz deja de ser vacio ''
	 */
	function proyecto__id_item_raiz()
	{
		$sql = array();
		$sql[] = "SET CONSTRAINTS ALL DEFERRED;";
		$sql[] = "UPDATE apex_item SET item = '__raiz__' 
					WHERE 
							item = ''
						AND proyecto = '{$this->elemento->get_id()}'
					";
		$sql[] = "UPDATE apex_item SET padre = '__raiz__' 
					WHERE 
							padre=''
						AND proyecto = '{$this->elemento->get_id()}'
					";
		$sql[] = "UPDATE apex_usuario_grupo_acc_item SET item = '__raiz__'
					WHERE 
							item =''
						AND proyecto = '{$this->elemento->get_id()}'							
				";
		return $this->elemento->get_db()->ejecutar($sql);
	}
}
?>