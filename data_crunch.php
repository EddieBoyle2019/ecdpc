<?php

//find current date in format required

$date_d = date("d");
$date_m = date("m");
$date_y = date("y");
$date_Y = date("Y");

//Country centroids data
$file1 = '/var/www/html/D3/Data/country_centroids_az8.json';

$JSON1 = file_get_contents($file1);

$json1_array = json_decode($JSON1, true);

//Virus data
$file2 = "/var/www/html/D3/Data/ecdpc_data_" . $date_d . $date_m . $date_y . ".json";

$JSON2 = file_get_contents($file2);

$json2_array = json_decode($JSON2, true);

$datestring = $date_d . "/" . $date_m . "/" . $date_Y;

//CSV file for saving data to
$data_filename = "/var/www/html/D3/Data/ECDPC3/" . "ecdpc_map_data_" . $date_d . $date_m . $date_Y . ".csv";
//Add data headers as first line to CSV file 
$dataheaders_string = "CountryCode" . "," . "Lon" . "," . "Lat" . "," . "Cases" . "," . "Deaths" . "\n";
file_put_contents($data_filename, $dataheaders_string, FILE_APPEND);

//Loop through centroids data, find lat/long values
foreach($json1_array['features'] as $feature)
{
	foreach($feature as $key => $value)
	{
		$found = 0;

		if (strcmp($key, "geometry") == 0)
		{
			foreach($value as $geom_key => $geom_value)
			{
				if (strcmp ($geom_key, "coordinates") == 0)
				{	
					$cur_lon_value = $geom_value[0];
					$cur_lat_value = $geom_value[1];
				}
			}
		}

		if (strcmp($key, "properties") == 0)
		{
			foreach($value as $property_key => $property_value)
			{

				if (strcmp ($property_key, "gu_a3") == 0)
				{	

					//Special cases for France and USA
					if (strcmp($property_value, "FRA") == 0)
					{
						$cur_lon_value = "2";
						$cur_lat_value = "46";
					}
					if (strcmp($property_value, "USA") == 0)
					{
						$cur_lon_value = "-98";
						$cur_lat_value = "40";
					}

					//Loop through virus data, and match on country code in centroids data, populate new data variables
					foreach($json2_array['records'] as $item)
					{

						//Find item in virus data with current date
						if (strcmp($item['dateRep'], $datestring) == 0)
						{

							if (strcmp ($property_value, $item['countryterritoryCode']) == 0)
							{
								$found = 1;
								//populate new data variables
								$cur_cases = $item['cases'];
								$cur_deaths = $item['deaths'];
							}

						}

					}

					if ($found == 1)
					{
						$datastring = $property_value . "," . $cur_lon_value . "," . $cur_lat_value . "," . $cur_cases . "," . $cur_deaths . "\n";
						//Read new data array to new CSV file
						file_put_contents($data_filename, $datastring, FILE_APPEND);
					}
					else
					{
						//Populate new data variables with zeros
						$cur_cases = 0;
						$cur_deaths = 0;
						$datastring = $property_value . "," . $cur_lon_value . "," . $cur_lat_value . "," . $cur_cases . "," . $cur_deaths . "\n";
						//Read new data array to new CSV file
						file_put_contents($data_filename, $datastring, FILE_APPEND);	
					}


				}
			}
		}

	}	

}

?>