const imageChooser = document.querySelector('#image-chooser');
const imagePreview = document.querySelector('#image-preview');
const slideshowForm = document.querySelector('.slideshow-form');
const nameToCaption = document.querySelector('#name-to-caption');
const imageGroupName = document.querySelector('#image-group-name');
const groupImages = document.querySelector('#group-images');
const appendSlideshow = document.querySelector('#append-slideshow');
const createSlideshow = document.querySelector('#create-slideshow');

let selectedThumbnails = {thumbnail: [], name: []};
let imageGroups = [];

const handleKeyPress = ev => {
  if (ev.key === 'Delete') {
    // console.log(ev.target, selectedThumbnails.thumbnail.indexOf(ev.target.children[0]));
  }
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
};

const toggleNameToCaption = () => {
  imageGroupName.disabled = !imageGroupName.disabled;
}

imageChooser.addEventListener('change', ev => {
  for (let i=0;i<imageChooser.files.length;i++) {
    const thumbnailContainer = document.createElement('div');
    thumbnailContainer.tabIndex = -1;
    thumbnailContainer.classList.add('thumbnail-container');
    thumbnailContainer.addEventListener('keydown', handleKeyPress);
    imagePreview.appendChild(thumbnailContainer);

    const photoFile = imageChooser.files[i];
    const photo = document.createElement('img');
    photo.classList.add('slideshow-thumbnail');
    photo.file = photoFile;
    thumbnailContainer.appendChild(photo);

    const imageReader = new FileReader();
    imageReader.addEventListener('load', ev => photo.src = ev.target.result);
    imageReader.readAsDataURL(photoFile);

    photo.addEventListener('click', handleThumbnailClick);

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

nameToCaption.addEventListener('change', toggleNameToCaption);

groupImages.addEventListener('click', () => {
  imageGroups.push([...selectedThumbnails.name]);
  selectedThumbnails.thumbnail.forEach (thumbnail => {
    thumbnail.classList.remove('selected');
    if (!imageGroupName.disabled) {
      thumbnail.parentElement.querySelector('.caption').value = imageGroupName.value;
    }
  });
  selectedThumbnails.thumbnail = [];
  selectedThumbnails.name = [];
  imageGroupName.value = '';
  console.log(imageGroups);
});

appendSlideshow.addEventListener('click', async ev => {
  ev.preventDefault();
  const slideshowData = new FormData(slideshowForm);
  slideshowData.append('projects', JSON.stringify(imageGroups));
  const resp = await fetch('append_slideshowpage.php', {
    method: 'POST',
    body: slideshowData,
  });
  const result = await resp.json();
  console.log(result.html);
  console.log(result.bytes);
});

createSlideshow.addEventListener('click', async ev => {
  ev.preventDefault();
  const slideshowData = new FormData(slideshowForm);
  slideshowData.append('image_groups', JSON.stringify(imageGroups));
  const resp = await fetch('create_slideshowpage.php', {
    method: 'POST',
    body: slideshowData,
  });
  const result = await resp.json();
  console.log(result);
});
