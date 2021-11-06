<?php namespace App\Controllers;

use App\Core\BaseController;

class API extends BaseController
{
    public function __construct()
    {
        $this->pageData = array();
    }

    public function submit_post_code()
    {
        if ($_POST) {

            $input = $this->request->getPost();
            $error = false;
            $message = "";

            if (empty($input['postcode'])) {
                $error = true;
                $message = "invalid post code";
            } else {
                $valid = $this->validate_post_code($input['postcode']);
                if (!$valid) {
                    $error = true;
                    $message = "invalid post code";
                } else {
                    $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $input['postcode'] . ",jp&key=AIzaSyBPhDeaHVnMYcQrmGVpWdP-SnXDyRMwe-k";
                    $result = $this->conn($url);
                    $result = json_decode($result, true);
                    if ($result['status'] != "OK") {
                        $error = true;
                        $message = "invalid post code";
                    } else {
                        $is_japan = false;
                        $add_data = $result['results'][0];
                        $full_address = "";
                        $administrative_area_level_1 = "";
                        $locality = "";
                        $sublocality = "";
                        $lat = $add_data['geometry']['location']['lat'];
                        $lng = $add_data['geometry']['location']['lng'];
                        foreach ($add_data['address_components'] as $row) {
                            if ($row['long_name'] == "Japan") {
                                $is_japan = true;
                            }
                            foreach ($row['types'] as $t_row) {
                                if ($t_row === "sublocality") {
                                    $sublocality = $row['long_name'];
                                }
                                if ($t_row === "locality") {
                                    $locality = $row['long_name'];
                                }
                                if ($t_row === "administrative_area_level_1") {
                                    $administrative_area_level_1 = $row['long_name'];
                                }
                            }
                        }

                        //if post code doesnt get accurate address, get new address via lat lng from post code
                        if ($administrative_area_level_1 === "") {
                            $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $lat . "," . $lng . "&key=AIzaSyBPhDeaHVnMYcQrmGVpWdP-SnXDyRMwe-k";
                            $result = $this->conn($url);
                            $result = json_decode($result, true);
                            $add_data = $result['results'][0];
                            foreach ($add_data['address_components'] as $row) {
                                if ($row['long_name'] == "Japan") {
                                    $is_japan = true;
                                }
                                foreach ($row['types'] as $t_row) {
                                    if ($t_row === "sublocality") {
                                        $sublocality = $row['long_name'];
                                    }
                                    if ($t_row === "locality") {
                                        $locality = $row['long_name'];
                                    }
                                    if ($t_row === "administrative_area_level_1") {
                                        $administrative_area_level_1 = $row['long_name'];
                                    }
                                }
                            }
                            $full_address = $administrative_area_level_1 . ", " . $locality . ", " . $sublocality;
                        } else {
                            $full_address = $administrative_area_level_1 . ", " . $locality . ", " . $sublocality;
                        }
                        if (!$is_japan) {
                            $error = true;
                            $message = "invalid japanese post code";
                        }
                    }
                }
            }

            if (!$error) {
                // forecast for today
                $url = "api.openweathermap.org/data/2.5/find?lat=" . $lat . "&lon=" . $lng . "&cnt=1&units=metric&appid=3aee5d3d3c5d4ab3e9b6da0829e1ea42";
                $current_weather = $this->conn($url);
                $current_weather = json_decode($current_weather, true);
                $current_weather = $current_weather['list'][0];

                $forecast[0] = array(
                    "date" => gmdate("Y-m-d D", $current_weather['dt']),
                    "max" => round($current_weather['main']['temp_max']),
                    "min" => round($current_weather['main']['temp_min']),
                    "weather" => $current_weather['weather'][0]['main'],
                    "icon" => "https://openweathermap.org/img/wn/" . $current_weather['weather'][0]['icon'] . "@4x.png",
                );

                // forecast for next 8 days
                $url = "https://api.openweathermap.org/data/2.5/onecall?lat=" . $lat . "&lon=" . $lng . "&exclude=minutely,hourly,alerts&units=metric&appid=3aee5d3d3c5d4ab3e9b6da0829e1ea42";
                $weather_data = $this->conn($url);
                $weather_data = json_decode($weather_data, true);

                for ($i = 1; $i <= 2; $i++) {
                    $index = $i - 1;

                    $forecast[$i] = array(
                        "date" => gmdate("Y-m-d D", $weather_data['daily'][$index]['dt']),
                        "max" => round($weather_data['daily'][$index]['temp']['max']),
                        "min" => round($weather_data['daily'][$index]['temp']['min']),
                        "weather" => $weather_data['daily'][$index]['weather'][0]['main'],
                        "icon" => "https://openweathermap.org/img/wn/" . $weather_data['daily'][$index]['weather'][0]['icon'] . "@4x.png",
                    );
                }

                //get popular locations within 1000m of zipcode
                $url = "https://api.opentripmap.com/0.1/en/places/radius?radius=10000&lon=" . $lng . "&lat=" . $lat . "&rate=3&limit=4&apikey=5ae2e3f221c38a28845f05b68bf3556882f05a32622f9041d7e1a968";
                $place_data = $this->conn($url);
                $place_data = json_decode($place_data, true);
                $places = $place_data['features'];

                $hotspot = array();
                foreach ($places as $row) {
					$wiki_data = $this->get_wiki_data($row['properties']['wikidata']);
					if(empty($wiki_data['title'])){
						continue;
					}
                    array_push($hotspot, array(
                        "name" => $row['properties']['name'],
                        "wikidata" => $row['properties']['wikidata'],
                        "lng" => $row['geometry']['coordinates'][0],
                        "lat" => $row['geometry']['coordinates'][1],
						"description" => $wiki_data['description'],
						"title" => $wiki_data['title'],
						"image" => $wiki_data['image'],
                    ));
                }

                //clean up $full_address text
                $full_address = rtrim($full_address, ", ");

                die(json_encode(array(
                    "status" => true,
                    "data" => array(
                        "address" => $full_address,
                        "lat" => $lat,
                        "lng" => $lng,
                        "forecast" => $forecast,
                        "hotspot" => $hotspot,
                    ),
                )));
            } else {
                die(json_encode(array(
                    "status" => false,
                    "message" => $message,
                )));
            }

        } else {
            die(json_encode(array(
                "status" => false,
                "message" => "invalid method",
            )));
        }
    }

    public function validate_post_code($postcode)
    {
        $valid = true;

        if (strlen($postcode) != 8) {
            $valid = false;
        }
        if (strpos($postcode, "-") != 3) {
            $valid = false;
        }
        if (!is_numeric(substr($postcode, 0, 3))) {
            $valid = false;
        }
        if (!is_numeric(substr($postcode, 3))) {
            $valid = false;
        }

        return $valid;
    }

    public function get_wiki_data($id)
    {
		// get wiki title from id
        $url = "https://www.wikidata.org/w/api.php?action=wbgetentities&format=json&props=sitelinks&ids=" . $id;
        
		$result = $this->conn($url);
		$result = json_decode($result, true);

		// unrelated location filter
		if(empty($result['entities'][$id]['sitelinks']['jawiki'])){
			return array(
				"title" => "",
				"description" => "",
				"image" => "",
			);
		}
		$title = $result['entities'][$id]['sitelinks']['jawiki']['title'];
		$title = urlencode($title);

		// get wiki data from title
		$url = 'https://ja.wikipedia.org/w/api.php?action=query&prop=extracts|info|pageimages&exintro&titles=' . $title . '&format=json&explaintext&redirects&inprop=url&indexpageids&pithumbsize=100';
		$result = $this->conn($url);
		$result = json_decode($result, true);
		$page_id = $result['query']['pageids'][0];
		$description = trim($result['query']['pages'][$page_id]['extract']);
		$image = $result['query']['pages'][$page_id]['thumbnail']['source'];

		return array(
			"title" => urldecode($title),
			"description" => $description,
			"image" => $image,
		);

    }

}
