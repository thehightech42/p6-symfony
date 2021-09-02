// Code du slider
var slideIndex = 1;  // On début avec la slide 1
showSlides(slideIndex); // On affiche la première slide

function plusSlides(n) {
  showSlides(slideIndex += n); // On passe à la slide suivant en prenant l'index de la slide en cours et en faisant +1
}

function currentSlide(n) {
  showSlides(slideIndex = n); //On passe à la slide suivant en prenant l'index de la slide en cours et en faisant -1
}

function showSlides(n) { // Fonction d'affichage de la slide en cours
  var i; // On crée une variable i utilisé pour l'index des boucles
  var slides = document.getElementsByClassName("mySlides"); // On crée la variables slide qui contiendra toutes les slides
  var dots = document.getElementsByClassName("dot-img"); // On crée la variables dots qui contiendra toutes les dots. 

  
  if (n > slides.length) {slideIndex = 1}   // Quand on arrive au bout on recommance 
  if (n < 1) {slideIndex = slides.length} // Quand on revient en arrière on retourne au max

  for (i = 0; i < slides.length; i++) { //Boucle pour mettre l'ensemble des images en display none
      slides[i].style.display = "none";  
  }
  for (i = 0; i < dots.length; i++) { /// boucle pour retirer toutes class active possible des dotes
      dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "block";  // On passe en display block la slide en cours
  dots[slideIndex-1].className += " active"; // On ajoute la classe active à la dot utilisé
}


// Pour récupérer les minatures des vidéos youtube
function urlVideoToPicture(url){
    url = url.replace('www', 'img'); 
    url = url.replace('embed','vi');
    url = url.replace( "\")", "/mqdefault.jpg\")") 
    return url
}

let htmlCollectionVideo = document.getElementsByClassName('dotImgVideo');
let arrayVideo = Array.prototype.slice.call(htmlCollectionVideo);

console.log(arrayVideo);

arrayVideo.forEach( vid =>{
    vid.style.backgroundImage = urlVideoToPicture(vid.style.backgroundImage);
})