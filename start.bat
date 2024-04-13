@echo off

python -c "import pandas" 2>nul
IF %ERRORLEVEL% NEQ 0 (
    echo Installing Pandas...
    pip install pandas
)

python -c "import pdfkit" 2>nul
IF %ERRORLEVEL% NEQ 0 (
    echo Installing pdfkit...
    pip install pdfkit
)

set port=8000
start "" php -S 127.0.0.1:%port% 
timeout /t 2 /nobreak >nul
start chrome http://localhost:%port%/
exit

