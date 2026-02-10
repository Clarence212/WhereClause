function zoomImage(src) {
  const modal = document.getElementById("modal");
  const img = document.getElementById("modalImg");
  modal.style.display = "flex";
  img.src = src;
}

function closeModal() {
  document.getElementById("modal").style.display = "none";
}
