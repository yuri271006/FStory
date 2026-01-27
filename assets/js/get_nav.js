// assets/js/get_nav.js
document.addEventListener("DOMContentLoaded", function() {
    let currentPath = window.location.pathname.split("/").pop();
    currentPath = currentPath.replace(/\.(php|html)$/, "");
    
    if (currentPath === "" || currentPath === "fstory" || currentPath === "index") {
        currentPath = "./";
    }

    const navItems = document.querySelectorAll('.main-nav .nav-link, .bottom-nav .nav-item');
    navItems.forEach(item => {
        let linkTarget = item.getAttribute('href').replace(/\.(php|html)$/, "");
        if (linkTarget === currentPath) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
});