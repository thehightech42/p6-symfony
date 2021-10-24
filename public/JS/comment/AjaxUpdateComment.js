let buttonSave = document.getElementById('saveUpdateComment');

// Boucle pour l'ouverture de la modal
for(let comment of document.getElementsByClassName('updateComment')){
    comment.addEventListener('click', (e)=>{
        e.preventDefault();
        document.getElementById('textareaModal').value = document.getElementById('commentValue-'+comment.getAttribute('data-id')).innerText; 
        buttonSave.setAttribute('href', comment.getAttribute('href'));
        buttonSave.setAttribute('data-token', comment.getAttribute('data-token'));
        buttonSave.setAttribute('data-id', comment.getAttribute('data-id'));
    })
}

// Fonction d'envoi des informations
buttonSave.addEventListener('click', (e)=>{
    e.preventDefault(); 
    jQuery.ajax({
        type: "POST",
        async: true, 
        url : buttonSave.getAttribute("href"),
        data: JSON.stringify({'_token':buttonSave.getAttribute('data-token'), 'value' : document.getElementById('textareaModal').value}),
        success : function(jsonData){
            if(jsonData['success']){
                $.toast({
                    heading: 'Success',
                    text: "Le commentaire a été correctement modifié",
                    showHideTransition: 'slide',
                    icon: 'success',
                    loader: true
                });
                document.getElementById("commentValue-"+ buttonSave.getAttribute("data-id")).innerText = document.getElementById('textareaModal').value;
                document.getElementById('modalUpdateComment').style.display = "none"; // Display none modal
                document.getElementsByClassName('modal-backdrop')[0].remove(); //Remove div with opacity
                document.getElementsByTagName('body')[0].classList.remove("modal-open"); // Remove classLite 
            }
        },
        error: function(xhr, status, error){
            var errorMessage = xhr.status + ': ' + xhr.statusText
        }
    });
});