<?php
	
	/*
	* Test Calc Sunrise and Sunset
	*/

		include('Clac_SunriseSunsetNOAA.php');

			$Calculator = new NOAA_SunriseSetCalculator(); // Defaults to the Current Day and Time

				/*
					We could add things like

						$Calculator->SetDay(4);
						...

					But for now, leave it.

				*/

			$Data = $Calculator->Calculate();

			echo "Sunrise: " . $Data->sunriseDateTime . "<br>";
			echo "Sunset: " . $Data->sunsetDateTime . "<br>";
?>