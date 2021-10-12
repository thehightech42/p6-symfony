console.log('Remove Avatar'); 

let avatar = document.querySelector('[data-token]');
let path = avatar.getAttribute('href'); 
let token = avatar.getAttribute('data-token'); 
let data = JSON.stringify({'_token' : token});
// console.log(JSON.parse(data));
// console.log({'_token' : token});

// console.log(token);

avatar.addEventListener('click', (e)=>{
    e.preventDefault();
    if( confirm('Êtes-vous sur de vouloir supprimer votre photo d\'avatar ?') ){
        let req = new XMLHttpRequest; 
        req.open("DELETE", path, true);
        req.responseType = "json";

        req.addEventListener('load', function () {
            if (req.status >= 200 && req.status < 400) {
                if(this.response.success){
                    $.toast({
                        heading: 'Success',
                        text: 'Votre avatar a corretement été supprimé !',
                        showHideTransition: 'slide',
                        icon: 'success'
                    }); 
                    document.getElementById('divImgAvatar').style.display = "none";
                }else{
                    $.toast({
                    heading: 'Error',
                    text: 'Erreur de suppression',
                    showHideTransition: 'slide',
                    icon: 'error'
                    })
                }
            }else{
                console.error("Error : " + req.status + " " + req.statusText);
            }
        });

        req.send(data);

    }
})