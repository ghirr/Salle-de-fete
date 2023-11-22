// flashMessage.js

// Get the Réserver button element
var reserverButton = document.getElementById('reserverButton');

// Add a click event listener to the Réserver button
reserverButton.addEventListener('click', function() {
    // Load the flash message script
    var flashMessageScript = document.createElement('script');
    flashMessageScript.src = '/flashMessage.js';
    document.head.appendChild(flashMessageScript);
});
