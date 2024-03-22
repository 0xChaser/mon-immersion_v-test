let openBtn = document.getElementById("menu_ouvert");
let closeBtn = document.getElementById("menu_ferme");
let navWrapper = document.getElementById("nav-wrapper");
let navLatteral = document.getElementById("menu_lateral");

const openNav = () => {
  navWrapper.classList.add("active");
  navLatteral.style.left = "0";
};

const closeNav = () => {
  navWrapper.classList.remove("active");
  navLatteral.style.left = "-100%";
};

openBtn.addEventListener("click", openNav);
closeBtn.addEventListener("click", closeNav);
navWrapper.addEventListener("click", closeNav);





