let buttonAddGroupe = document.getElementById('btnAddGroupe'); 
let title = document.getElementById('titleGroupe');
let description = document.getElementById('descriptionGroupe'); 
let selectGroupe = document.getElementById('form_groupe');
let modal = document.getElementById('modalAddGroupeFigure');

function ajax(title, description){
    console.log(title +" " + description);

    let req = new XMLHttpRequest(); 

    req.open('POST', '/figure/ajax/addGroupeFigure', true)

    req.addEventListener('load', function () {

        if (req.status >= 200 && req.status < 400) {
            console.log("Bingo !");
            console.log(JSON.parse(this.responseText));
            let data = JSON.parse(this.responseText);
            let option = document.createElement('option'); 
            option.setAttribute('selected', 'selected'); 
            option.value = data.id;
            option.textContent = title; 
            selectGroupe.appendChild(option);
        } else {
            console.error("Error : " + req.status + " " + req.statusText);
        }
    
    });
    

    let data = new FormData(); 
    data.append('title', title);
    data.append('description', description); 

    req.send(data);
}

buttonAddGroupe.addEventListener('click', (e)=>{
    e.preventDefault(); 
    ajax(title.value, description.value); 
    title.value = ""; 
    description.value = "";
    modal.style.display = "none"; // Display none modal
    document.getElementsByClassName('modal-backdrop')[0].remove(); //Remove div with opacity
    document.getElementsByTagName('body')[0].classList.remove("modal-open"); // Remove classLite 
})
