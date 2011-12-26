<?php
session_start();

// insert your own Application ID here (obtained from http://developer.yahoo.com/wsregapp/)
$app_id = 'cNz3du3V34ELtMP4AjsvvHzM7ZbdrY.vHGXrEGlMVZWz1HjygfcxOzYxOwhJ3x25Ow--';

if (isset($_POST['name'])) {
  $name = strip_tags($_POST['name']);
  
  // specify the endpoint of the Yahoo! Placemaker service
  $handle = curl_init('http://wherein.yahooapis.com/v1/document');
  
  // we're using POST
	curl_setopt ($handle, CURLOPT_POST, 1);
  
  // Set the POST variables
	curl_setopt (
    $handle, 
    CURLOPT_POSTFIELDS, 
    sprintf('documentContent=%s&documentType=%s&outputType=%s&autoDisambiguate=%s&appid=%s&inputLanguage=%s',
      urlencode($name), 
      'text/plain', 
      'xml', 
      'false',
      $app_id,
      'en-US')
    );
    
	curl_setopt ($handle, CURLOPT_FOLLOWLOCATION, 1);
  
  // We want to return the response as a string
	curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  
  // Go get the response
	$response = curl_exec ($handle);
  
  // Good practice to free up resources
	curl_close ($handle);
  
  $xml = simplexml_load_string($response); 
  
  $places = array();
    
  foreach ($xml->document->placeDetails as $xmlPlaceDetail) {    
    
    $xmlPlace = $xmlPlaceDetail->place;
    $xmlCentroid = $xmlPlace->centroid;
      
    $name = (string) $xmlPlace->name;
      
    $place = new stdClass();
    $place->id = (int) $xmlPlaceDetail->placeId;
    $place->woeid = (string) $xmlPlace->woeId;
    $place->name = $name;
    $place->lat = (float) $xmlCentroid->latitude;
    $place->lng = (float) $xmlCentroid->longitude;
    $place->confidence = (int) $xmlPlaceDetail->confidence;
    $place->type = (string) $xmlPlace->type;    
    // assuming name is something like "London, England, GB", take the last two characters to get the country
    $place->country = substr($name, (strlen($name)-2));
    
    $places[$place->id] = $place;
  
  }
  
  // now sort by confidence
    
  /**
   *
   * Simple callback for sorting
   * @param stdClass $a
   * @param stdClass $b
   * @return bool 
   */
  function confidenceSort($a, $b) {
    if ($a->confidence == $b->confidence) return 0;
    return ($a->confidence > $b->confidence) ? -1 : 1;
  }
  
  // sort in descending order of confidence, maintaining index association
  uasort($places, 'confidenceSort');
  
  // put the places in the session, we'll probably want to retrieve at least one of them later
  $_SESSION['places'] = $places;
}
?>
<?php include 'includes/header.php' ?>
<body>    

    <div class="container">

      <div class="content">
        <div class="page-header">
          <h1>Placefinder <small>A simple demo</small></h1>
        </div>
        <div class="row">
          <div class="span14">
            <h2>Where are you?</h2>
            <form action="/show.php" method="post">
              <fieldset>                
                <?php if ((isset($places))&&(count($places))): ?>
                <div class="clearfix">
                  <label id="optionsPlaces">Select place</label>
                  <div class="input">
                    <ul class="inputs-list">
                      <?php foreach ($places as $place): ?>
                      <li>
                        <label>
                          <input type="radio" name="optionsPlaces" value="<?php print $place->id ?>" />
                          <span><?php print $place->name ?></span>
                        </label>
                      </li>                    
                      <?php endforeach; ?>
                    </ul>              
                  </div>
                </div>
                <?php else: ?>
                <p class="error">No place entered, or your location could not be identified. Please <a href="/">go back</a> and try again.</p>
                <?php endif; ?>
                
                <div class="actions">
                  <input type="submit" value="submit" class="btn primary" />
                </div>
              </fieldset>
            </form>
<?php include 'includes/footer.php' ?>