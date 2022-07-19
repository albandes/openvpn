<?php
error_reporting(E_ERROR | E_PARSE);

use Albandes\openvpn;

require("vendor/autoload.php");

$statusFile='openvpn-status.log';
$timezone='America/Sao_Paulo';
$dateFormat='d/m/Y H:i:s';

$ovpn = new opevpn($statusFile,$timezone,$dateFormat);

$ovpn->set_smtpHost('smtp.example.com');
$ovpn->set_smtpUser('no-reply@example.com');
$ovpn->set_smtpPassword('password');
$ovpn->set_smtpPort('587');

$ovpn->set_smtpFrom('no-reply@example.com.br');
$ovpn->set_smtpFromName('No reply');

$ovpn->set_smtpSubject('[VPN] Conexões ativas');


$address = array();
array_push($address,'address@example.com');
array_push($address,'another.address@gmail.com');
$ovpn->set_smtpAddress($address);

$arrayRet = $ovpn->getStatusData();

$ovpn->sendEmail($arrayRet,$subject);
