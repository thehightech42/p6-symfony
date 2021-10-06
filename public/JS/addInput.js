console.log('addInput.js');

let videoElement = 1; 
let pictureElement = 1; 
let blockHtml = $('#BlocInputVisuel');

function addPicture(){
    console.log("Ajout d'une image")
    let htmlPicture = "<div class='form-group'>";
    htmlPicture +=       "<label for='inputGroupFile"+ pictureElement.toString() +"' class='form-label'>Ajouter une photo</label>";
    htmlPicture +=       "<input type='file' name='inputGroupFile"+ pictureElement.toString() +"' class='form-control' id='inputGroupFile"+ pictureElement.toString() +"'>";
    htmlPicture +=    "</div>";

    blockHtml.append(htmlPicture);

    pictureElement++;
}

function addVideo(){
    console.log("Ajoute d'une vidéo");

    let htmlVideo = "<div class='form-group'>";
    htmlVideo +=        "<label for='inputVideoUrl"+ videoElement +"'>Ajouter une vidéo</label>";
    htmlVideo +=        "<input type='text' id='inputVideoUrl' name='inputVideoUrl"+ videoElement +"' placeholder='Url de la vidéo' class='form-control'>";
    htmlVideo +=    "</div>";

    blockHtml.append(htmlVideo);
    videoElement++;
}


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
