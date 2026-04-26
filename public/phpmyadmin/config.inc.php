<?php
/**
 * Configuración de phpMyAdmin para esta aplicación
 */

$cfg['blowfish_secret'] = bin2hex(random_bytes(32));

$i = 0;
$i++;

/* Configuración del servidor MySQL */
$cfg['Servers'][$i]['auth_type'] = 'config';
$cfg['Servers'][$i]['host'] = '127.0.0.1';
$cfg['Servers'][$i]['user'] = 'Carlos';
$cfg['Servers'][$i]['password'] = '1234567890';
$cfg['Servers'][$i]['compress'] = false;
$cfg['Servers'][$i]['AllowNoPassword'] = false;

$cfg['UploadDir'] = '';
$cfg['SaveDir'] = '';
$cfg['TempDir'] = '/tmp';

$cfg['DefaultLang'] = 'es';
$cfg['ServerDefault'] = 1;
$cfg['ShowPhpInfo'] = false;
$cfg['ShowServerInfo'] = false;

/* Seguridad básica */
$cfg['PmaNoRelation_DisableWarning'] = true;
$cfg['SuhosinDisableWarning'] = true;
$cfg['LoginCookieValidity'] = 86400;