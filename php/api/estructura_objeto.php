<?
/*
	Falta: 
		- Mostrar codigo PHP
		- Esto hay que acompañarlo con un buen uso de los INSTANCIADORES

*/
class estructura_objeto
{
	private $datos;
	private $dependencias = array();

	function __construct($proyecto, $objeto)
	{
		$sql = "	SELECT 	o.proyecto        			as proyecto, 
							o.objeto          			as objeto,
							o.clase_proyecto  			as clase_proyecto, 
							o.clase           			as clase,
							o.subclase        			as subclase, 
							o.subclase_archivo			as subclase_archivo, 
							o.nombre          			as nombre,
							o.titulo          			as titulo,
							o.descripcion     			as descripcion,
							c.editor_proyecto			as editor_proyecto,
							c.editor_item               as editor_item,
							c.instanciador_proyecto     as instanciador_proyecto,
							c.instanciador_item         as instanciador_item,
							c.icono						as icono
					FROM 	apex_objeto o,
							apex_clase c
					WHERE 	( c.clase = o.clase )
					AND		( c.proyecto = o.clase_proyecto )
					AND		( o.proyecto = '$proyecto' ) 
					AND 	( o.objeto = '$objeto' ) ;";
		$this->datos = current (consultar_fuente($sql));
		$sql = "	SELECT 	proyecto, 
							objeto_proveedor, 
							identificador
					FROM 	apex_objeto_dependencias 
					WHERE 	( proyecto = '$proyecto' ) 
					AND 	(objeto_consumidor = '$objeto' ) ;";
		$this->dependencias = consultar_fuente($sql);
	}
	//----------------------------------------------------------------------------
		
	function generar_html()
	{
		echo "<table class='tabla-0' width='100%'>";
		$imagen = recurso::imagen_apl($this->datos['icono'],true);
		echo "<tr><td   class='barra-obj-leve' > $imagen [" . $this->datos['objeto'] . "] - ".  $this->datos['subclase'] . "</td></tr>";
		echo "<tr><td  class='barra-obj-link' >";

		echo "<table class='tabla-0' width='100%'>";
		echo "<tr><td>";
		echo $this->generar_acceso_editores();
		echo "</td>";
		echo "<td>";
		echo $this->datos['nombre'];
		echo "</td></tr>";
		echo "</table>";


		echo "</td></tr>";
		//--- DEPENDENCIAS ---
		if(isset($this->dependencias))
		{
			echo "<tr><td>";
			echo "<table   class='tabla-0' width='100%'>";
			foreach($this->dependencias as $dep)
			{
				echo "<tr><td class='barra-obj-tit'> {$dep['identificador']} </td>";
				echo "<td>";
				$objeto = new estructura_objeto($dep['proyecto'],$dep['objeto_proveedor']);
				$objeto->generar_html();
				echo "</td></tr>";
			}
			echo "</td></tr>";
			echo "</table>";
		}
		echo "</table>";
	}
	//----------------------------------------------------------------------------
	
	function generar_acceso_editores()
	{
		$target = apex_frame_centro;//apex_frame_lista
		echo "<table   class='tabla-0'>";
		echo "<tr>";
		echo "<td  class='barra-obj-id' width='5'>";
		echo "<a target='$target' href='" . toba::get_vinculador()->generar_solicitud(
									"toba","/admin/objetos/propiedades",
									array(apex_hilo_qs_zona=>$this->datos["proyecto"]
										.apex_qs_separador. $this->datos["objeto"]) ) ."'>".
			recurso::imagen_apl("objetos/objeto.gif",true,null,null,"Editar propiedades BASICAS del OBJETO"). "</a>";
		echo "</td>\n";
		echo "<td  class='barra-obj-id' width='5'>";
		echo "<a  target='$target' href='" . toba::get_vinculador()->generar_solicitud(
									$this->datos["editor_proyecto"],
									$this->datos["editor_item"],
									array(apex_hilo_qs_zona=>$this->datos["proyecto"]
										 .apex_qs_separador. $this->datos["objeto"]) ) ."'>".
			recurso::imagen_apl("objetos/editar.gif",true,null,null,"Editar propiedades ESPECIFICAS del OBJETO"). "</a>";
		echo "</td>\n";
/*
		echo "<td  class='barra-obj-id' width='5'>";
		echo "<a  target='$target' href='" . toba::get_vinculador()->generar_solicitud(
									$this->datos["instanciador_proyecto"], 
									$this->datos["instanciador_item"],
									array(apex_hilo_qs_zona=>$this->datos["proyecto"]
										.apex_qs_separador. $this->datos["objeto"]) ) ."'>".
			recurso::imagen_apl("objetos/instanciar.gif",true,null,null,"INSTANCIAR el OBJETO"). "</a>";
		echo "</td>\n";		
*/
		echo "</tr>";
		echo "</table>";
	}
	//----------------------------------------------------------------------------
}
?>