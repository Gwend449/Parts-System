// Engine data
const engines = [
  {
    id: 1,
    model: "2TR-FE",
    name: "Toyota Hiace",
    price: "₽ 145 000",
    power: "160 л.с.",
    year: "2010-2020",
    image: "/toyota-engine-metallic.jpg",
  },
  {
    id: 2,
    model: "N52B30",
    name: "BMW X5 E70",
    price: "₽ 285 000",
    power: "258 л.с.",
    year: "2006-2013",
    image: "/bmw-engine-industrial.jpg",
  },
  {
    id: 3,
    model: "1MZ-FE",
    name: "Toyota Camry",
    price: "₽ 165 000",
    power: "192 л.с.",
    year: "1997-2005",
    image: "/toyota-camry-engine-shiny.jpg",
  },
  {
    id: 4,
    model: "M54B30",
    name: "BMW 525i",
    price: "₽ 195 000",
    power: "218 л.с.",
    year: "2004-2010",
    image: "/bmw-mechanical-engine-dark.jpg",
  },
]

function renderEngines() {
  const container = document.getElementById("enginesContainer")

  engines.forEach((engine) => {
    const col = document.createElement("div")
    col.className = "col-md-6 col-lg-3"

    col.innerHTML = `
            <div class="card engine-card h-100 border-0">
                <div class="engine-image">
                    <img src="${engine.image}" alt="${engine.name}" class="card-img-top">
                </div>
                <div class="card-body">
                    <p class="text-secondary small mb-1">${engine.model}</p>
                    <h5 class="card-title fw-bold">${engine.name}</h5>
                    <div class="d-flex justify-content-between text-secondary small my-2">
                        <span>${engine.power}</span>
                        <span>${engine.year}</span>
                    </div>
                    <div class="border-top my-2 pt-2">
                        <p class="text-danger fw-bold fs-5 mb-2">${engine.price}</p>
                        <button class="btn btn-danger w-100 btn-sm fw-bold">Подробнее</button>
                    </div>
                </div>
            </div>
        `

    container.appendChild(col)
  })
}

document.getElementById("brandSelect").addEventListener("change", (e) => {
  console.log("Selected brand:", e.target.value)
  // search logic
})

// cta
document.querySelector(".glow-red").addEventListener("click", () => {
  const brand = document.getElementById("brandSelect").value
  if (brand !== "Выберите марку") {
    alert(`Поиск двигателя для: ${brand}`)
    // search filter
  } else {
    alert("Пожалуйста, выберите марку автомобиля")
  }
})

document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
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

document.addEventListener("DOMContentLoaded", () => {
  renderEngines()
})
