document.getElementById('deleteButton').addEventListener('click', (e)=>{
    if(!confirm('Etes vous sur de vouloir supprimer cet élement ? ATTENTION, cette action est définitive !')){
        e.preventDefault();
    }
})