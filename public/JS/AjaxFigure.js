console.log('figure.js');

class AjaxFigure{
    constructor(url, btn){ 
        this.url = url;
        this.button = document.getElementById(btn); 
        // this.figure = window.location.href.split('/').pop();
        this.page = 0;
        this.usingButton();
        this.requette();
    }

    requette(){
        // console.log('requette ' + this.page)
        jQuery.ajax({
            type: "POST",
            async: true, 
            url : this.url,
            data: {
                'page' : this.page 
            },
            // cache:false, 
            success : function(jsonData){
                this.usingData(jsonData);
                console.log(jsonData);
            }.bind(this),
            error: function(xhr, status, error){
                var errorMessage = xhr.status + ': ' + xhr.statusText
                // console.log('Error - ' + errorMessage);
            }
        });
    }

    usingData(jsonData){
        let blockComments = document.getElementById('figures');
        let stringAllFigures = "";
        jsonData.figures.forEach(figure => {
            // console.log(figure);
                let htmlString = "";
                htmlString += "<div class='col-lg-3 elementsFigure'><a href='"+ figure.path +"'>";
                htmlString +=    "<div class='card text-white bg-dark mb-3'>";
                htmlString +=        "<div class='card-header'><h5 class='card-title'>"+ figure.title +"</h5></div>";
                htmlString +=        "<div class='card-body'>";
                
                if( figure.mainVisuel !== 'undefined '){
                    htmlString +=        "<img class='mainImgIndex' src='"+ figure.mainVisuel +"' alt=''>";
                }
                // htmlString +=            "<h5 class='card-title'><a href=''></a></h5>";
                htmlString +=            "<p class='card-text'>"+ figure.shortDescription +"</p>";
                htmlString +=            "<p>Publié le x dans le groupe "+ figure.titleGroupe +"</p>";
                htmlString +=        "</div>";
                htmlString +=    "</div>";
                htmlString += "</a></div>";

                stringAllFigures += htmlString;

        });

        // blockComments.innerHTML = stringAllFigures + "<button id='seeMoreFigure' class='btn btn-primary'>Voir plus</button>";
        blockComments.innerHTML += stringAllFigures;

        if(jsonData.endData === true){
            this.button.style.display = "none";
        }else{
            this.page ++;
            console.log(this.page);
            console.log(this.url);        
        }
    }
    
    usingButton(){
        this.button.addEventListener('click', this.requette.bind(this));
        console.log('test usingButton');
        console.log(this.page);
    }
}