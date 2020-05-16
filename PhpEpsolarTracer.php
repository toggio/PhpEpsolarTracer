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
 * This library connects via RS485 port to the
 * widely known Epsolar/Epever Tracer BN Series MPPT solar
 * charger controller allowing users to get data such as 
 * Battery Voltage, Load Current, Panel Power and so on...
 * 
 * Modbus register addresses are based on the following document:
 * http://www.solar-elektro.cz/data/dokumenty/1733_modbus_protocol.pdf
 *
 */
 
require_once 'PhpSerialModbus.php';

class PhpEpsolarTracer
{
	// Define names for "Rated data" registers
	public $ratedKey = Array (
		"PV array rated voltage", 			// 3000
		"PV array rated current",			// 3001
		"PV array rated power",				// 3002-3003
		"Battery rated voltage",			// 3004
		"Rated charging current",			// 3005
		"Rated charging power",				// 3006-3007
		"Charging Mode",					// 3008
		"Rated load current"				// 300E
	);
	
	// Define names for "Real-time data" and "Real-time status" registers
	public $realtimeKey = Array (
		"PV array voltage",     			// 3100
		"PV array current", 				// 3101
		"PV array power",        			// 3102-3103
		"Battery voltage",					// 3104 
		"Battery charging current",			// 3105
		"Battery charging power" , 			// 3106-3107
		"Load voltage",						// 310C
		"Load current",						// 310D
		"Load power",						// 310E-310F				
		"Battery temperature",				// 3110
		"Charger temperature", 				// 3111
		"Heat sink temperature",  			// 3112		
		"Battery SOC", 						// 311A
		"Remote battery temperature", 		// 311B
		"System rated voltage",  			// 311C
		"Battery status",					// 3200
		"Equipment status",					// 3201						
	);
	
	// Define names for "Statistical parameter" registers
	public $statKey = Array (
		"Max input voltage today",			// 3300
		"Min input voltage today",			// 3301
		"Max battery voltage today",		// 3302
		"Min battery voltage today",		// 3303
		"Consumed energy today",			// 3304-3305
		"Consumed energy this month",		// 3306-3307
		"Consumed energy this year",		// 3308-3309
		"Total consumed energy",			// 330A-330B
		"Generated energy today",			// 330C-330D
		"Generated energy this month",		// 330E-330F
		"Generated energy this year",		// 3310-3311
		"Total generated energy",			// 3312-3313
		"Carbon dioxide reduction",			// 3314-3315
		"Net battery current",				// 331B-331C
		"Battery temperature",				// 331D
		"Ambient temperature"				// 331E
	);
	
	// Define names for "Setting parameter" registers
	public $settingKey = Array (
		"Battery type",						// 9000
		"Battery capacity",					// 9001
		"Temperature compensation coeff.",	// 9002
		"High voltage disconnect",			// 9003
		"Charging limit voltage",			// 9004
		"Over voltage reconnect",			// 9005
		"Equalization voltage",				// 9006
		"Boost voltage",					// 9007
		"Float voltage",					// 9008
		"Boost reconnect voltage",			// 9009
		"Low voltage reconnect",			// 900A
		"Under voltage recover",			// 900B
		"Under voltage warning",			// 900C
		"Low voltage disconnect",			// 900D
		"Discharging limit voltage",		// 900E
		"Realtime clock (sec)",				// 9013
		"Realtime clock (min)",				// 9013
		"Realtime clock (hour)",			// 9014
		"Realtime clock (day)",				// 9014
		"Realtime clock (month)",			// 9015
		"Realtime clock (year)",			// 9015
		"Equalization charging cycle",		// 9016
		"Battery temp. warning hi limit",	// 9017
		"Battery temp. warning low limit", 	// 9018
		"Controller temp. hi limit",  		// 9019
		"Controller temp. hi limit rec.", 	// 901A
		"Components temp. hi limit",		// 901B
		"Components temp. hi limit rec.", 	// 901C
		"Line impedance",					// 901D
		"Night Time Threshold Volt",		// 901E
		"Light signal on delay time",		// 901F
		"Day Time Threshold Volt",			// 9020
		"Light signal off delay time",		// 9021
		"Load controlling mode",			// 903D
		"Working time length1 min.",		// 903E
		"Working time length1 hour",		// 903E
		"Working time length2 min.",		// 903F
		"Working time length2 hour",		// 903F
		"Turn on timing1 sec",				// 9042
		"Turn on timing1 min",				// 9043
		"Turn on timing1 hour",				// 9044
		"Turn off timing1 sec",				// 9045
		"Turn off timing1 min",				// 9046
		"Turn off timing1 hour",			// 9047
		"Turn on timing2 sec",				// 9048
		"Turn on timing2 min",				// 9049
		"Turn on timing2 hour",				// 904A
		"Turn off timing2 sec",				// 904B
		"Turn off timing2 min",				// 904C
		"Turn off timing2 hour",			// 904D,							
		"Length of night min.",				// 9065
		"Length of night hour",				// 9065
		"Battery rated voltage code",		// 9067
		"Load timing control selection",	// 9069
		"Default Load On/Off",  			// 906A
		"Equalize duration",				// 906B
		"Boost duration",					// 906C
		"Dischargning percentage",			// 906D
		"Charging percentage",				// 906E							
		"Management mode"					// 9070		
	);
	
	// Define name for "Info data" registers
	public $infoKey = Array (
		"Manufacturer",
		"Model",
		"Version"
	);
	
	// Define name for "Coil data" registers
	public $coilKey = Array (
		"Manual control the load",			// 2
		"Enable load test mode",			// 5
		"Force the load on/off"				// 6
	);
	
	// Define name for "Discrete Data" registers
	public $discreteKey = Array (
		"Over temperature inside device",	// 2000
		"Day/Night"							// 200C
	);
	
	// Define data units
	public $ratedSym = Array ("V","A","W","V","A","W","","A");
	public $realtimeSym = Array ("V","A","W","V","A","W","V","A","W","°C","°C","°C","%","°C","V","","");
	public $statSym = Array ("V","V","V","V","KWH","KWH","KWH","KWH","KWH","KWH","KWH","KWH","T","A","°C","°C");
	public $settingSym = Array("","Ah","mV/°C/2V","V","V","V","V","V","V","V","V","V","V","V","V","","","","","",""," day","°C","°C","°C","°C","°C","°C","mOhm","V"," min.","V"," min.","","","","","","","","","","","","","","","","","","","","","",""," min."," min.","%","%","");
	
	// Define data dividers
	private $ratedDiv = Array (100,100,100,100,100,100,1,100);
	private $realtimeDiv = Array (100,100,100,100,100,100,100,100,100,100,100,100,1,100,100,1,1);
	private $statDiv= Array (100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100);
	private $settingDiv = Array (1,1,100,100,100,100,100,100,100,100,100,100,100,100,100,1,1,1,1,1,1,1,100,100,100,100,100,100,100,100,1,100,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
	
	// Declared data arrays
	public $ratedData = Array ();
	public $realtimeData = Array ();
	public $statData = Array ();
	public $settingData = Array ();
		
	public $infoData = Array ();	
	public $coilData = Array ();
	public $discreteData = Array ();
	
	// Initialize serial communication
	public function __construct($port='/dev/ttyUSB0') {
		$this->tracer = new PhpSerialModbus;
		if (php_sapi_name() == "cli") file_exists($port) or die("Cannot open serial port $port\n");
		$this->tracer->deviceInit($port,115200,'none',8,1,'none');
		$this->tracer->deviceOpen();
		// $this->tracer->debug=true;
	}
	
	public function __destruct() {
		$this->tracer->deviceClose();
	}
	
	// Convert data received from Modbus
	// where $doublebytes is an array with indexes of value that needs four bytes
	// instead of just two and $negbytes contains indexes of value that could be negative
	private function convertData($respArray,$doubleBytes=Array(),$negBytes=Array()) {
		$resultArray=[];
			// Iterate response array and aggregate Lo and Hi byte
			for ($i = 0; $i<(count($respArray)/2); $i++) {
				$data=$respArray[$i*2].$respArray[$i*2+1];
				if (in_array($i,$negBytes)) $negByte=true; else $negByte=false;
				// echo "0x".$data."\n";
				if ($negByte && ("0x".$data>0x7FFF)) {
					$data=dechex(0xFFFF-("0x".$data));
					$neg=true;
				} else $neg=false;
				// echo "0x".$data."\n";
				// If we need two bytes more, add them and increase counter
				if (in_array($i,$doubleBytes)) {
					$dataH=$respArray[$i*2+2].$respArray[$i*2+3];
					if ($negByte && ("0x".$dataH>0x7FFF)) $dataH=dechex(0xFFFF-("0x".$dataH));
					// echo $dataH."-".$data."\n";
					$data=$dataH.$data;
					$i++;
				}
			$resultArray[] = (!$neg) ? hexdec($data) : -hexdec($data);
			// echo $i.",".hexdec($data)."\n";
			}		
	return $resultArray;
	}	
	
	// Convert two bytes response to single byte (for some responses)
	private function convert8bit ($inputDtArray) {
		$outputDtArray=[];
		for ($i = 0; $i<(count($inputDtArray)); $i++) {
			$outputDtArray[$i*2] = (($inputDtArray[$i] >> 0) & 0xff);
			// echo ($inputDtArray[$i])."\n";
			$outputDtArray[$i*2+1] = (($inputDtArray[$i] >> 8) & 0xff);		
		}
		return $outputDtArray;
	}
	
	// Remove unused Indexes from array
	private function removeUnused (&$inputArray, $inputIndexes) {
		for ($i = 0; $i<(count($inputIndexes)); $i++) {
			array_splice($inputArray,$inputIndexes[$i]-$i,1);
		}
	}
	
	private function divide ($a, $b) {
		return $a/$b;
	}
		
	// Send query and get response for "Info data"
	public function getInfoData() {
		$this->tracer->sendRawQuery("\x01\x2b\x0e\x01\x00\x70\x77",false);
		$result=$this->tracer->getResponse(true);
		if ( (!$result) && (php_sapi_name() == "cli") ) die("Timeout on reading from serial port\n");
		// $escaped = addcslashes($result, "\0..\37!@\177..\377");
		// print $escaped."\n";
		// print preg_replace( '/[^[:print:]]/', '.',$result);
		$string = preg_replace('/[\f\r]/u', ' - ', $result);
		$string = preg_replace('/[^A-Za-z0-9 _\-\+\&.,]/','',$string)."\n";
		$this->infoData = explode(" - ",substr($string,1));
		if (!$result) return 0;
		return 1;
	}
	
	// Send query and get response for "Rated data"
	public function getRatedData() {
		$this->tracer->sendRawQuery("\x01\x43\x30\x00\x00\x0f\x0b\x01",false);
		$result = $this->tracer->getResponse(false,2,10);
		if (!$result) return 0;					   
		$this->ratedData = $this->convertData($result,array(2,6));
		$this->ratedData = array_map(array($this,'divide'),$this->ratedData,$this->ratedDiv);
		if (count($this->ratedData) != 8) return 0;		
		return 1;			
	}
	
	// Send query and get response for "Real-time data and status"
	public function getRealtimeData() {
		$this->tracer->sendRawQuery("\x01\x43\x31\x00\x00\x76\xcb\x1f",false);
		$result1 = $this->tracer->getResponse(false,15,190);
		if (!$result1) return 0;				
		$this->tracer->sendRawQuery("\x01\x43\x32\x00\x00\x04\x4b\x7e",false);
		$result2=$this->tracer->getResponse(false,1,2);
		if (!$result2) return 0;			
		$result=array_merge($result1,$result2);		
		$this->realtimeData = $this->convertData($result,array(2,6,10,14),array(16,17,18,19,21));
		$this->removeUnused($this->realtimeData,array(9,10,11,15,21));
		$this->realtimeData = array_map(array($this,'divide'),$this->realtimeData,$this->realtimeDiv);
		if (count($this->realtimeData) != 17) return 0;
		return 1;			
	}
	
	// Send query and get response for "Statistical parameter"
	public function getStatData() {
		$this->tracer->sendRawQuery("\x01\x43\x33\x00\x00\x76\xca\xa7",false);
		$result = $this->tracer->getResponse(false,15,174);
		if (!$result) return 0;					   
		$this->statData = $this->convertData($result,array(4,6,8,10,12,14,16,18,20,22,24,27),array(27,29,30));
		$this->removeUnused($this->statData,array(13,14,15));
		$this->statData = array_map(array($this,'divide'),$this->statData,$this->statDiv);
		if (count($this->statData) != 16) return 0;
		return 1;			
	}
	
	// Send query and get response for "Settings parameter"
	public function getSettingData() {
		$this->tracer->sendRawQuery("\x01\x43\x90\x00\x00\x76\xe8\xe3",false);
		$result = $this->tracer->getResponse(false,15,124);
		if (!$result) return 0;					   
		$this->settingData = $this->convertData($result,array(),array(20));
		// print_r($this->settingData);
		$dtArray=$this->convert8bit(array_slice($this->settingData,15,3));
		array_splice($this->settingData, 15, 3, $dtArray);
		// print_r($this->settingData);
		$dtArray=$this->convert8bit(array_slice($this->settingData,34,2));
		array_splice($this->settingData, 34, 2, $dtArray);
		// print_r($this->settingData);
		$dtArray=$this->convert8bit(array_slice($this->settingData,51,1));
		array_splice($this->settingData, 51, 1, $dtArray);
		// print_r($this->settingData);		
		$this->removeUnused($this->settingData,array(50,60));
		$this->settingData = array_map(array($this,'divide'),$this->settingData,$this->settingDiv);
		if (count($this->settingData) != 60) return 0;
		return 1;			
	}

	// Send query and get response for "Coils"
	public function getCoilData() {
		$this->tracer->sendRawQuery("\x01\x01\x00\x02\x00\x04\x9c\x09",false);
		$result = $this->tracer->getResponse(false);
		if (!$result) return 0;
		$this->coilData[0] = $result[0] & 1;
		$this->coilData[1] = ($result[0] >> 3) & 1; 
		$this->coilData[2] = ($result[0] >> 4) & 1;
		return 1;
	}
	
	// Send query and get response for "Discrete input"
	public function getDiscreteData () {
		$this->tracer->sendRawQuery("\x01\x02\x20\x00\x00\x01\xb2\x0a",false);
		$result1 = $this->tracer->getResponse(false);
		if (!$result1) return 0;				
		$this->tracer->sendRawQuery("\x01\x02\x20\x0c\x00\x01\x72\x09",false);
		$result2=$this->tracer->getResponse(false);
		if (!$result2) return 0;			
		$result = array_merge($result1,$result2);
		$this->discreteData[0] = $result[0] & 1;
		$this->discreteData[1] = $result[1] & 1;
		if (count($this->discreteData) != 2) return 0;
		return 1;
	}
}