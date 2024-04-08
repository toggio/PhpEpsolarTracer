Php EpSolar Tracer Class
======

Library for communicating with Epsolar/Epever Tracer BN MPPT Solar Charger Controller

Features
-------
This library connects via RS485 port to the widely known Epsolar/Epever Tracer BN Series MPPT solar charger controller (like mine Tracer 2215 BN) allowing users to get data such as Battery Voltage, Load Current, Panel Power and base on the [Tracer protocol] [protocol] (Modbus).
In order to get it to work you just need tu use a cheap USB/RS485 converter and connect one side to your PC/Raspberry USB port and the other to the solar charger's connector.

Class methods and properties
-------
For better understanding take a look at the "Quick start example"

**getInfoData()** , **getRatedData()** , **getRealtimeData()** , **getStatData()** , **getSettingData()** , **getCoilData()** , **getDiscreteData()**
>These functions get the various data from solar charger and put them in their rispective data arrays. The returned value is TRUE if data received or FALSE if not

**$infoData** , **$ratedData** , **$realtimeData** , **$statData** , **$settingData** , **$coilData** , **$dicreteData**
>These arrays are populated after calling the respective function with the data received from solar charger. The data acquired are rispectively: Info Registers, Rated Data Registers, Real-time Data/Status Registers, Statistical Data Registers, Settings Parameter Registers, Coils Registers, Discrete Input Registers. For example $realtimeData[3] could be 12.23 (Volts). These data are updated everytime you call the connected method, i.e. for updating $realtimeData you have to call getRatedData()

**$infoKey** , **$ratedKey** , **$realtimeKey** , **statKey** , **$settingKey** , **$coilKey** , **$discreteKey**
>These fixed arrays contains the "Key" (the label) of the specific data. For example $realtimeKey[3] is "Battery voltage" 

**$ratedSym** , **$realtimeSym** , **$statSym** , **$settingSym**
>This fixed array contains the "Symbol" conected to the value. For example $realtimeSym[3] is "V" (Volts) 


Quick start example (example_cli.php)
------
This example will print all datas received from solar  charger
```php
<?php
require_once 'PhpEpsolarTracer.php';

$tracer = new PhpEpsolarTracer('/dev/ttyUSB0');

if ($tracer->getInfoData()) {
	print "Info Data\n";
	print "----------------------------------\n";
	for ($i = 0; $i < count($tracer->infoData); $i++)
		print str_pad($i, 2, '0', STR_PAD_LEFT)." ".$tracer->infoKey[$i].": ".$tracer->infoData[$i]."\n";
	} else print "Cannot get Info Data\n";

if ($tracer->getRatedData()) {
	print "Rated Data\n";
	print "----------------------------------\n";
	for ($i = 0; $i < count($tracer->ratedData); $i++)
		print str_pad($i, 2, '0', STR_PAD_LEFT)." ".$tracer->ratedKey[$i].": ".$tracer->ratedData[$i].$tracer->ratedSym[$i]."\n";
	} else print "Cannot get Rated Data\n";

if ($tracer->getRealtimeData()) {
	print "\nRealTime Data\n";
	print "----------------------------------\n";
	for ($i = 0; $i < count($tracer->realtimeData); $i++)
		print str_pad($i, 2, '0', STR_PAD_LEFT)." ".$tracer->realtimeKey[$i].": ".$tracer->realtimeData[$i].$tracer->realtimeSym[$i]."\n";
	} else print "Cannot get RealTime Data\n";

if ($tracer->getStatData()) {
	print "\nStatistical Data\n";
	print "----------------------------------\n";
	for ($i = 0; $i < count($tracer->statData); $i++)
		print str_pad($i, 2, '0', STR_PAD_LEFT)." ".$tracer->statKey[$i].": ".$tracer->statData[$i].$tracer->statSym[$i]."\n";
	} else print "Cannot get Statistical Data\n";
	
if ($tracer->getSettingData()) {
	print "\nSettings Data\n";
	print "----------------------------------\n";
	for ($i = 0; $i < count($tracer->settingData); $i++)
		print str_pad($i, 2, '0', STR_PAD_LEFT)." ".$tracer->settingKey[$i].": ".$tracer->settingData[$i].$tracer->settingSym[$i]."\n";
	} else print "Cannot get Settings Data\n";

if ($tracer->getCoilData()) {
	print "\nCoils Data\n";
	print "----------------------------------\n";
	for ($i = 0; $i < count($tracer->coilData); $i++)
		print str_pad($i, 2, '0', STR_PAD_LEFT)." ".$tracer->coilKey[$i].": ".$tracer->coilData[$i]."\n";
	} else print "Cannot get Coil Data\n";

if ($tracer->getDiscreteData()) {
	print "\nDiscrete Data\n";
	print "----------------------------------\n";
	for ($i = 0; $i < count($tracer->discreteData); $i++)
		print str_pad($i, 2, '0', STR_PAD_LEFT)." ".$tracer->discreteKey[$i].": ".$tracer->discreteData[$i]."\n";
	} else print "Cannot get Discrete Data\n";
?>
```
and will produce the following output on my solar charger:

```SH
Info Data
----------------------------------
00 Manufacturer: EPsolar Tech co., Ltd
01 Model: Tracer2215BN
02 Version: V02.13+V07.24

Rated Data
----------------------------------
00 PV array rated voltage: 150V
01 PV array rated current: 20A
02 PV array rated power: 520W
03 Battery rated voltage: 24V
04 Rated charging current: 20A
05 Rated charging power: 520W
06 Charging Mode: 2
07 Rated load current: 20A

RealTime Data
----------------------------------
00 PV array voltage: 13.37V
01 PV array current: 0.01A
02 PV array power: 0.24W
03 Battery voltage: 12.34V
04 Battery charging current: 0.02A
05 Battery charging power: 0.24W
06 Load voltage: 12.34V
07 Load current: 0.64A
08 Load power: 7.89W
09 Battery temperature: 25°C
10 Charger temperature: 20.45°C
11 Heat sink temperature: 20.45°C
12 Battery SOC: 12%
13 Remote battery temperature: 25°C
14 System rated voltage: 12V
15 Battery status: 0
16 Equipment status: 11

Statistical Data
----------------------------------
00 Max input voltage today: 88.36V
01 Min input voltage today: 1.73V
02 Max battery voltage today: 15.5V
03 Min battery voltage today: 12.01V
04 Consumed energy today: 0.15KWH
05 Consumed energy this month: 2.39KWH
06 Consumed energy this year: 12.81KWH
07 Total consumed energy: 13.4KWH
08 Generated energy today: 0.26KWH
09 Generated energy this moth: 3.56KWH
10 Generated energy this year: 16.55KWH
11 Total generated energy: 17.01KWH
12 Carbon dioxide reduction: 0.01T
13 Net battery current: -0.6A
14 Battery temperature: 25°C
15 Ambient temperature: 25°C

Settings Data
----------------------------------
00 Battery type: 0
01 Battery capacity: 50Ah
02 Temperature compensation coeff.: 3mV/°C/2V
03 High voltage disconnect: 16V
04 Charging limit voltage: 15V
05 Over voltage reconnect: 15V
06 Equalization voltage: 14.6V
07 Boost voltage: 14.4V
08 Float voltage: 13.8V
09 Boost reconnect voltage: 13.2V
10 Low voltage reconnect: 12.9V
11 Under voltage recover: 12.2V
12 Under voltage warning: 12V
13 Low voltage disconnect: 11.4V
14 Discharging limit voltage: 11V
15 Realtime clock (sec): 25
16 Realtime clock (min): 26
17 Realtime clock (hour): 18
18 Realtime clock (day): 13
19 Realtime clock (month): 3
20 Realtime clock (year): 16
21 Equalization charging cycle: 30 day
22 Battery temp. warning hi limit: 65°C
23 Battery temp. warning low limit: -39.99°C
24 Controller temp. hi limit: 85°C
25 Controller temp. hi limit rec.: 75°C
26 Components temp. hi limit: 85°C
27 Components temp. hi limit rec.: 75°C
28 Line impedance: 0mOhm
29 Night Time Threshold Volt: 5V
30 Light signal on delay time: 10 min.
31 Day Time Threshold Volt: 6V
32 Light signal off delay time: 10 min.
33 Load controlling mode: 0
34 Working time length1 min.: 0
35 Working time length1 hour: 1
36 Working time length2 min.: 0
37 Working time length2 hour: 1
38 Turn on timing1 sec: 0
39 Turn on timing1 min: 0
40 Turn on timing1 hour: 19
41 Turn off timing1 sec: 0
42 Turn off timing1 min: 0
43 Turn off timing1 hour: 6
44 Turn on timing2 sec: 0
45 Turn on timing2 min: 0
46 Turn on timing2 hour: 19
47 Turn off timing2 sec: 0
48 Turn off timing2 min: 0
49 Turn off timing2 hour: 6
50 Length of night min.: 27
51 Length of night hour: 11
52 Battery rated voltage code: 0
53 Load timing control selection: 0
54 Default Load On/Off: 1
55 Equalize duration: 120 min.
56 Boost duration: 120 min.
57 Dischargning percentage: 80%
58 Charging percentage: 100%
59 Management mode: 0

Coils Data
----------------------------------
00 Manual control the load: 1
01 Enable load test mode: 0
02 Force the load on/off: 0

Discrete Data
----------------------------------
00 Over temperature inside device: 00
01 Day/Night: 00
Info Data
----------------------------------
00 Manufacturer: EPsolar Tech co., Ltd
01 Model: Tracer2215BN
02 Version: V02.13+V07.24

Rated Data
----------------------------------
00 PV array rated voltage: 150V
01 PV array rated current: 20A
02 PV array rated power: 520W
03 Battery rated voltage: 24V
04 Rated charging current: 20A
05 Rated charging power: 520W
06 Charging Mode: 2
07 Rated load current: 20A

RealTime Data
----------------------------------
00 PV array voltage: 13.39V
01 PV array current: 0.02A
02 PV array power: 0.37W
03 Battery voltage: 12.34V
04 Battery charging current: 0.03A
05 Battery charging power: 0.37W
06 Load voltage: 12.34V
07 Load current: 0.65A
08 Load power: 8.02W
09 Battery temperature: 25°C
10 Charger temperature: 20.44°C
11 Heat sink temperature: 20.44°C
12 Battery SOC: 12%
13 Remote battery temperature: 25°C
14 System rated voltage: 12V
15 Battery status: 0
16 Equipment status: 11

Statistical Data
----------------------------------
00 Max input voltage today: 88.36V
01 Min input voltage today: 1.73V
02 Max battery voltage today: 15.5V
03 Min battery voltage today: 12.01V
04 Consumed energy today: 0.15KWH
05 Consumed energy this month: 2.39KWH
06 Consumed energy this year: 12.81KWH
07 Total consumed energy: 13.4KWH
08 Generated energy today: 0.26KWH
09 Generated energy this moth: 3.56KWH
10 Generated energy this year: 16.55KWH
11 Total generated energy: 17.01KWH
12 Carbon dioxide reduction: 0.01T
13 Net battery current: -0.61A
14 Battery temperature: 25°C
15 Ambient temperature: 25°C

Settings Data
----------------------------------
00 Battery type: 0
01 Battery capacity: 50Ah
02 Temperature compensation coeff.: 3mV/°C/2V
03 High voltage disconnect: 16V
04 Charging limit voltage: 15V
05 Over voltage reconnect: 15V
06 Equalization voltage: 14.6V
07 Boost voltage: 14.4V
08 Float voltage: 13.8V
09 Boost reconnect voltage: 13.2V
10 Low voltage reconnect: 12.9V
11 Under voltage recover: 12.2V
12 Under voltage warning: 12V
13 Low voltage disconnect: 11.4V
14 Discharging limit voltage: 11V
15 Realtime clock (sec): 26
16 Realtime clock (min): 26
17 Realtime clock (hour): 18
18 Realtime clock (day): 13
19 Realtime clock (month): 3
20 Realtime clock (year): 16
21 Equalization charging cycle: 30 day
22 Battery temp. warning hi limit: 65°C
23 Battery temp. warning low limit: -39.99°C
24 Controller temp. hi limit: 85°C
25 Controller temp. hi limit rec.: 75°C
26 Components temp. hi limit: 85°C
27 Components temp. hi limit rec.: 75°C
28 Line impedance: 0mOhm
29 Night Time Threshold Volt: 5V
30 Light signal on delay time: 10 min.
31 Day Time Threshold Volt: 6V
32 Light signal off delay time: 10 min.
33 Load controlling mode: 0
34 Working time length1 min.: 0
35 Working time length1 hour: 1
36 Working time length2 min.: 0
37 Working time length2 hour: 1
38 Turn on timing1 sec: 0
39 Turn on timing1 min: 0
40 Turn on timing1 hour: 19
41 Turn off timing1 sec: 0
42 Turn off timing1 min: 0
43 Turn off timing1 hour: 6
44 Turn on timing2 sec: 0
45 Turn on timing2 min: 0
46 Turn on timing2 hour: 19
47 Turn off timing2 sec: 0
48 Turn off timing2 min: 0
49 Turn off timing2 hour: 6
50 Length of night min.: 27
51 Length of night hour: 11
52 Battery rated voltage code: 0
53 Load timing control selection: 0
54 Default Load On/Off: 1
55 Equalize duration: 120 min.
56 Boost duration: 120 min.
57 Dischargning percentage: 80%
58 Charging percentage: 100%
59 Management mode: 0

Coils Data
----------------------------------
00 Manual control the load: 1
01 Enable load test mode: 0
02 Force the load on/off: 0

Discrete Data
----------------------------------
00 Over temperature inside device: 00
01 Day/Night: 00

Voltage: 12.14
```
The number at the beginning of every line rapresent the array index

Note
------
If you use this class in HTTPD and not CLI don't forget to give the user the permssion to use serial port (for example with Apache on Debian: usermod -a -G dialout www-data)

Contributors
--------
This project is developed by Luca Soltoggio

http://arduinoelectronics.wordpress.com/ ~ http://minibianpi.wordpress.com

PhpSerial by Rémy Sanchez and Rizwan Kassim

## Help us

If you find this project useful and would like to support its development, consider making a donation. Any contribution is greatly appreciated!

**Bitcoin (BTC) Address:** `3Ctmurhy18PmkTKPa2s7PjfAKzR8ZBj8Na`

License
------
Copyright (C) 2016 Luca Soltoggio

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.

[//]: #

   [protocol]: <http://www.solar-elektro.cz/data/dokumenty/1733_modbus_protocol.pdf>
   
