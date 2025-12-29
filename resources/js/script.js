
// Mobile menu toggle
function initMobileMenu() {
  const mobileMenuBtn = document.getElementById("mobileMenuBtn")
  const navbarMenu = document.getElementById("navbarMenu")

  if (!mobileMenuBtn) return

  mobileMenuBtn.addEventListener("click", () => {
    mobileMenuBtn.classList.toggle("active")
    navbarMenu.classList.toggle("active")
  })

  // Close menu when clicking on a link
  document.querySelectorAll(".nav-link").forEach((link) => {
    link.addEventListener("click", () => {
      mobileMenuBtn.classList.remove("active")
      navbarMenu.classList.remove("active")
    })
  })
}

// Smooth scroll for navigation links
function initSmoothScroll() {
  document.querySelectorAll("a[href^='#']").forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      const href = this.getAttribute("href")
      if (href !== "#" && href !== "#home") {
        e.preventDefault()
        const target = document.querySelector(href)
        if (target) {
          target.scrollIntoView({
            behavior: "smooth",
            block: "start",
          })
        }
      }
    })
  })
}

// Initialize all functionality
document.addEventListener("DOMContentLoaded", () => {
  console.log("w210 070")

  renderEngines()
  renderBrands()
  initSearchButton()
  initBrandSelector()
  initMobileMenu()
  initSmoothScroll()

  console.log("[v0] Application initialized successfully")
})
