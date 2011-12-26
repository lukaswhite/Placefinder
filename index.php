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
            <form action="/select-place.php" method="post">
              <fieldset>
                <div class="clearfix">                
                  <label for="name">Name </label>
                  <div class="input">
                    <input type="text" size="30" name="name" id="name" placeholder="Place name" />
                  </div>
                </div>                
                <div class="actions">
                  <input type="submit" value="submit" class="btn primary" />
                </div>
              </fieldset>
            </form>
<?php include 'includes/footer.php' ?>