let add_rapports_form = document.getElementById("add_rapports_form");

add_rapports_form.addEventListener('submit', (e)=>{
    e.preventDefault();
    add_rapports();
}); 

function add_rapports(){
    let data = new FormData();
    data.append('add_rapports', '');
    data.append('date_debut', add_rapports_form.elements['date_debut'].value);
    data.append('date_fin', add_rapports_form.elements['date_fin'].value);
    
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/rapports_crud.php", true);

    xhr.onload = function() {
        var myModal = document.getElementById('add_rapports');
        var modal = bootstrap.Modal.getInstance(myModal);
        modal.hide();

        if(this.responseText == 'rapport_already'){
            alert('error', 'Un rapport existe déjà pour la période sélectionnée.')
        }else if(this.responseText == 1){
            alert('success', 'Nouveau rapport ajouter!');
            add_rapports_form.reset();
            get_all_rapports();
        }else{
            alert('error', 'Erreur Server!');
        }
        
    }

    xhr.send(data);
}

function get_all_rapports(search=''){
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/rapports_crud.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        document.getElementById('rapports-data').innerHTML = this.responseText;
    }
    xhr.send('get_all_rapports&search='+search);
}

function remove_rapports(rapports_id){
    if(confirm("Voulez-vous Vraiment supprimer cet rapports?")){
        let data = new FormData();
        data.append('rapports_id', rapports_id);
        data.append('remove_rapports', '');

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/rapports_crud.php", true);

        xhr.onload = function() {
            if(this.responseText == 1){
                alert('success', 'rapports supprimer!');
                get_all_rapports();
            }else{
                alert('error', 'Erreur de suppression!');
            }
        }
        xhr.send(data);
    }
}

function print_rapports(id) {
    if(confirm("Voulez-vous vraiment générer le PDF pour ce rapport ?")){
        let formData = new FormData();
        formData.append("id", id);
        formData.append('print_rapports', '');

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/rapports_crud.php", true);
        xhr.responseType = "blob"; 
        
        xhr.onload = function () {
            if (xhr.status === 200) {
                let contentDisposition = xhr.getResponseHeader('Content-Disposition');
                let filename = "rapport_";
                let rapportName = contentDisposition.match(/filename="([^"]+)"/);
                if (rapportName && rapportName[1]) {
                    filename += rapportName[1];
                }
                filename += ".pdf";

                let blob = xhr.response;
                let url = window.URL.createObjectURL(blob);
                let a = document.createElement("a");
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                alert('success', 'PDF généré avec succès!');
                get_all_rapports();
            } else {
                alert("Erreur lors de la génération du PDF !");
            }
        };
        xhr.send(formData);
    }
}

window.onload = function(){
    get_all_rapports();
}