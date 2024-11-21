// Select the header text elements
const headerText = document.querySelector('.header-text p');
const headerSubText = document.querySelector('.header-text h1');

// Function to add animation classes
function animateHeader() {
    headerText.classList.add('fade-in');
    headerSubText.classList.add('slide-in');
}

// Trigger animation on page load
window.addEventListener('load', animateHeader);