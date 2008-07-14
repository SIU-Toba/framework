"""
    Muestra el nombre de la ultima versiÃn lanzada de toba[[BR]]
    Sintaxis: {{{[[version_toba(texto a parsear,completo,texto alternativo)Âº]]}}} [[BR]]
"""
import os

def execute(hdf, txt, env):
    args = txt.split(',', 3)
    template = args[0]
    if len(args) > 1:
	corta = args[1] == '1'
    else:
        corta = 0
    if len(args) > 2:
	vinculo = args[2]
    else:
	vinculo = 0

    archivos = os.listdir('/var/www/downloads/toba')
    archivos.sort(reverse=True)
    version = archivos[0].split('toba_')[-1].split('.zip')[0]
    if corta:
	partes = version.split('.')
	version = partes[0] + '.' + partes[1]

    version = template % version
    	
    if vinculo:
	version = '<a href="' + version  + '">' + vinculo + '</a>'
    
    return version
    
