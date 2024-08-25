const popupContainer = document.getElementById("popupContainer");
const inputField = document.getElementById("inputField");
const closeBtn = document.querySelector(".close-btn");
const submitBtn = document.getElementById("submitBtn");
const menu = document.querySelector(".navbar-collapse"); // Add this line to get the menu element

// Open the popup when "New" link is clicked
const newLink = document.querySelector(".nav-link[href='add.php']");
newLink.addEventListener("click", (e) => {
  e.preventDefault();
  popupContainer.style.display = "block";
  menu.classList.remove("show"); // Add this line to hide the menu
});

// Close the popup when the close button is clicked
closeBtn.addEventListener("click", () => {
  popupContainer.style.display = "none";
});

// Close the popup when clicking outside of the popup
window.addEventListener("click", (e) => {
  if (e.target === popupContainer) {
    popupContainer.style.display = "none";
  }
});
// Restrict "." and " " characters in the input field
inputField.addEventListener("keypress", (e) => {
  if (e.key === "." || e.key === '"') {
    e.preventDefault();
  }
});

// Send data to create_new.php when submit button is clicked
submitBtn.addEventListener("click", () => {
  const inputValue = inputField.value.trim();
  if (inputValue !== "") {
    sendDataToServer(inputValue);
  }
});

// AJAX function to send data to the server
function sendDataToServer(data) {
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "create_new.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function() {
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
      console.log(xhr.responseText);
      // Handle the server response here if needed
      popupContainer.style.display = "none";
      inputField.value = "";
    }
  };
  xhr.send("data=" + encodeURIComponent(data));
}
