// On selectonne tous les elements

// elements = document.querySelectorAll("[data-delete]");
// function arrayImg(){
//     let arrayImg = [];
//     document.querySelectorAll("[data-type]").forEach(image =>{ 
//         if(image.getAttribute('data-type') === 'picture'){
//             arrayImg.push(image);
//         }    
//     })
//     return arrayImg;
// }

function inputImg(){
    let inputs = document.getElementsByClassName('inputVisuelFigureImg');
    return inputs;
}
console.log(inputImg());

document.querySelectorAll("[data-delete]").forEach(element => {
    console.log(element.getAttribute("data-type"));
    element.addEventListener('click', (e)=>{
        e.preventDefault();
        if(inputImg().length > 1 || element.getAttribute("data-type") === "video"){
            let sentence = ""
            if(element.getAttribute("data-type") === "picture"){
                sentence = "Voulez vous supprimer cette image ? ATTENTION cette manipulation est irréversible"
            }else{
                sentence = "Voulez vous supprimer cette vidéo ? ATTENTION cette manipulation est irréversible"
            }
            if(confirm(sentence) === true ){
                let data = JSON.stringify({'_token' : element.getAttribute('data-token'), '_idVisuelFigure' : element.getAttribute("data-delete") });

                jQuery.ajax({
                    type: "DELETE",
                    async: true, 
                    url : element.getAttribute("href"),
                    data: data,
                    success : function(jsonData){
                        $.toast({
                            heading: 'Success',
                            text: "L'element a correctement été supprimé",
                            showHideTransition: 'slide',
                            icon: 'success',
                            loader: true
                        });
                        console.log(jsonData);
                        document.getElementById("col-"+ element.getAttribute("data-delete")).remove();
                        if(jsonData['newMain']){
                            document.getElementById(jsonData['newMain']).setAttribute('checked', 'checked');
                        }
                    },
                    error: function(xhr, status, error){
                        var errorMessage = xhr.status + ': ' + xhr.statusText
                        // console.log('Error - ' + errorMessage);
                    }
                });
            }

        }
    });
});