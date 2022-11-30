<?php
  namespace Backend\Slideshow;

  class SlideshowValidator {
    private $slideshowTypes = ['Course', 'Trip'];
    private $slideshowOrganizations = ['ABC', 'FLA'];
    private $slideshowDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
    private $slideshowTimes = ['M', 'A'];
    private $slideshowSeasons = ['Winter', 'Spring', 'Summer', 'Fall'];
    private $slideshowGrades = ['K', '1', '2', '3', '4', '5', '6', '7', '8'];
    private $slideshowNewAppend = ['append', 'new'];

    public function validateSlideshowInput($inp, $inpArray) {
      if (!in_array($inp, $inpArray, true)) {
        return false;
      }
      return $inp;
    }
  
    public function validateType($inp) {
      return $this->validateSlideshowInput($inp, $this->slideshowTypes);
    }
  
    public function validateOrganization($inp) {
      return $this->validateSlideshowInput($inp, $this->slideshowOrganizations);
    }
  
    public function validateDay($inp) {
      return $this->validateSlideshowInput($inp, $this->slideshowDays);
    }
  
    public function validateTime($inp) {
      return $this->validateSlideshowInput($inp, $this->slideshowTimes);
    }
  
    public function validateSeason($inp) {
      return $this->validateSlideshowInput($inp, $this->slideshowSeasons);
    }
  
    public function validateGrade($inp) {
      return $this->validateSlideshowInput($inp, $this->slideshowGrades);
    }
  
    public function validateNewAppend($inp) {
      return $this->validateSlideshowInput($inp, $this->slideshowNewAppend);
    }
  
    public function validateYear($inp) {
      if (!(ctype_digit($inp) && strlen($inp) === 4)) {
        return false;
      }
      return $inp;
    }
  
    public function sanitizeInput($inp) {
      return htmlspecialchars($inp, ENT_NOQUOTES);
    }
  
    public function sanitizeArray($arr) {
      return array_map([$this, 'sanitizeInput'], $arr);
    }
  }