<? 
	$centro = $this->hilo->obtener_item_inicial();
	$proyecto = $this->hilo->obtener_proyecto_descripcion();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<title><? echo $proyecto ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<frameset rows="*" cols="380,*" frameborder="YES" border="1" bordercolor="#553DA1" framespacing="0" id='frameset_admin'>
  <frameset rows="87,*,3" frameborder="NO" border="1"  bordercolor="#553DA1" framespacing="0">
    <frame src="<? echo $this->vinculador->generar_solicitud("toba","/admin/menu_principal")?>" name="<? echo  apex_frame_control ?>" scrolling="NO">
    <frame src="<? echo $this->vinculador->generar_solicitud("toba","/admin/objetos/organizador")?>" name="<? echo  apex_frame_lista ?>" scrolling="auto">
    <frame src="<? echo $this->vinculador->generar_solicitud("toba","/basicos/com_js")?>" name="<? echo  apex_frame_com ?>" scrolling="auto" >
  </frameset>
  <frame src="<? echo $this->vinculador->generar_solicitud($centro[0], $centro[1], $centro[2])?>" name="<? echo  apex_frame_centro ?>" scrolling="auto">
</frameset>

</html>
