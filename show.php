<?php
session_start();
if (isset($_POST['optionsPlaces'])) {
  
  // get the place ID
  $place_id = intval($_POST['optionsPlaces']);
  
  // The places are in the session
  $places = $_SESSION['places'];
  
  $place = $places[$place_id];
}
?>
<?php include 'includes/header.php' ?>
<body onload="initialize()">

    <div class="container">

      <div class="content">
        <div class="page-header">
          <h1>Placefinder <small>A simple demo</small></h1>
        </div>
        <div class="row">
          <div class="span14">
            <h2>Where are you?</h2>                     
            <?php if (isset($place)): ?>
            <h3><?php print $place->name ?></h3>
            <div id="map"></div>            
                                                
            <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
            <script type="text/javascript">
              function initialize() {
                
                var latlng = new google.maps.LatLng(<?php print $place->lat ?>, <?php print $place->lng ?>);
                
                var myOptions = {
                  zoom: 8,
                  center: latlng,
                  mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                
                var map = new google.maps.Map(document.getElementById("map"),
                    myOptions);
                
                var marker = new google.maps.Marker({
                  position: latlng,
                  map: map,
                  title: "<?php print $place->name ?>"
                });
              }

            </script>
            
            <?php else: ?>
            <p class="error">No place selected, please <a href="/select-place.php">go back</a> and select one.</p>
            <?php endif; ?>     
            
            <a href="/">Start Over</a>
<?php include 'includes/footer.php' ?>