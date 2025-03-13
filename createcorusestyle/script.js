const starsContainer = document.querySelector('.stars-container');

// Function to create falling stars
function createFallingStar() {
    const star = document.createElement('div');
    star.classList.add('star', 'falling');

    // Randomize position and animation duration
    star.style.left = Math.random() * 100 + 'vw'; // Position across the width of the viewport
    star.style.animationDuration = Math.random() * 3 + 2 + 's'; // Random duration between 2s to 5s

    // Randomly assign color: 50% chance for yellow or white
    star.style.background = Math.random() < 0.5 ? 'yellow' : 'white';

    starsContainer.appendChild(star);

    // Remove the star after it has fallen
    star.addEventListener('animationend', () => {
        star.remove();
    });
}

// Generate falling stars at intervals
setInterval(createFallingStar, 300); // Adjust the interval as needed
