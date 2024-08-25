function extractImageNames(fileContent) {
  const regex = /\.jpg/gi;
  const matches = fileContent.match(regex);
  return matches ? matches.map(match => match.slice(1)) : [];
}

function createGallery(imageNames) {
  let galleryHtml = '<div class="gallery">';

  imageNames.forEach((imageName, index) => {
    const imagePath = `images/${imageName}.jpg`;
    galleryHtml += `
      <div class="gallery-item">
        <h2>${imageName}</h2>
        <img src="${imagePath}" alt="${imageName}">
      </div>
    `;
  });

  galleryHtml += `
    <div class="gallery-nav">
      <button class="prev-btn">Previous</button>
      <button class="next-btn">Next</button>
    </div>
  </div>`;

  return galleryHtml;
}

function handleGetData(data, sessionName, fileContent) {
  const imageNames = extractImageNames(fileContent);
  const galleryHtml = createGallery(imageNames);

  const galleryContainer = document.getElementById('gallery');
  galleryContainer.innerHTML = galleryHtml;
}

handleGetData(data, sessionName, fileContent);
