<?

	//Cierro las fuentes de datos abiertas
	foreach(array_keys($db) as $base)
	{
		$db[$base][apex_db_con]->close();			
	}

?>