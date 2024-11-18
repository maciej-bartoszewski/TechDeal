function updateImagePreview() {
    const imagePath = document.getElementById('image_path').value;
    document.getElementById('image_preview').src = imagePath;
}