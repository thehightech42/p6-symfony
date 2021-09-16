// Controle avant suppression
console.log('Test');
let deleteButton = document.getElementById('deleteButton');
deleteButton.addEventListener('click', (e)=>{
  if ( window.confirm('Voulez vous vraiment supprimer cette figure du site ? La suppression sera définitive et irréversible.') === true ){
    console.log("Controle de suppression"); 
  }else{
    e.preventDefault();
  }
})