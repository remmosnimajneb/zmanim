<?php

	/**
	 * Calculate Sunrise and Sunset
	 * Copied from NOAA Solar Calculator
	 * Find Sunrise, Sunset, Solar Noon and Solar Position for Any Place on Earth
	 * https://gml.noaa.gov/grad/solcalc/main.js
	 * 
	 * Author: Benjamin Sommer (@remmosnimajneb), 2021
	 */
	class NOAA_SunriseSetCalculator {

			private $Day, $Month, $Year, $Hours, $Minutes, $Seconds, $Lat, $Long, $TimeZoneName;

			private $monthList = array(
						array("name"=> "January",   "numdays"=> 31, "abbr"=> "Jan"),
						array("name"=> "February",  "numdays"=> 28, "abbr"=> "Feb"),
						array("name"=> "March",     "numdays"=> 31, "abbr"=> "Mar"),
						array("name"=> "April",     "numdays"=> 30, "abbr"=> "Apr"),
						array("name"=> "May",       "numdays"=> 31, "abbr"=> "May"),
						array("name"=> "June",      "numdays"=> 30, "abbr"=> "Jun"),
						array("name"=> "July",      "numdays"=> 31, "abbr"=> "Jul"),
						array("name"=> "August",    "numdays"=> 31, "abbr"=> "Aug"),
						array("name"=> "September", "numdays"=> 30, "abbr"=> "Sep"),
						array("name"=> "October",   "numdays"=> 31, "abbr"=> "Oct"),
						array("name"=> "November",  "numdays"=> 30, "abbr"=> "Nov"),
						array("name"=> "December",  "numdays"=> 31, "abbr"=> "Dec")
					);

			/*
			* Default Constructor
			*/
				function __construct(){

					$this->Day = date("d");
					$this->Month = date("m");
					$this->Year = date("Y");

					$this->Hours = date("H");
					$this->Minutes = date("i");
					$this->Seconds = date("s");

					/* Default Location is Manhatten becuase #WeLoveNYC */
						$this->Lat = 40.754932;
						$this->Long = -73.984016;
						$this->TimeZoneName = "America/New_York";

				}


		/*************************************************************/
		/*                          GETTERS                          */
		/*************************************************************/

			/*
			* Return (int) Day Number
			*/
			public function getDay(){
				return $this->Day;
			}

			/*
			* Return (int) Month Number
			*/
			public function getMonth(){
				return $this->Month;
			}

			/*
			* Return (int) - 4 Digit Year
			*/
			public function getYear(){
				return $this->Year;
			}

			/*
			* Return (int) 24 hours Hour
			*/
			public function getHours(){
				return $this->Hours;
			}

			/*
			* Return (int) Minutes
			*/
			public function getMinutes(){
				return $this->Minutes;
			}

			/*
			* Return (int) Seconds
			*/
			public function getSeconds(){
				return $this->Seconds;
			}

			/*
			* Returns (double) - $Lat
			*/
			public function getLat(){
				return $this->Lat;
			}

			/*
			* Return (double) - $Long
			*/
			public function getLong(){
				return $this->Long;
			}

			/*
			* Return (String) TimeZone
			*/
			public function getTimeZone(){
				return $this->TimeZoneName;
			}

		/*************************************************************/
		/*                          SETTERS                          */
		/*************************************************************/

			/*
			* Set Day
			*/
			public function setDay($N_Day){
				$this->Day = $N_Day;
			}

			/*
			* Set Month
			*/
			public function setMonth($N_Month){
				$this->Month = $N_Month;
			}

			/*
			* Set Year
			*/
			public function setYear($N_Year){
				$this->Year = $N_Year;
			}

			/*
			* Set Hours
			*/
			public function setHours($N_Hours){
				$this->Hours = $N_Hours;
			}

			/*
			* Set Minutes
			*/
			public function setMinutes($N_Minutes){
				$this->Minutes = $N_Minutes;
			}

			/*
			* Set Seconds
			*/
			public function setSeconds($N_Seconds){
				$this->Seconds = $N_Seconds;
			}

			/*
			* Set Lat
			*/
			public function setLat($N_Lat){
				$this->Lat = $N_Lat;
			}

			/*
			* Set Long
			*/
			public function setLong($N_Long){
				$this->Long = $N_Long;
			}

			/*
			* Return String TimeZone
			*/
			public function setTimeZone($N_TimeZone){
				$this->TimeZoneName = $N_TimeZone;
			}

		/*************************************************************/
		/* Solar Position calculation functions */
		/*************************************************************/

		function calcTimeJulianCent($jd) {
			$T = ($jd - 2451545.0)/36525.0;
			return $T;
		}

		function calcJDFromJulianCent($t) {
			$JD = $t * 36525.0 + 2451545.0;
			return $JD;
		}

		function isLeapYear($yr) {
			return (($yr % 4 == 0 && $yr % 100 != 0) || $yr % 400 == 0);
		}

		function calcDateFromJD($jd) {
			$z = floor($jd + 0.5);
			$f = ($jd + 0.5) - $z;
			if ($z < 2299161) {
				$A = $z;
			} else {
				$alpha = floor(($z - 1867216.25)/36524.25);
				$A = $z + 1 + $alpha - floor($alpha/4);
			}
			$B = $A + 1524;
			$C = floor(($B - 122.1)/365.25);
			$D = floor(365.25 * $C);
			$E = floor(($B - $D)/30.6001);
			$day = $B - $D - floor(30.6001 * $E) + $f;
			$month = ($E < 14) ? $E - 1 : $E - 13;
			$year = ($month > 2) ? $C - 4716 : $C - 4715;

			$R = new stdClass();

				$R->year = $year;
				$R->month = $month;
				$R->day = $day;

			return $R;
		}

		function calcDoyFromJD($jd) {
			$date = $this->calcDateFromJD($jd);

			$k = (isLeapYear($date.year) ? 1 : 2);
			$doy = floor((275 * $date.month)/9) - $k * floor(($date.month + 9)/12) + $date.day -30;

			return $doy;
		}


		function radToDeg($angleRad) {
			return (180.0 * $angleRad / pi());
		}

		function degToRad($angleDeg) {
			return (pi() * $angleDeg / 180.0);
		}

		function calcGeomMeanLongSun($t) {
			$L0 = 280.46646 + $t * (36000.76983 + $t*(0.0003032));
			while($L0 > 360.0) {
				$L0 -= 360.0;
			}
			while($L0 < 0.0) {
				$L0 += 360.0;
			}
			return $L0;		// in degrees
		}

		function calcGeomMeanAnomalySun($t) {
			$M = 357.52911 + $t * (35999.05029 - 0.0001537 * $t);
			return $M;		// in degrees
		}

		function calcEccentricityEarthOrbit($t) {
			$e = 0.016708634 - $t * (0.000042037 + 0.0000001267 * $t);
			return $e;		// unitless
		}

		function calcSunEqOfCenter($t) {
			$m = $this->calcGeomMeanAnomalySun($t);
			$mrad = $this->degToRad($m);
			$sinm = sin($mrad);
			$sin2m = sin($mrad+$mrad);
			$sin3m = sin($mrad+$mrad+$mrad);
			$C = $sinm * (1.914602 - $t * (0.004817 + 0.000014 * $t)) + $sin2m * (0.019993 - 0.000101 * $t) + $sin3m * 0.000289;
			return $C;		// in degrees
		}

		function calcSunTrueLong($t) {
			$l0 = $this->calcGeomMeanLongSun($t);
			$c = $this->calcSunEqOfCenter($t);
			$O = $l0 + $c;
			return $O;		// in degrees
		}

		function calcSunTrueAnomaly($t) {
			$m = $this->calcGeomMeanAnomalySun($t);
			$c = $this->calcSunEqOfCenter($t);
			$v = $m + $c;
			return $v;		// in degrees
		}

		function calcSunRadVector($t) {
			$v = $this->calcSunTrueAnomaly($t);
			$e = $this->calcEccentricityEarthOrbit($t);
			$R = (1.000001018 * (1 - $e * $e)) / (1 + $e * cos($this->degToRad($v)));
			return $R;		// in AUs
		}

		function calcSunApparentLong($t) {
			$o = $this->calcSunTrueLong($t);
			$omega = 125.04 - 1934.136 * $t;
			$lambda = $o - 0.00569 - 0.00478 * sin($this->degToRad($omega));
			return $lambda;		// in degrees
		}

		function calcMeanObliquityOfEcliptic($t) {
			$seconds = 21.448 - $t*(46.8150 + $t*(0.00059 - $t*(0.001813)));
			$e0 = 23.0 + (26.0 + ($seconds/60.0))/60.0;
			return $e0;		// in degrees
		}

		function calcObliquityCorrection($t) {
			$e0 = $this->calcMeanObliquityOfEcliptic($t);
			$omega = 125.04 - 1934.136 * $t;
			$e = $e0 + 0.00256 * cos($this->degToRad($omega));
			return $e;		// in degrees
		}

		function calcSunRtAscension($t) {
			$e = $this->calcObliquityCorrection($t);
			$lambda = $this->calcSunApparentLong($t);
			$tananum = (cos($this->degToRad($e)) * sin($this->degToRad($lambda)));
			$tanadenom = (cos($this->degToRad($lambda)));
			$alpha = $this->radToDeg(atan2($tananum, $tanadenom));
			return $alpha;		// in degrees
		}

		function calcSunDeclination($t) {
			$e = $this->calcObliquityCorrection($t);
			$lambda = $this->calcSunApparentLong($t);
			$sint = sin($this->degToRad($e)) * sin($this->degToRad($lambda));
			$theta = $this->radToDeg(asin($sint));
			return $theta;		// in degrees
		}

		function calcEquationOfTime($t) {
			$epsilon = $this->calcObliquityCorrection($t);
			$l0 = $this->calcGeomMeanLongSun($t);
			$e = $this->calcEccentricityEarthOrbit($t);
			$m = $this->calcGeomMeanAnomalySun($t);

			$y = tan($this->degToRad($epsilon)/2.0);
			$y *= $y;

			$sin2l0 = sin(2.0 * $this->degToRad($l0));
			$sinm   = sin($this->degToRad($m));
			$cos2l0 = cos(2.0 * $this->degToRad($l0));
			$sin4l0 = sin(4.0 * $this->degToRad($l0));
			$sin2m  = sin(2.0 * $this->degToRad($m));

			$Etime = $y * $sin2l0 - 2.0 * $e * $sinm + 4.0 * $e * $y * $sinm * $cos2l0 - 0.5 * $y * $y * $sin4l0 - 1.25 * $e * $e * $sin2m;
			return $this->radToDeg($Etime)*4.0;	// in minutes of time
		}

		function calcHourAngleSunrise($lat, $solarDec) {
			$latRad = $this->degToRad($lat);
			$sdRad  = $this->degToRad($solarDec);
			$HAarg = (cos($this->degToRad(90.833))/(cos($latRad)*cos($sdRad))-tan($latRad) * tan($sdRad));
			$HA = acos($HAarg);
			return $HA;		// in radians (for sunset, use -HA)
		}

		function isNumber($inputVal) {
			$oneDecimal = false;
			$inputStr = "" + $inputVal;
			for ($i = 0; i < strlen($inputStr); $i++) {
				$oneChar = $inputStr[$i];
				if ($i == 0 && ($oneChar == "-" || $oneChar == "+")) {
					continue;
				}
				if ($oneChar == "." && !$oneDecimal) {
					$oneDecimal = true;
					continue;
				}
				if ($oneChar < "0" || $oneChar > "9") {
					return false;
				}
			}
			return true;
		}

		function getJD($year, $month, $day) {

			if ($month <= 2) {
				$year -= 1;
				$month += 12;
			}
			$A = floor($year/100);
			$B = 2 - $A + floor($A/4);
			$JD = floor(365.25*($year + 4716)) + floor(30.6001*($month+1)) + $day + $B - 1524.5;
			return $JD;
		}

		function calcRefraction($elev) {

			if ($elev > 85.0) {
				$correction = 0.0;
			} else {
				$te = tan($this->degToRad($elev));
				if ($elev > 5.0) {
					$correction = 58.1 / $te - 0.07 / ($te*$te*$te) + 0.000086 / ($te*$te*$te*$te*$te);
				} else if ($elev > -0.575) {
					$correction = 1735.0 + $elev * (-518.2 + $elev * (103.4 + $elev * (-12.79 + $elev * 0.711) ) );
				} else {
					$correction = -20.774 / $te;
				}
				$correction = $correction / 3600.0;
			}

			return $correction;
		}

		function calcAzEl($T, $localtime, $latitude, $longitude, $zone) {

			$eqTime = $this->calcEquationOfTime($T);
			$theta  = $this->calcSunDeclination($T);

			$solarTimeFix = $eqTime + 4.0 * $longitude - 60.0 * $zone;
			$earthRadVec = $this->calcSunRadVector($T);
			$trueSolarTime = $localtime + $solarTimeFix;
			while ($trueSolarTime > 1440) {
				$trueSolarTime -= 1440;
			}
			$hourAngle = $trueSolarTime / 4.0 - 180.0;
			if ($hourAngle < -180) {
				$hourAngle += 360.0;
			}
			$haRad = $this->degToRad($hourAngle);
			$csz = sin($this->degToRad($latitude)) * sin($this->degToRad($theta)) + cos($this->degToRad($latitude)) * cos($this->degToRad($theta)) * cos($haRad);
			if ($csz > 1.0) {
				$csz = 1.0;
			} else if ($csz < -1.0) { 
				$csz = -1.0;
			}
			$zenith = $this->radToDeg(acos($csz));
			$azDenom = ( cos($this->degToRad($latitude)) * sin($this->degToRad($zenith)) );
			if (abs($azDenom) > 0.001) {
				$azRad = (( sin($this->degToRad($latitude)) * cos($this->degToRad($zenith)) ) - sin($this->degToRad($theta))) / $azDenom;
				if (abs($azRad) > 1.0) {
					if ($azRad < 0) {
						$azRad = -1.0;
					} else {
						$azRad = 1.0;
					}
				}
				$azimuth = 180.0 - $this->radToDeg(acos($azRad));
				if ($hourAngle > 0.0) {
					$azimuth = -$azimuth;
				}
			} else {
				if ($latitude > 0.0) {
					$azimuth = 180.0;
				} else { 
					$azimuth = 0.0;
				}
			}
			if ($azimuth < 0.0) {
				$azimuth += 360.0;
			}
			$exoatmElevation = 90.0 - $zenith;

			// Atmospheric Refraction correction
			$refractionCorrection = $this->calcRefraction($exoatmElevation);

			$solarZen = $zenith - $refractionCorrection;
			$elevation = 90.0 - $solarZen;

			$R = new stdClass();
				$R->azimuth = $azimuth;
				$R->elevation = $elevation;

			return $R;
		}

		function calcSolNoon($jd, $longitude, $timezone) {
			$tnoon = $this->calcTimeJulianCent($jd - $longitude/360.0);
			$eqTime = $this->calcEquationOfTime($tnoon);
			$solNoonOffset = 720.0 - ($longitude * 4) - $eqTime; // in minutes
			$newt = $this->calcTimeJulianCent($jd + $solNoonOffset/1440.0);
			$eqTime = $this->calcEquationOfTime($newt);
			$solNoonLocal = 720 - ($longitude * 4) - $eqTime + ($timezone*60.0); // in minutes
			while ($solNoonLocal < 0.0) {
				$solNoonLocal += 1440.0;
			}
			while ($solNoonLocal >= 1440.0) {
				$solNoonLocal -= 1440.0;
			}

			return $solNoonLocal;
		}



		function calcSunriseSetUTC($rise, $JD, $latitude, $longitude) {
			$t = $this->calcTimeJulianCent($JD);
			$eqTime = $this->calcEquationOfTime($t);
			$solarDec = $this->calcSunDeclination($t);
			$hourAngle = $this->calcHourAngleSunrise($latitude, $solarDec);
			if (!$rise) $hourAngle = -$hourAngle;
			$delta = $longitude + $this->radToDeg($hourAngle);
			$timeUTC = 720 - (4.0 * $delta) - $eqTime;	// in minutes

			return $timeUTC;
		}

		// rise = 1 for sunrise, 0 for sunset
		function calcSunriseSet($rise, $JD, $latitude, $longitude, $timezone) {


			$timeUTC = $this->calcSunriseSetUTC($rise, $JD, $latitude, $longitude);
			$newTimeUTC = $this->calcSunriseSetUTC($rise, $JD + $timeUTC/1440.0, $latitude, $longitude);
			if (is_numeric($newTimeUTC)) {

				$timeLocal = $newTimeUTC + ($timezone * 60.0);
				$riseT = $this->calcTimeJulianCent($JD + $newTimeUTC/1440.0);
				$riseAzEl = $this->calcAzEl($riseT, $timeLocal, $latitude, $longitude, $timezone);
				$azimuth = $riseAzEl->azimuth;
				$jday = $JD;
				if ( ($timeLocal < 0.0) || ($timeLocal >= 1440.0) ) {
					$increment = (($timeLocal < 0) ? 1 : -1);
					while (($timeLocal < 0.0)||($timeLocal >= 1440.0)) {
						$timeLocal += $increment * 1440.0;
						$jday -= $increment;
					}
				}

			} else { // no sunrise/set found

				$azimuth = -1.0;
				$timeLocal = 0.0;
				$doy = $this->calcDoyFromJD($JD);
				if ( (($latitude > 66.4) && ($doy > 79) && ($doy < 267)) ||
				     (($latitude < -66.4) && (($doy < 83) || ($doy > 263))) ) {
					//previous sunrise/next sunset
					$jday = $this->calcJDofNextPrevRiseSet(!$rise, $rise, $JD, $latitude, $longitude, $timezone);
				} else {   //previous sunset/next sunrise
					$jday = $this->calcJDofNextPrevRiseSet($rise, $rise, $JD, $latitude, $longitude, $timezone);
				}
			}

			$R = new stdClass();
				$R->jday = $jday;
				$R->timeLocal = $timeLocal;
				$R->azimuth = $azimuth;

			return $R;
		}

		function calcJDofNextPrevRiseSet($next, $rise, $JD, $latitude, $longitude, $tz) {

			$julianday = $JD;
			$increment = (($next) ? 1.0 : -1.0);
			$time = $this->calcSunriseSetUTC($rise, $julianday, $latitude, $longitude);

			while(!isNumber($time)) {
				$julianday += $increment;
				$time = $this->calcSunriseSetUTC($rise, $julianday, $latitude, $longitude);
			}
			$timeLocal = $time + $tz * 60.0;
			while (($timeLocal < 0.0) || ($timeLocal >= 1440.0)) {
				$incr = (($timeLocal < 0) ? 1 : -1);
				$timeLocal += ($incr * 1440.0);
				$julianday -= $incr;
			}

			return $julianday;
		}

		/*************************************************************/
		/* End calculation functions */
		/*************************************************************/



		//--------------------------------------------------------------
		// returns a string in the form DDMMMYYYY[ next] to display prev/next rise/set
		// flag=2 for DD MMM, 3 for DD MM YYYY, 4 for DDMMYYYY next/prev
		function dayString($jd, $next, $flag) {

			if ( ($jd < 900000) || ($jd > 2817000) ) {
				return  "error";
			}

			$date = $this->calcDateFromJD($jd);

			if ($flag == 2)
				$output = $this->zeroPad($date->day,2) . " " . $this->monthList[$date->month-1]["abbr"];
			if ($flag == 3)
				$output = $this->zeroPad($date->day,2) . $this->monthList[$date->month-1]["abbr"] . $date->year;
			if ($flag == 4)
				$output = $this->zeroPad($date->day,2) . $this->monthList[$date->month-1]["abbr"] . $date->year . (($next) ? " next" : " prev");

			return $output;
		}

		//--------------------------------------------------------------
		function timeDateString($JD, $minutes) {
			return $this->timeString($minutes, 3) . " " . $this->dayString($JD, 0, 3);
		}

		//--------------------------------------------------------------
		// timeString returns a zero-padded string (HH:MM:SS) given time in minutes
		// flag=2 for HH:MM, 3 for HH:MM:SS
		function timeString($minutes, $flag) {

			if ( ($minutes >= 0) && ($minutes < 1440) ) {
				$floatHour = $minutes / 60.0;
				$hour = floor($floatHour);
				$floatMinute = 60.0 * ($floatHour - floor($floatHour));
				$minute = floor($floatMinute);
				$floatSec = 60.0 * ($floatMinute - floor($floatMinute));
				$second = floor($floatSec + 0.5);
				if ($second > 59) {
					$second = 0;
					$minute += 1;
				}
				if (($flag == 2) && ($second >= 30)) $minute++;
				if ($minute > 59) {
					$minute = 0;
					$hour += 1;
				}
				$output = $this->zeroPad($hour, 2) . ":" . $this->zeroPad($minute, 2);
				if ($flag > 2) $output = $output . ":" . $this->zeroPad($second,2);
			} else { 
				$output = "error";
			}

			return $output;
		}


		//--------------------------------------------------------------
		// zero pad a string 'n' with 'digits' number of zeros 
		function zeroPad($n, $digits) {

			$n = strval($n);
			
			while (strlen((string)$n) < $digits) {
				$n = '0' . $n;
			}

			return $n;
		}


		//--------------------------------------------------------------
		function getDateString($date) {

		        $s = $date->year 
				. '-' 
				. $this->zeroPad($date->month,2) 
				. '-' 
				. $this->zeroPad($date->day,2) 
				. 'T' 
				. $this->zeroPad($date->hour,2) 
				. ':' 
				. $this->zeroPad($date->minute,2) 
				. ':'
				. $this->zeroPad($date->second,2);

			return $s;
		}

		//--------------------------------------------------------------

		/*
		* Setup Date for the Times
		*/
			function SetupDate(){

				/*
				* Setup Date Values
				*/

					// Validate Day is OK for the Month
						if(($this->isLeapYear($this->Year)) && ($this->Month == 2) ) {
							if ($this->Day > 29) {
								$this->Day = 29;
							} 
						} else {
							if ($this->Day > $this->monthList[$this->Month-1]["numdays"]) {
								$this->Day = $this->monthList[$this->Month-1]["numdays"];
							}
						}

					$Date = new stdClass();
						$Date->year = $this->Year;
						$Date->month = $this->Month;
						$Date->day = $this->Day;
						$Date->hour = $this->Hours;
						$Date->minute = $this->Minutes;
						$Date->second = $this->Seconds;

				/*
				* Add in TZ, Lat and Long
				*/
					$Mins = $this->Hours*60 + $this->Minutes + $this->Seconds/60.0;
					$Lat = floatval($this->Lat);
					$Lng = floatval($this->Long);

				    
				    $DateString = $this->getDateString($Date);
					$TimeStamp = new DateTimeImmutable($DateString, new DateTimeZone($this->TimeZoneName));
					
				/*
				* Setup UTC Offset
				*/
					$UTCOffset = $TimeStamp->format('Z');

					$Offset = explode(":", $UTCOffset);
					$TimeZoneOffset = floatval($Offset[0]) + floatval($Offset[1])/60.0;

					// Temp Fix
					$TimeZoneOffset = -5;



			

				$Data = new stdClass();
					$Data->year= $Date->year;
					$Data->month= $Date->month;
					$Data->day= $Date->day;
					$Data->hour= $Date->hour;
					$Data->minute= $Date->minute;
					$Data->second= $Date->second;
					$Data->time_local= $Mins;
					$Data->utc_offset= $UTCOffset;
					$Data->lat= $Lat;
					$Data->lon= $Lng;
					$Data->tz= $TimeZoneOffset;

				return $Data;


			}

		//--------------------------------------------------------------
		// Do the calculations and return the results
		function Calculate(){

			// Setup Data
				$data = $this->setupDate();
			


				$jday = $this->getJD($data->year, $data->month, $data->day);
				$total = $jday + $data->time_local/1440.0 - $data->tz/24.0;

				$T = $this->calcTimeJulianCent($total);
				$azel = $this->calcAzEl($T, $data->time_local, $data->lat, $data->lon, $data->tz);
				$solnoon = $this->calcSolNoon($jday, $data->lon, $data->tz);
				$rise = $this->calcSunriseSet(1, $jday, $data->lat, $data->lon, $data->tz);
				$set  = $this->calcSunriseSet(0, $jday, $data->lat, $data->lon, $data->tz);


			$Return_Data = new stdClass();


				$Return_Data->sunriseDateTime = $this->timeDateString($rise->jday, $rise->timeLocal);
				$Return_Data->sunsetDateTime = $this->timeDateString($set->jday, $set->timeLocal);

			return $Return_Data;
		}

}