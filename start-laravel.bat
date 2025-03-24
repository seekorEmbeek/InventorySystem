@echo off
title Starting XAMPP, Laravel, and Ngrok...
echo Starting Apache & MySQL...
start "" "C:\xampp\xampp_start.exe"
timeout /t 5 >nul

rem Navigate to Laravel project folder
cd /d C:\xampp\htdocs\InventorySystem

echo Starting Laravel Server...
start /min cmd /k "php artisan serve --host=127.0.0.1 --port=8000"

echo Starting Ngrok...
start "" C:\ngrok\ngrok.exe http 8000
exit
