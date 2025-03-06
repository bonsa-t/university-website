// Initialize AOS (Animate On Scroll)
AOS.init({
    duration: 1000,
    once: true,
    offset: 100
});

// Mobile Navigation Toggle
const burger = document.querySelector('.burger');
const navLinks = document.querySelector('.nav-links');
const links = document.querySelectorAll('.nav-links li');

burger.addEventListener('click', () => {
    // Toggle navigation
    navLinks.classList.toggle('nav-active');
    
    // Animate links
    links.forEach((link, index) => {
        if (link.style.animation) {
            link.style.animation = '';
        } else {
            link.style.animation = `navLinkFade 0.5s ease forwards ${index / 7 + 0.3}s`;
        }
    });
    
    // Burger animation
    burger.classList.toggle('toggle');
});

// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
        
        // Close mobile menu if open
        if (navLinks.classList.contains('nav-active')) {
            navLinks.classList.remove('nav-active');
            burger.classList.remove('toggle');
            links.forEach(link => link.style.animation = '');
        }
    });
});


// Add scroll-based navbar styling
window.addEventListener('scroll', () => {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// News and Events Dynamic Loading
const loadNews = async () => {
    try {
        const response = await fetch('/api/news');
        const news = await response.json();
        const newsGrid = document.querySelector('.news-grid');
        
        news.forEach(item => {
            const newsCard = createNewsCard(item);
            newsGrid.appendChild(newsCard);
        });
    } catch (error) {
        console.error('Error loading news:', error);
    }
};

// Department Heads Dynamic Loading
const loadDepartmentHeads = async () => {
    try {
        const response = await fetch('/api/department-heads');
        const heads = await response.json();
        const headsContainer = document.querySelector('.department-heads');
        
        heads.forEach(head => {
            const headCard = createHeadCard(head);
            headsContainer.appendChild(headCard);
        });
    } catch (error) {
        console.error('Error loading department heads:', error);
    }
};

// Initialize dynamic content
document.addEventListener('DOMContentLoaded', () => {
    loadNews();
    loadDepartmentHeads();
});

// Theme toggle functionality
const themeToggle = document.querySelector('.theme-toggle');
const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');

// Check for saved theme preference or default to user's system preference
const currentTheme = localStorage.getItem('theme') || 
                    (prefersDarkScheme.matches ? 'dark' : 'light');

// Set initial theme
document.documentElement.setAttribute('data-theme', currentTheme);

// Toggle theme
themeToggle.addEventListener('click', () => {
  const currentTheme = document.documentElement.getAttribute('data-theme');
  const newTheme = currentTheme === 'light' ? 'dark' : 'light';
  
  document.documentElement.setAttribute('data-theme', newTheme);
  localStorage.setItem('theme', newTheme);
});

// Existing mobile navigation code remains the same

function openImagePreview(imgSrc) {
    const modal = document.getElementById('imagePreviewModal');
    const fullSizeImage = document.getElementById('fullSizeImage');
    
    modal.style.display = "block";
    fullSizeImage.src = imgSrc;
    
    // Close modal when clicking the X
    document.querySelector('.close-modal').onclick = function() {
        modal.style.display = "none";
    }
    
    // Close modal when clicking outside the image
    modal.onclick = function(e) {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    }
}

// Close modal with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById('imagePreviewModal').style.display = "none";
    }
});
// Add this to your existing script
document.addEventListener('DOMContentLoaded', () => {
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                dropdown.classList.toggle('active');
            }
        });
    });
});
