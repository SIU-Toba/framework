#!/bin/bash

#Crea la carpeta acto administrativo
curl -v -u jasperadmin:jasperadmin -c cookies.txt -d "@folder_descriptor.xml" -H "Content-Type:application/repository.folder+xml" http://localhost:8180/jasperserver/rest_v2/resources/reportes/toba_referencia/operaciones?createFolders=true&overwrite=true 

sleep 3;

#Sube el jrxml del reporte
curl -v -b cookies.txt -F "data=@operaciones_araireportes.jrxml" -F "label=listado" -F "type=jrxml" -F "uri=/reportes/toba_referencia/operaciones/listado" -F "version=1" -F "permissionMask=1" http://localhost:8180/jasperserver/rest_v2/resources/reportes/toba_referencia/operaciones?createFolders=true&overwrite=true 

sleep 3;


#Crea el reportUnit para poder obtener el pdf/html/doc etc
curl -v -b cookies.txt -d "@reportUnit_descriptor2.xml" -H "Content-Type:application/repository.reportUnit+xml" http://localhost:8180/jasperserver/rest_v2/resources/reportes/toba_referencia/operaciones?createFolders=true&overwrite=true 

