<?php
  // error_reporting(E_ALL);
  // ini_set('display_errors', 1);
  
  require __DIR__.'/vendor/autoload.php';
  
  require('Slideshow/SlideshowValidator.php');
  require('Slideshow/SlideshowService.php');
  require('Slideshow/CourseSlideshow.php');
  require('Slideshow/TripSlideshow.php');
  
  use Dotenv\Dotenv;

  use Backend\Slideshow\SlideshowService;
  use Backend\Slideshow\SlideshowValidator;

  $slideshows = [
    'Course' => 'Backend\\Slideshow\\CourseSlideshow',
    'Trip' => 'Backend\\Slideshow\\TripSlideshow',
  ];

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
  $validatedCourseInputs['image_files'] = $_FILES['image_files'];

  $type = $validatedCourseInputs['slideshow_type'];
  // Load environment based on slideshow type
  $dotenv = Dotenv::createImmutable(__DIR__, "$type.env");
  $dotenv->load();

  $slideshow = new $slideshows[$type]($validatedCourseInputs);
  $slideshowService = new SlideshowService($slideshow);
  $slideshowCreated = $slideshowService->createSlideshow();
  // echo json_encode($slideshowCreated);
  
  echo json_encode([
    'post' => $_POST, 
    'files' => $_FILES,
    'env' => $_ENV,
  //   'validated' => $validatedCourseInputs,
    // 'html' => $slideshow,
    // 'bytes' => $bytes_written
  ]);

?>