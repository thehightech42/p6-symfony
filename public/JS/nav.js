/**
 * Pour faire fonctionner la class Nav, vous devais ajouter dans le balise div avec la class nav-item
 * Ajouter le nom de la page en class dans la page HTML 
 */
 class Nav{
    constructor(){
        this.links = document.getElementsByClassName('nav-item'); 
        this.path = this.pathNameFunction();
        this.research();
    }
    research(){
        for(let i = 0; i < this.links.length; i++){
            let element = this.links[i];
            for(let v = 0; v < element.classList.length; v++){
                let elementClass = element.classList[v];
                if(this.path === "" && elementClass === "/"){
                    this.addClass(element);
                }else{
                    if(elementClass === this.path){
                        this.addClass(element)
                    }
                }
            }
        }
    }
    addClass(element){
        element.classList.add('active');
        console.log('Nav validé');
    }
    pathNameFunction(){
       let split =  window.location.pathname.split('/');
       return split.pop();
    }
}

let systemNav = new Nav;