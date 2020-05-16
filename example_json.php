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
	* It returns a JSON object of tracer data
	*
	*/
	
	require_once 'PhpEpsolarTracer.php';

	$tracer = new PhpEpsolarTracer('/dev/ttyUSB0');

	$json = new stdClass();

	if ($tracer->getInfoData()) {
		$json->connected = true;
	} else {
		$json->connected = false;
	}

	if ($tracer->getRealtimeData()) {
		$json->realtime = new stdClass();
		
		$equipStatus = $tracer->realtimeData[16];
		$chargStatus = 0b11 & ($equipStatus >> 2);
		switch ($chargStatus) {
			case 0: $eStatus = "Not charging"; break;
			case 1: $eStatus = "Float (13.8V)"; break;
			case 2: $eStatus = "Boost (14.4V)"; break;
			case 3: $eStatus = "Equalization (14.6V)"; break;
		};
		if ($equipStatus >> 4) {
			$eStatus = "Fault";	
		}
		
		$battStatus = $tracer->realtimeData[15];
		$battLevel = 0b1111 & $battStatus;
		switch ($battLevel) {
			case 0: $bStatus = "Normal"; break;
			case 1: $bStatus = "Overvolt"; break;
			case 2: $bStatus = "Undervolt"; break;
			case 3: $bStatus = "Low volt disconnect"; break;
			case 4: { 
				$bStatus = "FAULT";
				break;
			}
		}
		
		$json->realtime->chargeCode = $chargStatus;
		$json->realtime->chargeStatus = $eStatus;
		$json->realtime->batteryCode = $battStatus;
		$json->realtime->batteryStatus = $bStatus;
		$json->realtime->batterySOC = $tracer->realtimeData[12] . "%";
		$json->realtime->solarVoltage = $tracer->realtimeData[0] . "V";
		$json->realtime->solarCurrent = $tracer->realtimeData[1] . "A";
		$json->realtime->solarWattage = $tracer->realtimeData[2] . "W";
		$json->realtime->batteryVoltage = $tracer->realtimeData[3] . "V";
		$json->realtime->batteryChargingCurrent = $tracer->realtimeData[4] . "A";
		$json->realtime->batteryChargingPower = $tracer->realtimeData[5] . "W";
		$json->realtime->loadVoltage = $tracer->realtimeData[6] . "V";
		$json->realtime->loadCurrent = $tracer->realtimeData[7] . "A";
		$json->realtime->loadWattage = $tracer->realtimeData[8] . "W";
		$json->realtime->temperatureBattery = $tracer->realtimeData[9] . "℃";
		$json->realtime->temperatureBatteryRemote = $tracer->realtimeData[13] . "℃";
		$json->realtime->temperatureCharger = $tracer->realtimeData[10] . "℃";
		$json->realtime->temperatureHeatsink = $tracer->realtimeData[11] . "℃";
		$json->realtime->systemRatedVoltage = $tracer->realtimeData[14] . "V";

	}

	if ($tracer->getStatData()) {
		$json->stats = new stdClass();
		$json->stats->maxInputVoltageToday = $tracer->statData[0] . "V";
		$json->stats->minInputVoltageToday = $tracer->statData[1] . "V";
		$json->stats->maxBatteryVoltageToday = $tracer->statData[2] . "V";
		$json->stats->minBatteryVoltageToday = $tracer->statData[3] . "V";
		$json->stats->energyConsumedDay = $tracer->statData[4] . "kWh";
		$json->stats->energyConsumedMonth = $tracer->statData[5] . "kWh";
		$json->stats->energyConsumedYear = $tracer->statData[6] . "kWh";
		$json->stats->energyConsumedTotal = $tracer->statData[7] . "kWh";
		$json->stats->energyGeneratedDay = $tracer->statData[8] . "kWh";
		$json->stats->energyGeneratedMonth = $tracer->statData[9] . "kWh";
		$json->stats->energyGeneratedYear = $tracer->statData[10] . "kWh";
		$json->stats->energyGeneratedTotal = $tracer->statData[11] . "kWh";
		$json->stats->carbonDioxideReduction = $tracer->statData[12] . "T";
		$json->stats->netBatteryCurrent = $tracer->statData[13] . "A";
		$json->stats->temperatureBattery = $tracer->statData[14] . "℃";
		$json->stats->temperatureAmbient = $tracer->statData[15] . "℃";

	}

	$output = json_encode($json, JSON_PRETTY_PRINT);
	print $output
?>