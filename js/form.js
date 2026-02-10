const form = document.getElementById("itemForm");
const founderInput = form.querySelector('input[type="text"]');
const typeInput = form.querySelector('select');
const descInput = form.querySelector('textarea');
const imageInput = form.querySelector('input[type="file"]');
const preview = document.getElementById("preview");

// Image preview
imageInput.addEventListener("change", () => {
  const file = imageInput.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = () => {
    preview.src = reader.result;
    preview.style.display = "block";
  };
  reader.readAsDataURL(file);
});

// Submit form
form.addEventListener("submit", function(e) {
  e.preventDefault();

  const items = JSON.parse(localStorage.getItem("items")) || [];

  const newId = items.length > 0 ? items[items.length - 1].id + 1 : 1;

  const newItem = {
    id: newId,
    founder: founderInput.value,
    type: typeInput.value,
    description: descInput.value,
    image: preview.src || "",
    timestamp: new Date().getTime() // for newest & 1 month limit
  };

  items.push(newItem);

  window.location.href = "index.html";
});
