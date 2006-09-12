@echo off
set toba_instancia=desarrollo

chdir > tempfile
call setfile.bat tempfile
del tempfile
set toba_dir=%toba_dir%\..
REM echo %toba_dir%
php %toba_dir%\php\consola\run.php instalacion autoinstalar
if  errorlevel  1  goto error_php;

pause;
goto end

:error_php 
echo.
echo ---------------------------------------------------------------------------
echo  Necesita tener a php.exe en la ruta por defecto del sistema
echo ---------------------------------------------------------------------------
pause

:end