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

// Deleting Element

let deleteElement = 1; 
let allElementsPictures = document.getElementsByClassName('imgAvailable');

function deleteFigure(idFigure, type){
    if(allElementsPictures.length > 1 || type != 'img'){

        if(confirm("Voulez vous supprimer cette image ?") === true ){
            console.log('fonction delete');
            let htmlDetete = "<input type='hidden' name='deleteElement"+ deleteElement +"' value="+idFigure+" >";
            
            let elementToRemove = document.getElementById('col-'+idFigure);
            elementToRemove.parentNode.removeChild(elementToRemove);
        
            allElementsPictures[0].childNodes.forEach(element => {
                if(element.nodeName === "INPUT" && element.checked === false){
                    element.checked = true;
                }
            });
            blockHtml.append(htmlDetete);
            deleteElement ++; 

        }

    }
        
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














