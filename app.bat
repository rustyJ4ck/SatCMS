@echo off
chcp 65001 > NUL

SET cmd=q:\xampp\php\php app.php

IF NOT "%1" == "" (
%cmd% %*
) ELSE (
%cmd% help 
)
