<?
	function responder($respuesta)
	{
		echo "<script language='javascript'>";
		if(is_array($respuesta)){
			echo " datos = new Object();\n";
			foreach($respuesta as $id => $valor)
			{
				echo "datos['$id'] = '$valor';\n";	
			}
			echo " responder_consulta(datos);\n";
		}else{
			echo " responder_consulta('". $respuesta . "');\n";
		}
		echo "</script>";
	}

	if(isset($_POST['nombre_frame'])){
		$nombre_frame = $_POST['nombre_frame'];
	}else{
		$nombre_frame = "x";//Eso solo deberia pasar en el request inicial
	}
?>
<HTML>
<HEAD>
<script language='javascript'>

//Solicita informacion al servidor (Esta funcion recarga la pagina)
//Faltan validaciones!
function realizar_consulta(nombre_frame, item_proyecto, item, parametros)
{
	//1) Cargo en el formulario el ITEM que resuelve la pregunta
	v_prefijo="<? echo $this->hilo->prefijo_vinculo() ?>";
	v_id_item="<? echo apex_hilo_qs_item ?>";
	v_separador="<? echo apex_qs_separador ?>";
	vinculo = v_prefijo + "&" + v_id_item + "=" + item_proyecto + v_separador + item;
	//alert(vinculo);
	document.comunicaciones.action=vinculo;
	//2) Setear los campos con parametros: frame, parametros
	document.comunicaciones.parametros.value = parametros;
	document.comunicaciones.nombre_frame.value = nombre_frame;
	//3) Hacer un submit del formulario
	document.comunicaciones.submit();
}

//Devuelve la informacion despues de que la pagina es recargada por la funcion anterior
function responder_consulta(retorno)
{
	//alert('El server respondio');
	top.frames['<? echo $nombre_frame ?>'].retornar_info(retorno);
}
</script>
</HEAD>
<BODY>
<?
	include_once('nucleo/browser/interface/form.php');
	echo form::abrir("comunicaciones","no_ahun");
	echo form::hidden("nombre_frame","no_ahun");
	echo form::hidden("parametros","no_ahun");
	echo form::cerrar();
?>