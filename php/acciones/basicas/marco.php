<? 
	$vinculo_contenido = explode(apex_qs_separador,apex_pa_item_inicial_contenido);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<title><? echo $this->hilo->obtener_proyecto_descripcion()  ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<frameset rows="34,*,0" cols="*" framespacing="0" border="0" frameborder="YES">
  <frame src="<? echo $this->vinculador->generar_solicitud("toba","/basicos/cabecera")?>" name="cabecera" noresize scrolling="no">
  <frame src="<? echo $this->vinculador->generar_solicitud($vinculo_contenido[0],$vinculo_contenido[1]) ?>" name="contenido"  scrolling="auto">
  <frame src="<? echo $this->vinculador->generar_solicitud("toba","/basicos/com_js")?>" name="<? echo  apex_frame_com ?>" scrolling="NO">
</frameset>
<noframes><body>
</body></noframes>
</html>
