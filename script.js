// Swiper.js Configuration
document.addEventListener("DOMContentLoaded", function () {
    var swiper = new Swiper(".mySwiper", {
        slidesPerView: 4,  
        spaceBetween: 20,  
        loop: true,  
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        breakpoints: {
            320: { slidesPerView: 1 }, 
            768: { slidesPerView: 2 }, 
            1024: { slidesPerView: 3 }, 
            1200: { slidesPerView: 4 }  
        }
    });

    // Toggle Navigation Menu
    const menuBtn = document.querySelector(".menu-btn");
    const navMenu = document.querySelector("nav ul");
    menuBtn.addEventListener("click", function () {
        navMenu.classList.toggle("show");
    });

    // User Dropdown Toggle
    const userIcon = document.getElementById("userIcon");
    const userDropdown = document.getElementById("userDropdown");
    const signupForm = document.getElementById("signupForm");
    const loginForm = document.getElementById("loginForm");
    const toggleToLogin = document.getElementById("toggleToLogin");
    const toggleToSignup = document.getElementById("toggleToSignup");

    userIcon.addEventListener("click", function (event) {
        event.preventDefault();
        userDropdown.style.display = userDropdown.style.display === "block" ? "none" : "block";
    });

    toggleToLogin.addEventListener("click", function (event) {
        event.preventDefault();
        signupForm.style.display = "none";
        loginForm.style.display = "block";
    });

    toggleToSignup.addEventListener("click", function (event) {
        event.preventDefault();
        signupForm.style.display = "block";
        loginForm.style.display = "none";
    });

    document.addEventListener("click", function (event) {
        if (!userIcon.contains(event.target) && !userDropdown.contains(event.target)) {
            userDropdown.style.display = "none";
        }
    });

    // Signup Form Submission
    document.getElementById("signupForm").addEventListener("submit", function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        fetch("signup.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.status === "success") {
                document.getElementById("userIcon").innerHTML = `<i class="fas fa-user"></i> ${data.user}`;
                document.getElementById("userDropdown").innerHTML = `
                    <p>Welcome, ${data.user}!</p>
                    <a href="settings.php">Settings</a>
                    <a href="logout.php">Logout</a>
                `;
            }
        })
        .catch(error => console.error("Error:", error));
    });

    // Live Search Functionality
    const searchInput = document.getElementById("searchInput");
    const searchResults = document.getElementById("searchResults");

    searchInput.addEventListener("keyup", function () {
        let query = searchInput.value.trim();

        if (query.length > 1) {
            fetch("search.php", {
                method: "POST",
                body: new URLSearchParams({ query: query }),
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
            })
            .then((response) => response.text())
            .then((data) => {
                searchResults.innerHTML = data;
                searchResults.style.display = "block";
            });
        } else {
            searchResults.style.display = "none";
        }
    });

    document.addEventListener("click", function (event) {
        if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
            searchResults.style.display = "none";
        }
    });

    // Smooth Scrolling for Navigation
    document.querySelectorAll(".nav a").forEach(link => {
        link.addEventListener("click", function (e) {
            e.preventDefault();

            let targetSection = document.querySelector(this.getAttribute("href"));
            if (targetSection) {
                window.scrollTo({
                    top: targetSection.offsetTop - 80, 
                    behavior: "smooth"
                });
            }
        });
    });
});
