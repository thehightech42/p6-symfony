

















// Controle avant suppression
// Deleting Element
let deleteElement = 1; 
let allElementsPictures = document.getElementsByClassName('imgAvailable');


let deleteButton = document.getElementById('deleteButton');
if(deleteButton !== null){
  deleteButton.addEventListener('click', (e)=>{

    if ( window.confirm('Voulez vous vraiment supprimer cette figure du site ? La suppression sera définitive et irréversible.') === true ){
      console.log("Suppression en cours.");
    }else{
      e.preventDefault();
    }
  })
}



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
            $.toast({
                icon:'info',
                heading:'Suppression du visuel en cours',
                text: 'Pensez bien à enregistrer les modifications éffectué quand vous avez fini !',
                allowToastClose: 'true',
                hideAfter:'false' 
              }) 

        }

    }
        
}