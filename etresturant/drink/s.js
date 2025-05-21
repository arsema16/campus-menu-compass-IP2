const menuIcon = document.getElementById('menu-icon');
const navList = document.querySelector('.navlist');

menuIcon.addEventListener('click', () => {
  console.log('Menu icon clicked');
  navList.classList.toggle('open');
  console.log('Nav list open state:', navList.classList.contains('open'));
});

window.onscroll = () => {
  navList.classList.remove('open');
};
document.addEventListener("DOMContentLoaded", function () {
  const searchIcon = document.getElementById("search-icon");
  const searchBar = document.getElementById("search-bar");

  searchIcon.addEventListener("click", function (e) {
    e.preventDefault();
    searchBar.classList.toggle("visible");
  });
});
