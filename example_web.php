<?php
/*
 * PHP EpSolar Tracer Class (PhpEpsolarTracer) v0.9
 *
 * Library for communicating with 
 * Epsolar/Epever Tracer BN MPPT Solar Charger Controller
 *
 * THIS PROGRAM COMES WITH ABSOLUTELY NO WARRANTIES !
 * USE IT AT YOUR OWN RISKS !
 *
 * Copyright (C) 2016 under GPL v. 2 license
 * 13 March 2016
 *
 * @author Luca Soltoggio
 * http://www.arduinoelettronica.com/
 * https://arduinoelectronics.wordpress.com/
 *
 * This is an example on how to use the library
 *
 * It creates a web page with tracer datas
 *
 */
 
require_once 'PhpEpsolarTracer.php';
$tracer = new PhpEpsolarTracer('/dev/ttyUSB0');

$tracerstatus_bgcolor = "#dedede";
// $ecolor = "black";
// $battSoc = 0;

// Get Info and check if is connected
if ($tracer->getInfoData()) {
	$connection="Connected";
	$connection_bgcolor = "lime";
}
else
	{
	$connection="Disconnected";
	$connection_bgcolor = "red";
}

// Get Real Time Data
if ($tracer->getRealTimeData()) {
	$tracerstatus_bgcolor = "lime";
	$equipStatus = $tracer->realtimeData[16];
	$chargStatus = 0b11 & ($equipStatus >> 2);
	switch ($chargStatus) {
		case 0: $eStatus = "Not charging"; break;
		case 1: $eStatus = "Float (13.8V)"; break;
		case 2: $eStatus = "Boost (14.4V)"; break;
		case 3: $eStatus = "Equalization (14.6V)"; break;
	};
	if ($equipStatus >> 4) {
		$eStatus = "<font color=\"red\">FAULT</font>";
		$tracerstatus_bgcolor = "red";		
	}
	
	$battStatus = $tracer->realtimeData[15];
	$battLevel = 0b1111 & $battStatus;
	switch ($battLevel) {
		case 0: $bStatus = "Normal"; break;
		case 1: $bStatus = "<font color=\"red\">Overvolt</font>"; break;
		case 2: $bStatus = "<font color=\"yellow\">Undervolt</font>"; break;
		case 3: $bStatus = "<font color=\"red\">Low volt disconnect</font>"; break;
		case 4: { 
			$bStatus = "<font color=\"red\">FAULT</font>"; 
			$tracerstatus_bgcolor = "red";
			break;
		}
	}
	
	$battSoc = $tracer->realtimeData[12];
}
?>
<!DOCTYPE html>
<html lang="it">
  <head>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <title>PHP EpSolar Tracer Class Web Example</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	
	<style>
	table.gridtable {
		font-family: verdana,arial,sans-serif;
		font-size:12px;
		color:#333333;
		border-width: 1px;
		border-color: #666666;
		border-collapse: collapse;
		width: 100%;
	}
	
	table.gridtable th {
		border-width: 1px;
		padding: 8px;
		border-style: solid;
		border-color: #666666;
		background-color: #dedede;
		text-align: center;
	}

	table.gridtable th.connection {
		background-color: <?php echo $connection_bgcolor ?>;
		text-align:center;
	}
	
		table.gridtable th.tracerstatus {
		background-color: <?php echo $tracerstatus_bgcolor ?>;
		text-align:center;
	}

	table.gridtable td {
		border-width: 1px;
		border-top: 0px;
		padding: 5px;
		border-style: solid;
		border-color: #666666;
		background-color: #ffffff;
		text-align:right;
		height:17px;
	}

	table.gridtable td.bold {
		font-weight: bold;
		width: 33.3%;
		text-align:left;
	}

	table.gridtable td.head {
		font-weight: bold;
		width: 33.3%;
		text-align:right;
	}

	table.gridtable td.button {
		width: 15%;
		text-align:center;
		background-color:#efefef;
		color:#cecece;
		cursor: default;
	}

	div.centered 
	{
	text-align: center;
	}

	div.inner
	{
	max-width: 650px;
	width: 95%;
	text-align: center;
	margin: 0 auto;
	}
	div.inner table
	{
	margin: 0 auto; 
	text-align: left;
	}

	#chargepercentp {
		width: 100%;
		height: 100%;
		position: absolute;
		vertical-align: middle;
		left:-5px;
		z-index: 10;
	}

	#chargepercentg {
		top: 0;
		width: <?php echo $battSoc; ?>%;
		height: 100%;
		position: absolute;
		background-color:#dedede;
		margin: 0 auto;
		padding: 0;
		z-index: 1;
	}

	#container {
		position: relative;
		top: 0;
		left: 0;
		width:100%;
		height:100%;
		margin: 0 auto;
		padding: 0;
		vertical-align: middle;
		line-height: 27px;
	}
	</style> 
    
  </head>
  <body>
  
<div class="centered">
<div class="inner">
<p style="	font-family: verdana,arial,sans-serif; font-size:16px; font-weight:bold;">PHP EpSolar Tracer Class Web Example</p>


<table class="gridtable">
<tr>
	<th class="connection" id="connection"><?php echo $connection; ?></th>
</tr>

</table>

<br>

<table class="gridtable">
<tr>
	<th class="tracerstatus" id="tracerstatus" colspan=2>-= Tracer Status =-</th>
</tr>
<tr>
	<td class="bold">Battery status</td><td class="status" id="batterystatus"><?php echo $bStatus; ?></td>
</tr>
<tr>
	<td class="bold">Equipment status</td><td class="status" id="equipmentstatus"><?php echo $eStatus; ?></td>
</tr>
<tr>
	<td class="bold">Battery SOC</td><td style="padding:0px; height:27px;"><div id="container"><div id="chargepercentg"></div><div id="chargepercentp"><?php echo $battSoc; ?>%</div></div></td>
</tr>
</table>

<br>

<table class="gridtable">
<tr>
	<th colspan=2>-= Tracer Data =-</th>
</tr>
<tr>
	<td class="bold">Battery Voltage</td><td class="data" id="batteryvoltage"><?php echo $tracer->realtimeData[3]; ?>V</td>
</tr>
<tr>
	<td class="bold">Battery Current</td><td class="data" id="batterycurrent"><?php echo $tracer->realtimeData[4]-$tracer->realtimeData[7]; ?>A</td>
</tr>
<tr>
	<td class="bold">Battery Power</td><td class="data" id="batterypower"><?php echo $tracer->realtimeData[5]-$tracer->realtimeData[8]; ?>W</td>
</tr>
<tr>
	<td class="bold">Panel Voltage</td><td class="data" id="panelvoltage"><?php echo $tracer->realtimeData[0]; ?>V</td>
</tr>
<tr>
	<td class="bold">Panel Current</td><td class="data" id="panelcurrent"><?php echo $tracer->realtimeData[1]; ?>A</td>
</tr>
<tr>
	<td class="bold">Panel Power</td><td class="data" id="panelpower"><?php echo $tracer->realtimeData[2]; ?>W</td>
</tr>
<tr>
	<td class="bold">Charger temperature</td><td class="data" id="chargertemperature"><?php echo $tracer->realtimeData[10]; ?><sup>o</sup>C</td>
</tr>
</table>

<br>

<table class="gridtable">
<tr>
	<th id="tracerinfo" colspan=2>-= Tracer Info =-</th>
</tr>
<tr>
	<td class="bold">Manufacturer</td><td class="info" id="manufacturer"><?php echo $tracer->infoData[0]; ?></td>
</tr>
<tr>
	<td class="bold">Model</td><td class="info" id="model"><?php echo $tracer->infoData[1]; ?></td>
</tr>
<tr>
	<td class="bold">Version</td><td class="info" id="version"><?php echo $tracer->infoData[2]; ?></td>
</tr>
</table>

<br>

</div>
</div>

  </body>
</html>

