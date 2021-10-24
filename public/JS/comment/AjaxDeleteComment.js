let comments = document.getElementsByClassName('deleteComment');
console.log(comments);
for(let comment of comments){
    comment.addEventListener('click', (e)=>{
        e.preventDefault();
        if(confirm('Etes-vous sur de vouloir supprimer ce commentaire ? ')){
            let data = JSON.stringify({'_token':comment.getAttribute('data-token')}); 
            jQuery.ajax({
                type: "DELETE",
                async: true, 
                url : comment.getAttribute("href"),
                data: data,
                success : function(jsonData){
                    if(jsonData['success']){
                        $.toast({
                            heading: 'Success',
                            text: "Le commentaire a été correctement supprimé",
                            showHideTransition: 'slide',
                            icon: 'success',
                            loader: true
                        });
                        document.getElementById("comment-"+ comment.getAttribute("data-id")).remove();
                    }
                },
                error: function(xhr, status, error){
                    var errorMessage = xhr.status + ': ' + xhr.statusText
                }
            });
        }
    })
}