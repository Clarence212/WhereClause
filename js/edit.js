const editId = localStorage.getItem("editId");
if(!editId) window.location.href="index.html";

let items = JSON.parse(localStorage.getItem("items")) || [];
let index = items.findIndex(i => i.id == editId);
if(index === -1) window.location.href="index.html";

let item = items[index];

const founderInput = document.getElementById("founder");
const typeInput = document.getElementById("type");
const descInput = document.getElementById("description");
const imageInput = document.getElementById("image");
const preview = document.getElementById("imagePreview");

// Fill form
founderInput.value = item.founder;
typeInput.value = item.type;
descInput.value = item.description;
if(item.image){ preview.src = item.image; preview.style.display="block"; }

imageInput.addEventListener("change",()=>{
    const file = imageInput.files[0];
    if(!file) return;
    const reader = new FileReader();
    reader.onload = ()=>{ preview.src = reader.result; preview.style.display="block"; }
    reader.readAsDataURL(file);
});

document.getElementById("editForm").addEventListener("submit",function(e){
    e.preventDefault();
    items[index].founder = founderInput.value;
    items[index].type = typeInput.value;
    items[index].description = descInput.value;
    if(imageInput.files.length>0) items[index].image = preview.src;
    items[index].timestamp = new Date().getTime(); // newest first
    localStorage.setItem("items",JSON.stringify(items));
    localStorage.removeItem("editId");
    window.location.href="index.html";
});