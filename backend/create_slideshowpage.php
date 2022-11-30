<?php
  // error_reporting(E_ALL);
  // ini_set('display_errors', 1);

  require('Slideshow/SlideshowValidator.php');
  require('Slideshow/SlideshowService.php');
  require('Slideshow/CourseSlideshow.php');
  require('Slideshow/TripSlideshow.php');

  use Backend\Slideshow\SlideshowService;
  use Backend\Slideshow\SlideshowValidator;

  $slideshows = [
    'Course' => 'Backend\\Slideshow\\CourseSlideshow',
    'Trip' => 'Backend\\Slideshow\\TripSlideshow',
  ];

  // echo json_encode([
  //   'post' => $_POST, 
  // ]);

  // die();

  // Validate input
  $validator = new SlideshowValidator();
  
  $validatedCourseInputs = filter_input_array(
    INPUT_POST, 
    [
      'slideshow_type' => ['filter'=>FILTER_CALLBACK, 'options'=>[$validator, 'validateType']],
      'organization' => ['filter'=>FILTER_CALLBACK, 'options'=>[$validator, 'validateOrganization']],
      'day' => ['filter'=>FILTER_CALLBACK, 'options'=>[$validator, 'validateDay']],
      'season' => ['filter'=>FILTER_CALLBACK, 'options'=>[$validator, 'validateSeason']],
      'year' => ['filter'=>FILTER_CALLBACK, 'options'=>[$validator, 'validateYear']],
      'min_grade' => ['filter'=>FILTER_CALLBACK, 'options'=>[$validator, 'validateGrade']],
      'max_grade' => ['filter'=>FILTER_CALLBACK, 'options'=>[$validator, 'validateGrade']],
      'location' => ['filter'=>FILTER_CALLBACK, 'options'=>[$validator, 'sanitizeInput']],
      'new_append' => ['filter'=>FILTER_CALLBACK, 'options'=>[$validator, 'validateNewAppend']],
    ]
  );

  if (isset($_POST['class_time'])) {
    $validatedCourseInputs['class_time'] = $validator->validateTime($_POST['class_time']);
  }

  $validatedCourseInputs['caption'] = $validator->sanitizeArray($_POST['caption']);
  $validatedCourseInputs['image_names'] = $validator->sanitizeArray($_POST['image_names']);
  $validatedCourseInputs['image_groups'] = $_POST['image_groups'];

  $slideshow = new $slideshows[$validatedCourseInputs['slideshow_type']]($validatedCourseInputs);
  $slideshowService = new SlideshowService($slideshow);
  $slideshowCreated = $slideshowService->createSlideshow();
  echo json_encode($slideshowCreated);
  
  // echo json_encode([
  //   'post' => $_POST, 
  //   'validated' => $validatedCourseInputs,
    // 'html' => $slideshow,
    // 'bytes' => $bytes_written
  // ]);

  // Copy image files to webpage image directory
  /*
  foreach ($image_files["error"] as $key => $error) {
    if ($error == UPLOAD_ERR_OK) {
        $tmp_name = $image_files["tmp_name"][$key];
        // basename() may prevent filesystem traversal attacks;
        // further validation/sanitation of the filename may be appropriate
        $name = basename($image_files["name"][$key]);
        move_uploaded_file($tmp_name, "$image_directory/$name");
    }
  }
  */
?>