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
        
        jsonData.comments.forEach(comment => {
            let datePerso = new Date(comment.created_at.date);

            let divComment = document.createElement('div'); 

            let pNameValue = document.createElement('p');
            pNameValue.textContent = comment.user + " - " + comment.value;
            let br = document.createElement('br');
            let pDate = document.createElement('p')
            pDate.textContent = "Publié le : " + datePerso.toUTCString(); 

            divComment.appendChild(pNameValue); 
            divComment.appendChild(pDate); 
            blockComments.appendChild(divComment);
            blockComments.appendChild(br);
        });

        if(jsonData.endData === true){
            this.button.style.display = "none";
        }else{
            this.blockElement += 1;
        }
    }
    
    usingButton(){
        this.button.addEventListener('click', this.requette.bind(this));
    }
}