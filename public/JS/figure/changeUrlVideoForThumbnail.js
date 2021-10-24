// Modification de l'url pour les miniatures des vidéos. 
function urlVideoToPicture(url){
    // console.log(typeof(url));
    url = url.replace('www', 'img'); 
    url = url.replace('embed','vi');
    url = url.replace( "\")", "/mqdefault.jpg\")") 

    return url
}

let htmlCollectionVideo = document.getElementsByClassName('checkVideo');
let arrayVideo = Array.prototype.slice.call(htmlCollectionVideo);

arrayVideo.forEach( vid =>{
    vid.style.backgroundImage = urlVideoToPicture(vid.style.backgroundImage);
})