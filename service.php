<?php

class Mapa extends Service
{
	/**
	 * Function executed when the service is called
	 *
	 * @param Request
	 * @return Response
	 * */
	public function _main(Request $request)
	{
		// do not allow blank searches
		if(empty($request->query))
		{
			$response = new Response();
			$response->setCache();
			$response->setResponseSubject("Que mapa desea ver?");
			$response->createFromTemplate("home.tpl", array());
			return $response;
		}

		// include google maps library
		require_once "{$this->pathToService}/lib/GoogleStaticMap.php";
		require_once "{$this->pathToService}/lib/GoogleStaticMapFeature.php";
		require_once "{$this->pathToService}/lib/GoogleStaticMapFeatureStyling.php";
		require_once "{$this->pathToService}/lib/GoogleStaticMapMarker.php";
		require_once "{$this->pathToService}/lib/GoogleStaticMapPath.php";
		require_once "{$this->pathToService}/lib/GoogleStaticMapPathPoint.php";

		// get and clean the argument
		$argument = $request->query;
		$argument = str_replace("\n", " ", $argument);
		$argument = str_replace("\r", "", $argument);
		$argument = trim(strtolower($argument));

		// detecting type
		$type = 'hibrido';
		$internalType = "hybrid";

		if (stripos($argument, 'fisico') !== false)
		{
			$type = 'fisico';
			$internalType = "satellite";
		}
		elseif (stripos($argument, 'politico') !== false)
		{
			$type = 'politico';
			$internalType = "roadmap";
		}
		elseif (stripos($argument, 'terreno') !== false)
		{
			$type = 'terreno';
			$internalType = "terrain";
		}

		// remove the type from the query to display on the template
		$argument = str_ireplace($type, '', $argument);

		// detecting zoom
		$zoom = null;
		for($i = 22; $i >= 1; $i--)
		{
			if (stripos($argument, $i . 'x') !== false)
			{
				$zoom = $i;
				$argument = str_ireplace("{$i}x", '', $argument);
			}
		}

		// remove bad starting arguments
		if (substr($argument, 0, 3) == 'de ') $argument = substr($argument, 3);
		if (substr($argument, 0, 4) == 'del ') $argument = substr($argument, 4);

		// create the map
		$oStaticMap = new GoogleStaticMap();
		$oStaticMap->setScale(1);
		$oStaticMap->setHeight(400);
		$oStaticMap->setWidth(400);
		$oStaticMap->setLanguage("es");
		$oStaticMap->setHttps(true);
		$oStaticMap->setMapType($internalType);
		if ( ! is_null($zoom)) $oStaticMap->setZoom($zoom);
		$oStaticMap->setCenter($argument);

		// get path to the www folder
		$di = \Phalcon\DI\FactoryDefault::getDefault();
		$wwwroot = $di->get('path')['root'];

		// save the image as a temp file
		$mapImagePath = "$wwwroot/temp/" . $this->utils->generateRandomHash() . ".jpg";
		$content = file_get_contents($oStaticMap);
		file_put_contents($mapImagePath, $content);

		// optimize the image
		$this->utils->optimizeImage($mapImagePath);

		// create the response variables
		$responseContent = array(
			"type" => $type,
			"request" => $argument,
			"zoom" => $zoom,
			"image" => $mapImagePath
		);

		// create the response
		$response = new Response();
		$response->setCache();
		$response->setResponseSubject("Mapa para " . $request->query);
		$response->createFromTemplate("basic.tpl", $responseContent, array($mapImagePath));
		return $response;
	}
}
