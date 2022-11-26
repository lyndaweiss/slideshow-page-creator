<?php
  // Course Properties
  $organization = $_POST['organization'];
  $min_grade = $_POST['min-grade'];
  $max_grade = $_POST['max-grade'];
  $day = $_POST['day'];
  $season = $_POST['season'];
  $year = $_POST['year'];
  // Project Groups
  $project_groups = json_decode($_POST['projects']);
  // Image Properties
  $captions = $_POST['caption'];
  // Directories and Files
  $page_directory = 'pages';
  $slideshow_html_file = "$organization$min_grade-$max_grade$day$season$year.html";

  // Open slideshow html file and read into a string
  $slideshow = file_get_contents("$page_directory/$slideshow_html_file");

  // Position at which to add new slideshow photos
  $insert_index = strpos($slideshow, "</ul>");

  // Create string with contents of class photo page html
  /*
  */
  $pageHtml = '';
  $liStart = "\n\t\t<li class=\"card\" data-target=\"card\">";
  $liEnd = "\n\t\t</li>";

  $caption_index = 0;
  foreach($project_groups as $project) {
    $num_images = count($project);
    foreach($project as $key => $value) {
      $image_src = "images/$season$year/$organization$min_grade-$max_grade$day/" . $value;
      $image_num = $key + 1;
      $pageHtml .= $liStart;
      $pageHtml .= "\n\t\t\t<p class=\"caption\">" . $captions[$caption_index];
      $pageHtml .= "<span class=\"slide-number\">" . " ($image_num / $num_images) " . "</span></p>";
      $pageHtml .= "\n\t\t\t<img src=\"" . $image_src . "\" alt=\"\">";
      $pageHtml .= $liEnd;
      $caption_index++;
    }
  }

  $pageHtml .= "\n\t";

  // Insert new html into file contents string
  $slideshow = substr_replace($slideshow, $pageHtml, $insert_index, 0);

  // Write html to file and close it
  /*
  */
  $bytes_written = file_put_contents("$page_directory/$slideshow_html_file", $slideshow);

  echo json_encode([
    'properties' => $_POST, 
    'html' => $slideshow,
    'bytes' => $bytes_written
  ]);
?>