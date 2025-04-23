document.addEventListener('DOMContentLoaded', function () {
  const gifImages = document.querySelectorAll('div .image-container img, div .gif-container img');
  gifImages.forEach((image) => {
    image.dataset.static = image.src;
    image.addEventListener('click', function () {
      const isPlaying = image.dataset.isPlaying === 'true';
      const gifSrc = image.getAttribute('data-alt');
      const staticSrc = image.dataset.static;
      if (!isPlaying) {
        image.setAttribute('src', gifSrc);
        image.dataset.isPlaying = 'true';
      } else {
        image.setAttribute('src', staticSrc);
        image.dataset.isPlaying = 'false';
      }
    });
  });
});