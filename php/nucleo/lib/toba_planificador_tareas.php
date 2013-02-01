<?php

/**
 * Permite programar tareas a ejecutarse automáticamente en el servidor
 * @package Centrales  
 */
class toba_planificador_tareas
{
	protected $proyecto;
	
	function __construct($proyecto=null)
	{
		if (! isset($proyecto)) {
			$proyecto = toba::proyecto()->get_id();
		}
		$this->proyecto = $proyecto;
	}
	
	/**
	 * Programa la ejecución de una tarea
	 *
	 * @param toba_tarea $tarea Objeto tarea, puede ser un toba_mail o cualquier clase que implemente la interface toba_tarea
	 * @param string $timestamp Fecha y hora de ejecución, expresado en un timestamp postgres (ej. now() + '5 minutes')
	 * @param string $intervalo Perioricidad con que se ejecuta la tarea por ej '1 week'::interval para ejecutar una vez por semana. Si es null se ejecuta por única vez.
	 * @param string $nombre Nombre de la tarea, sirve para identificarla en el log
	 * @return integer Id. de la tarea programada
	 */
	function programar_tarea(toba_tarea $tarea, $timestamp, $intervalo=null, $nombre=null)
	{
		$db = toba::instancia()->get_db();
		if (isset($intervalo)) {
			$intervalo = $db->quote($intervalo);
		} else {
			$intervalo = 'NULL';
		}
		$nombre = $db->quote($nombre);
		$clase = get_class($tarea);

		$sql = "INSERT INTO apex_tarea (proyecto, nombre, ejecucion_proxima, intervalo_repeticion, tarea_objeto, tarea_clase) VALUES ";
		$sql.= "('{$this->proyecto}', $nombre, $timestamp, $intervalo, ?, '$clase')";
		
		//Inserta el objeto serializado en el BLOB
		$pdo = $db->get_pdo();
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(1, serialize($tarea), PDO::PARAM_LOB);
		$stmt->execute();
		
		$id_tarea = $db->recuperar_secuencia('apex_tarea_seq');
		return $id_tarea;
	}
	
	/**
	 * Quita la programación de una tarea
	 *
	 * @param integer $id_tarea Número de la tarea programada
	 * @param toba_manejodr_interface $manejador_interface Clase para la salida grafica, por defecto nulo
	 */
	function desprogramar($id_tarea, $manejador_interface=null)
	{
		$db = toba::instancia()->get_db();
		$id_tarea = $db->quote($id_tarea);
		$proyecto = $db->quote($this->proyecto);
		$sql = "DELETE FROM apex_tarea WHERE tarea=$id_tarea AND proyecto= $proyecto";
		$db->ejecutar($sql);
		$mensaje_debug = "[Programador de Tareas] Tarea $id_tarea desprogramada";
		toba::logger()->debug($mensaje_debug);	
		if (isset($manejador_interface)) {
			$manejador_interface->mensaje($mensaje_debug);
		}
	}
	
	/**
	 * Ejecuta todas aquellas tareas que estén en período de ejecución (pasadas) 
	 * Por lo general este método se invoca desde el planificador de tareas del S.O.
	 * @param toba_manejodr_interface $manejador_interface Clase para la salida grafica, por defecto nulo
	 */
	function ejecutar_pendientes($manejador_interface=null)
	{
		$proyecto = toba::instancia()->get_db()->quote($this->proyecto);
		$sql = "
			SELECT 
				tarea
			FROM
				apex_tarea
			WHERE
					proyecto = $proyecto
				AND ejecucion_proxima <= NOW()
		";
		$tareas = toba::instancia()->get_db()->consultar($sql);
		$mensaje_debug = "[Programador de Tareas] Encontradas ".count($tareas)." tarea(s) pendiente(s)";
		toba::logger()->debug($mensaje_debug);
		if (isset($manejador_interface)) {
			$manejador_interface->subtitulo($mensaje_debug);
		}		
		foreach ($tareas as $tarea) {
			$this->ejecutar_tarea($tarea['tarea'], $manejador_interface);
			if (isset($manejador_interface)) {
				$manejador_interface->enter();
			}
		}
	}
	
	/**
	 * Fuerza la ejecución de una tarea específica, sin tener en cuenta su momento de planificación
	 *
	 * @param integer $id_tarea Número de la tarea programada
	 * @param toba_manejodr_interface $manejador_interface Clase para la salida grafica, por defecto nulo
	 */
	function ejecutar_tarea($id, $manejador_interface=null)
	{
		//-- Obtiene los datos de la tarea
		$db = toba::instancia()->get_db();
		$id = $db->quote($id);
		$proyecto = $db->quote($this->proyecto);
		$sql = "
			SELECT 
				tar.tarea,
				tar.nombre,
				tar.tarea_objeto,
				tar.tarea_clase,
				tar.intervalo_repeticion,
				tar.ejecucion_proxima
			FROM
				apex_tarea tar
			WHERE
					tar.proyecto = $proyecto
				AND	tar.tarea = $id
		";
		$datos = $db->consultar_fila($sql);
		if ($datos === false) {
			throw new toba_error("[Programador de Tareas] No existe una tarea programada con id '{$datos['tarea']}' en el proyecto '{$this->proyecto}'");
		}
		
		//-- Ejecuta el objeto tarea
		$tarea = unserialize(stream_get_contents($datos['tarea_objeto']));
		if ($tarea === false) {
			$mensaje_debug = "[Programador de Tareas] Error al deserializar tarea '{$datos['tarea']}' con clase '{$datos['clase']}' en el proyecto '{$this->proyecto}'";
			toba::logger()->error($mensaje_debug);
			if (isset($manejador_interface)) {
				$manejador_interface->error($mensaje_debug);
			}
		} else {
			$tarea->ejecutar();
			$this->registrar_ejecucion($datos, $manejador_interface);

			//Si no posee proxima ejecucion la elimina, sino la reprograma a futuro
			if (!isset($datos['intervalo_repeticion'])) {
				$this->desprogramar($datos['tarea'], $manejador_interface);
			} else {
				$this->reprogramar($datos, $manejador_interface);
			}	
		}
	}
	
	/**
	 * Loguea la ejecucion de la tarea
	 */
	protected function registrar_ejecucion($datos, $manejador_interface=null)
	{
		$db = toba::instancia()->get_db();
		$schema_log = $db->get_schema(). '_logs';
		$proyecto = $db->quote($this->proyecto);
		$id = $db->quote($datos['tarea']);
		$mensaje_debug = "[Programador de Tareas] Ejecutada tarea $id:{$datos['nombre']} de clase '{$datos['tarea_clase']}' en el proyecto '{$this->proyecto}'";
		toba::logger()->debug($mensaje_debug);		
		if (isset($manejador_interface)) {
			$manejador_interface->mensaje($mensaje_debug);
		}		
		
		$sql = "INSERT INTO $schema_log.apex_log_tarea (proyecto, tarea, nombre, tarea_clase, tarea_objeto, ejecucion)
				SELECT proyecto, tarea, nombre, tarea_clase, tarea_objeto, NOW()
				FROM apex_tarea WHERE tarea=$id AND proyecto = $proyecto
		";
		$db->ejecutar($sql);
	}
	
	/**
	 * Vuelve a programar la tarea, asegurandose que sea en el futuro
	 */
	protected function reprogramar($datos, $manejador_interface=null)
	{
		$proxima = $datos['ejecucion_proxima'];
		$db = toba::instancia()->get_db();
		$proyecto = $db->quote($this->proyecto);
		$tarea = $db->quote($datos['tarea']);
		
		//--Cicla aumentando de a intervalos hasta encontrar una fecha futura
		do {
			$sql = "
				SELECT 
					('$proxima'::timestamp + intervalo_repeticion) as proxima,
					('$proxima'::timestamp + intervalo_repeticion >= NOW()) as es_futura
				FROM
					apex_tarea
				WHERE
						tarea = $tarea
					AND proyecto = $proyecto
			";
			$rs = $db->consultar_fila($sql);
			$proxima = $rs['proxima'];
		} while ($rs['es_futura'] !== true);
		
		//-- Actualiza la tarea
		$sql = "UPDATE apex_tarea SET 
					ejecucion_proxima = '{$rs['proxima']}'::timestamp
				WHERE
						tarea = $tarea
					AND	proyecto = $proyecto
		";
		$db->ejecutar($sql);

		$mensaje = "[Programador de Tareas] Tarea $tarea reprogamada al {$rs['proxima']}";
		toba::logger()->debug($mensaje);
		if (isset($manejador_interface)) {
			$manejador_interface->mensaje($mensaje);
		}
		
	}
	
}


?>
