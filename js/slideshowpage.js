const imageChooser = document.querySelector('#image-chooser');
const clearPhotosButton = document.querySelector('#clear-photos');
const imagePreview = document.querySelector('#image-preview');
const slideshowForm = document.querySelector('.slideshow-form');
const groupCaptionInput = document.querySelector('#image-group-name');
const numSelectedImages = document.querySelector('#number-photos-selected');
const groupButton = document.querySelector('#group-images');
const captionButton = document.querySelector('#caption-images');
const groupAndCaptionButton = document.querySelector('#group-caption-images');
const appendSlideshow = document.querySelector('#append-slideshow');
const createSlideshow = document.querySelector('#create-slideshow');

let draggedThumbnail;
let draggedThumbnailGroup = -1;
let droppedOnThumbnailGroup = -1;
let draggedThumbnailIndex = -1;
let droppedOnThumbnailIndex = -1;
let draggedImageName = '';

let selectedThumbnails = {thumbnail: [], name: []};
let imageGroups = [];

const deleteThumbnail = thumbnail => {
  const thumbnailImage = thumbnail.children.namedItem('image_names[]').value;
  const thumbnailNameIndex = selectedThumbnails.name.indexOf(thumbnailImage);
  // remove from selectedThumbnails
  selectedThumbnails.thumbnail.splice(thumbnailNameIndex, 1);
  selectedThumbnails.name.splice(thumbnailNameIndex, 1);
  // If image is in a group - find and remove
  if (thumbnail.dataset.groupNumber >= 0) {
    const groupIndex = parseInt(thumbnail.dataset.groupNumber);
    const imageIndex = imageGroups[groupIndex].indexOf(thumbnailImage);
    imageGroups[groupIndex].splice(imageIndex, 1);
  }
  // remove thumbnail container and children
  thumbnail.remove();
}

// Selected images functions
const captionSelected = thumbnails => {
  thumbnails.forEach (thumbnail => {
    const thumbnailParent = thumbnail.parentElement;
    thumbnailParent.querySelector('.caption').value = groupCaptionInput.value;
  });
}

const clearSelection = () => {
  selectedThumbnails.thumbnail.forEach (thumbnail => {
    thumbnail.classList.remove('selected');
  });
  selectedThumbnails.thumbnail = [];
  selectedThumbnails.name = [];
  numSelectedImages.textContent = 0;
  groupCaptionInput.value = '';
  console.log(imageGroups);
}

const groupSelected = (thumbnails, imageNames) => {
  imageGroups.push([...imageNames]);
  const groupNumber = imageGroups.length - 1;
  thumbnails.forEach (thumbnail => {
    const thumbnailParent = thumbnail.parentElement;
    thumbnailParent.dataset.groupNumber = groupNumber;
  });
}

// Drag and drop functions
const getDragDropContainer = target => {
  let targetThumbnailContainer;
  // No matter which element in the container is grabbed, the whole container should be dragged/dropped
  if (target.classList.contains('thumbnail-container')) {
    targetThumbnailContainer = target;
  } else if (target.classList.contains('caption')) {
    // item grabbed is caption input, whose parent is a label, whose parent is the container
    targetThumbnailContainer = target.parentElement.parentElement;
  } else {
    // item grabbed is child of the container
    targetThumbnailContainer = target.parentElement;
  }

  return targetThumbnailContainer;
}

const resetDragDropParameters = () => {
  draggedThumbnailGroup = -1;
  droppedOnThumbnailGroup = -1;
  draggedThumbnailIndex = -1;
  droppedOnThumbnailIndex = -1;
  draggedImageName = '';
}

// Image group functions used in drag and drop
const groupIndex = (thumbnail, groupNumber) => {
    // get index of this thumbnail in the group
    const group = document.querySelectorAll(`.thumbnail-container[data-group-number="${groupNumber}"]`);
    const indexInGroup = [...group].indexOf(thumbnail);
    return indexInGroup;
}

const isAtGroupEnd = (target, groupNumber) => {
  const thumbnailContainers = document.querySelectorAll('.thumbnail-container');
  const dropIndex = [...thumbnailContainers].indexOf(target);
  if (dropIndex - 1 < 0 || thumbnailContainers[dropIndex - 1].dataset.groupNumber !== groupNumber) {
    return false;
  }
  return true;
}

const pushToGroupEnd = (groupNumber, oldIndex) => {
  draggedImageName = imageGroups[groupNumber][oldIndex];
  // Insert at end of image group
  imageGroups[groupNumber].push(draggedImageName);
  // Remove from old index
  imageGroups[groupNumber].splice(oldIndex, 1);
}

const reorderGroup = (target, targetGroup, oldIndex) => {
  const newIndex = groupIndex(target, targetGroup);
  draggedImageName = imageGroups[targetGroup][oldIndex];
  // Insert at new index
  imageGroups[targetGroup].splice(newIndex, 0, draggedImageName);
  // Remove from old index
  if (newIndex > oldIndex) {
    imageGroups[targetGroup].splice(oldIndex, 1);
  } else {
    imageGroups[targetGroup].splice(oldIndex + 1, 1);
  }
}

// Event Handlers
const handleClearPhotos = ev => {
  const thumbnails = [...imagePreview.children];
  thumbnails.forEach(thumbnail => deleteThumbnail(thumbnail));
}

const handleThumbnailDelete = ev => {
  const selectedThumbnail = ev.target.parentElement;
  deleteThumbnail(selectedThumbnail);
}

const handleThumbnailDragEnter = ev => {
  ev.preventDefault();
}

const handleThumbnailDragOver = ev => {
  ev.preventDefault();
}

const handleThumbnailDragStart = ev => {
  draggedThumbnail = getDragDropContainer(ev.target);
  
  draggedThumbnailGroup = draggedThumbnail.dataset.groupNumber;
  if (draggedThumbnailGroup >= 0) {
    draggedThumbnailIndex = groupIndex(draggedThumbnail, draggedThumbnailGroup);
  }
}

const handleThumbnailDrop = ev => {
  dropTarget = getDragDropContainer(ev.target);

  let okToMove = false;
  // Determine if image is part of a group - if it is, determine if its new location
  // is part of the same group
  droppedOnThumbnailGroup = dropTarget.dataset.groupNumber;
  if (droppedOnThumbnailGroup == draggedThumbnailGroup && draggedThumbnailIndex >= 0) {
    reorderGroup(dropTarget, droppedOnThumbnailGroup, draggedThumbnailIndex);
    okToMove = true;
  } else if (draggedThumbnailGroup >= 0 && isAtGroupEnd(dropTarget, draggedThumbnailGroup)) {
    pushToGroupEnd(draggedThumbnailGroup, draggedThumbnailIndex);
    okToMove = true;
  } else if (draggedThumbnailGroup == -1 && droppedOnThumbnailGroup == -1) {
    // If neither the dragged image or the drop target are part of a group, it's OK to move
    okToMove = true;
  }

  if (!okToMove) {
    console.log('Illegal Move');
    return;
  }

  imagePreview.insertBefore(draggedThumbnail, dropTarget);

  // Reset drag,drop indexes and groups
  resetDragDropParameters();
}

const handleFormSubmit = async ev => {
  ev.preventDefault();
  const slideshowData = new FormData(slideshowForm);
  slideshowData.append('new_append', ev.target.value);
  slideshowData.append('image_groups', JSON.stringify(imageGroups));
  const resp = await fetch('backend/create_slideshowpage.php', {
    method: 'POST',
    body: slideshowData,
  });
  const result = await resp.json();
  console.log(result);
}

const handleSelectedImagesOp = ev => {
  const op = ev.target.id;
  // Group - used Group and Group & Caption buttons
  if (op === 'group-images' || op === 'group-caption-images') {
    groupSelected(selectedThumbnails.thumbnail, selectedThumbnails.name);
  }
  // Caption - used by Caption and Group & Caption buttons
  if (op === 'caption-images' || op === 'group-caption-images') {
    captionSelected(selectedThumbnails.thumbnail);
  }
  // Clear selection - used by Group, Caption, Group & Caption buttons
  clearSelection();
}

const handleThumbnailClick = ev => {
  const selected = ev.target;

  if (selected.classList.contains('selected')) {
    selected.classList.remove('selected');
    const indexSelected = selectedThumbnails.thumbnail.indexOf(selected);
    const indexSelectedFileName = selectedThumbnails.name.indexOf(selected.file.name);
    selectedThumbnails.thumbnail.splice(indexSelected, 1);
    selectedThumbnails.name.splice(indexSelectedFileName, 1);
    console.log(selectedThumbnails);
  } else {
    selected.classList.add('selected');
    selectedThumbnails.thumbnail.push(selected);
    selectedThumbnails.name.push(selected.file.name);
  }
  numSelectedImages.textContent = selectedThumbnails.thumbnail.length;
}

// Assign Event Handlers
clearPhotosButton.addEventListener('click', handleClearPhotos);

const imagePreviewClickHandlers = {
  'button': handleThumbnailDelete,
  'img': handleThumbnailClick,
};

imagePreview.addEventListener('click', ev => {
  const handlerType = ev.target.tagName.toLowerCase();
  if (handlerType in imagePreviewClickHandlers) {
    imagePreviewClickHandlers[handlerType](ev);
  }
});

const imagePreviewDragDropHandlers = {
  'dragenter': handleThumbnailDragEnter,
  'dragover': handleThumbnailDragOver,
  'dragstart': handleThumbnailDragStart,
  'drop': handleThumbnailDrop,
};

const handleThumbnailDragDropOp = ev => {
  if (ev.target.id !== 'image-preview') {
    imagePreviewDragDropHandlers[ev.type](ev);
  }
}

imagePreview.addEventListener('dragenter', handleThumbnailDragDropOp);
imagePreview.addEventListener('dragover', handleThumbnailDragDropOp);
imagePreview.addEventListener('dragstart', handleThumbnailDragDropOp);
imagePreview.addEventListener('drop', handleThumbnailDragDropOp);

imageChooser.addEventListener('change', ev => {
  for (let i=0;i<imageChooser.files.length;i++) {
    const thumbnailContainer = document.createElement('div');
    thumbnailContainer.tabIndex = -1;
    thumbnailContainer.classList.add('thumbnail-container');
    thumbnailContainer.draggable = true;
    thumbnailContainer.dataset.groupNumber = -1;
    imagePreview.appendChild(thumbnailContainer);

    // Add delete button
    const deleteButton = document.createElement('button');
    deleteButton.classList.add('slideshow-button', 'delete-button')
    deleteButton.type = 'button';
    deleteButton.ariaLabel = 'Remove Thumbnail';
    thumbnailContainer.appendChild(deleteButton);

    const photoFile = imageChooser.files[i];
    const photo = document.createElement('img');
    photo.classList.add('slideshow-thumbnail');
    photo.file = photoFile;
    thumbnailContainer.appendChild(photo);

    const imageReader = new FileReader();
    imageReader.addEventListener('load', ev => photo.src = ev.target.result);
    imageReader.readAsDataURL(photoFile);

    // Add hidden input with image file name
    const imageName = document.createElement('input');
    imageName.type = 'text'
    imageName.name = 'image_names[]';
    imageName.value = photoFile.name;
    imageName.hidden = true;
    thumbnailContainer.appendChild(imageName);

    // Add thumbnail caption input
    const caption = document.createElement('input');
    caption.type = 'text'
    caption.name = 'caption[]';
    caption.classList.add('caption');
    const captionLabel = document.createElement('label');
    captionLabel.textContent = 'Photo Caption';
    captionLabel.append(caption);
    thumbnailContainer.appendChild(captionLabel);
  }
});

groupButton.addEventListener('click', handleSelectedImagesOp);
captionButton.addEventListener('click', handleSelectedImagesOp);
groupAndCaptionButton.addEventListener('click', handleSelectedImagesOp);

appendSlideshow.addEventListener('click', handleFormSubmit);
createSlideshow.addEventListener('click', handleFormSubmit);
// console.log(result.html);
// console.log(result.bytes);

