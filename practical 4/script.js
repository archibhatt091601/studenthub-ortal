/* =========================================
   StudentHub Portal - script.js
   Simple beginner-level JavaScript
   ========================================= */

// 1. Highlight the current page's link in the nav menu
window.addEventListener("DOMContentLoaded", function () {
  var links = document.querySelectorAll("nav.ttt a");
  var currentPage = window.location.pathname.split("/").pop();

  for (var i = 0; i < links.length; i++) {
    var linkPage = links[i].getAttribute("href");
    if (linkPage === currentPage) {
      links[i].style.backgroundColor = "#ff9800";
    }
  }
});


// 2. Simple form validation
// Works on any page that has a <form> (Register, Login, Contact, Feedback)
var myForm = document.querySelector("form");

if (myForm) {
  myForm.addEventListener("submit", function (event) {
    // Always stop the page from actually reloading (no backend yet)
    event.preventDefault();

    var textInputs = myForm.querySelectorAll(
      "input[type='text'], input[type='password'], input[type='tel'], textarea"
    );
    var emailInputs = myForm.querySelectorAll("input[type='email']");
    var isValid = true;

    // Check that normal text fields are not empty
    for (var i = 0; i < textInputs.length; i++) {
      if (textInputs[i].value.trim() === "") {
        isValid = false;
        break;
      }
    }

    // Very simple email check
    for (var j = 0; j < emailInputs.length; j++) {
      var emailValue = emailInputs[j].value.trim();
      if (emailValue === "" || emailValue.indexOf("@") === -1) {
        isValid = false;
      }
    }

    if (!isValid) {
      alert("Please fill in all the fields correctly before submitting.");
    } else {
      alert("Form submitted successfully!");
      myForm.reset();
    }
  });
}


// 3. Dashboard sidebar menu - click an item to make it active
var menuItems = document.querySelectorAll(".side-menu li");

for (var k = 0; k < menuItems.length; k++) {
  menuItems[k].addEventListener("click", function () {
    var allItems = document.querySelectorAll(".side-menu li");
    for (var m = 0; m < allItems.length; m++) {
      allItems[m].classList.remove("active");
    }
    this.classList.add("active");
  });
}


// 4. Character counter under any textarea (Contact / Feedback pages)
var textArea = document.querySelector("textarea");

if (textArea) {
  var counter = document.createElement("p");
  counter.style.fontSize = "13px";
  counter.style.color = "#777777";
  counter.textContent = "0 characters typed";
  textArea.parentNode.insertBefore(counter, textArea.nextSibling);

  textArea.addEventListener("input", function () {
    counter.textContent = textArea.value.length + " characters typed";
  });
}


// 5. Show the current year automatically in the footer
var footerText = document.querySelector("footer p");

if (footerText) {
  var currentYear = new Date().getFullYear();
  footerText.innerHTML = footerText.innerHTML.replace("2026", currentYear);
}
