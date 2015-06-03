<?php

  session_start();

  $me = $_SESSION['user'];
  $moovy = $_SESSION['current_moovy'];

  $frames = array(); // fetch images from ./frames/
  if ($handle = opendir('./' . $me . '/' . $moovy . '/frames')) { // optimize with /thumbs for performance
    while (false !== ($entry = readdir($handle))) {
      if (strpos($entry, '.gif'))
        $frames[] = $entry;
    }
    closedir($handle);
  }
  
  $frame_count = count($frames);

?>

<head>
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js"></script>
	<script src="http://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.3.3/underscore-min.js"></script>
  <script type="text/javascript" src="dragslider.js"></script>
  <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.10/themes/ui-lightness/jquery-ui.css" />
</head>

<body>

  <div style="width:100%;">
    <img id="first_display" src="placeholder.png" style="width:49%;">
    <img id="last_display" src="placeholder.png" style="width:49%;">
  </div>

  <div style="width:90%;padding:20px;">
    <div id="slider" style=""></div>
  </div>

  <div id="first"></div>
  <div id="last"></div>
  
  <div data-moovy="<?php echo $moovy; ?>" data-frame-count="<?php echo $frame_count; ?>"></div>
</body>

<script>

  var frame_queue = [];

  $(function(){
  
    $('#slider').dragslider({
      animate: true,
      range: true,
      rangeDrag: true,
      step: 1,
      values: [1, $('[data-frame-count]').data('frame-count')],
      min: 1,
      max: $('[data-frame-count]').data('frame-count'),
      slide: function(event, ui) {
      
        window.first_val =                  ui.values[0];
        $('#first').text('first frame: ' +  ui.values[0]);
        window.last_val =                   ui.values[1];
        $('#last').text('last frame: '   +  ui.values[1]);
        
        window.last_handle = (ui.values[0] == ui.value) ? 'first' : 'last'
        window.throttled_get_frames();
      },
      change: function(event, ui) {
        window.last_handle = (ui.values[0] == ui.value) ? 'first' : 'last'
        //window.get_frames();
      }
    });

    window.get_frames = function() {
      $.ajax({
        url: "frame.php",
        type: "POST",
        data: {
          'moovy' : $('[data-moovy]').data('moovy'),
          'first' : window.first_val,
          'last' : window.last_val
        },
        dataType: "json",
        success: function (data) {
          $('#first_display').attr('src', data['first']);
          $('#last_display').attr('src', data['last']);
        }
      });
    }
    
    window.throttled_get_frames = _.throttle(window.get_frames, 200);

  });
</script>
