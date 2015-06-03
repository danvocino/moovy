<pre>

      dMMMMMMMMb  .aMMMb  .aMMMb  dMP dMP dMP dMP
     dMP"dMP"dMP dMP"dMP dMP"dMP dMP dMP dMP.dMP
    dMP dMP dMP dMP dMP dMP dMP dMP dMP  VMMMMP
   dMP dMP dMP dMP.aMP dMP.aMP  YMvAP" dA .dMP
  dMP dMP dMP  VMMMP"  VMMMP"    VP"   VMMMP"

</pre>

<?php

  session_start();
  $user = $_SESSION['user'];

  // break out individual frames of video file
  shell_exec('ffmpeg -i ' . $user . '/' . $_POST['video_file']
                          . ' -vf "scale=-1:480" -t 10 '
                          . $user . '/' . $_POST['video_name'] . '/frames/fr_%04d_.gif');

  $first_batch = array();

  // read in frames that were broken out of video file
  if ($handle = opendir('./' . $user .'/frames')) {
    while (false !== ($entry = readdir($handle))) {
      if (strpos($entry, '.gif'))
        $first_batch[] = $entry;
    }
    closedir($handle);
  }

  // reverse the frames, duplicate them and append to first batch
  // to create seamless looping frames
  $frames = array_reverse($first_batch);

  $parts = explode('_', $frames[0], 2);
    $last_id = intval($parts[1]);
      $name = $parts[0];

  unset($frames[0]); //won't double first
  unset($frames[count($frames)]); //or last frame

  foreach ($frames as $frame) {
    $nid = str_pad(++$last_id, 4, "0", STR_PAD_LEFT);
    shell_exec('cp ./' . $user . '/frames/' . $frame
                       . ' '
                       . './frames/' . $name . '_' . $nid . '_c.gif');
  }

  // convert frames from both batches into seamless looping gif
  shell_exec('convert -quiet -delay 1 ' . $user . '/frames/*.gif'
                                        . ' '
                                        . $user . '/' . $_POST['video_name'] . '.gif');

?>
