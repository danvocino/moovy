<?php
/*

      dMMMMMMMMb  .aMMMb  .aMMMb  dMP dMP dMP dMP
     dMP"dMP"dMP dMP"dMP dMP"dMP dMP dMP dMP.dMP
    dMP dMP dMP dMP dMP dMP dMP dMP dMP  VMMMMP
   dMP dMP dMP dMP.aMP dMP.aMP  YMvAP" dA .dMP
  dMP dMP dMP  VMMMP"  VMMMP"    VP"   VMMMP"

*/

  if (!isset($_POST['first'])) return;

  $me = $_POST['user'];
  $moovy = $_POST['video_name'];
  $first_frame = $_POST['first'];
  $last_frame = $_POST['last'];

  $frames = array(); // fetch images from ./frames/
  if ($handle = opendir('./' . $me . '/' . $moovy . '/frames')) { // optimize this to /thumbs for better performance
    while (false !== ($entry = readdir($handle))) {
      if (strpos($entry, '.gif'))
        $frames[] = $entry;
    }
    closedir($handle);
  }

  $trimmed = array_slice($frames, $first_frame-1, ($last_frame+1)-$first_frame);

  $frame_urls = array(
    'first' => $me . '/' . $moovy . '/thumbs/' . $trimmed[0],
    'last' => $me . '/' . $moovy . '/thumbs/' . $trimmed[count($trimmed)-1],
    'intermediate_frames' => isset($_POST['intermediate_frame_count']) ? get_intermediate_frames($first_frame, $last_frame, $trimmed) : null
  );

  echo json_encode($frame_urls);


  
  function get_intermediate_frames($first, $last, $frames) {
    if (!isset($_POST['intermediate_frame_count']))
      return null;

    $first_frame = $_POST['first'];
    $last_frame = $_POST['last'];

    $span = count($frames)-2;
    if ($span <= 0)
      return null;

    if ($span < $_POST['intermediate_frame_count']) //not enough frames, just give all we can
      $_POST['intermediate_frame_count'] = $span;

    $step = $span / ($_POST['intermediate_frame_count']);

    $keys = range($first_frame, $last_frame-2, $step);
    $adjust = ((($span+1) - $keys[count($keys)-1]) - $keys[0]) / 2;

    $keys = range($first_frame + $adjust, $last_frame-2, $step);

    $int_frames = array();
    foreach ($keys as $key) {
      $int_frames[] = $frames[$key];
    }
    return $int_frames;
  }

?>
