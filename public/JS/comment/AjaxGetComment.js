class AjaxComment{
    constructor(url, btn){ 
        this.url = url;
        this.button = document.getElementById(btn); 
        this.figure = window.location.href.split('/').pop();
        this.blockElement = 0;
        this.usingButton();
        this.requette();
    }

    requette(){
        $.ajax({
            type: "POST",
            async:true, 
            url : this.url,
            data: {
                'figure' : this.figure, 
                'blockElement' :this.blockElement
            },
            cache:false, 
            success : function(jsonData){
                console.log(jsonData);
                this.usingData(jsonData);
                
            }.bind(this),
            error: function(xhr, status, error){
                var errorMessage = xhr.status + ': ' + xhr.statusText
                console.log('Error - ' + errorMessage);
            }
        });
    }

    usingData(jsonData){
        // console.log(jsonData);
        let blockComments = document.getElementById('comments');

        if(jsonData.endData === true){
            this.button.style.display = "none";
        }else{
            this.blockElement += 1;
        }
        // console.log(typeof jsonData.comments == undefined);
        if(typeof jsonData.comments != "undefined"){
            jsonData.comments.forEach(comment => {
                let datePerso = new Date(comment.created_at.date.replace(/\s/, 'T'));
                let divComment = document.createElement('div'); 
    
                let pNameValue = document.createElement('p');
                pNameValue.textContent = comment.user + " - " + comment.value;
                let br = document.createElement('br');
                let pDate = document.createElement('p')
                let optionsDate = { year: 'numeric', month: 'long', day: 'numeric' };
                let dateFormat = datePerso.toLocaleString("fr-FR", optionsDate);
                // pDate.textContent = "Publié le : " + datePerso.toUTCString();
                pDate.textContent = "Publié le " + dateFormat; 
    
                divComment.appendChild(pNameValue); 
                divComment.appendChild(pDate); 
                blockComments.appendChild(divComment);
                blockComments.appendChild(br);
            });
        }else if(this.blockElement === 0){
            let divComment = document.createElement('div'); 
            let paragrapheInfo = document.createElement('p');
            paragrapheInfo.textContent = "Aucun message pour le moment. Soyer le premier à en écrire un !"

            divComment.appendChild(paragrapheInfo);
            blockComments.appendChild(divComment);
        }
        

        
    }
    
    usingButton(){
        this.button.addEventListener('click', this.requette.bind(this));
    }
}