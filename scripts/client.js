let add_clients_form = document.getElementById("add_clients_form");
let edit_clients_form = document.getElementById("edit_clients_form");

add_clients_form.addEventListener('submit', (e)=>{
    e.preventDefault();
    add_clients();
}); 

function add_clients(){
    let data = new FormData();
    data.append('add_clients', '');
    data.append('type', add_clients_form.elements['type'].value);
    data.append('nom', add_clients_form.elements['nom'].value);
    data.append('addresse', add_clients_form.elements['addresse'].value);
    data.append('phone', add_clients_form.elements['phone'].value);
    data.append('dob', add_clients_form.elements['dob'].value);
    
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/clients_crud.php", true);

    xhr.onload = function() {
        var myModal = document.getElementById('add_clients');
        var modal = bootstrap.Modal.getInstance(myModal);
        modal.hide();

        if(this.responseText == 'nom_already'){
            alert('error', 'Le nom du client existe déjà dans la base de données.')
        }else if (this.responseText == 'phone_already') {
            alert('error', 'Le numéro de téléphone du client est déjà enregistré !')
        }else if(this.responseText == 1){
            alert('success', 'Nouveau Client ajouter!');
            add_clients_form.reset();
            get_all_clients();
        }else{
            alert('error', 'Erreur Server!');
        }
        
    }

    xhr.send(data);
}

function get_all_clients(search=''){
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/clients_crud.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        document.getElementById('clients-data').innerHTML = this.responseText;
    }
    xhr.send('get_all_clients&search='+search);
}

function edit_clients(id){

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/clients_crud.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        let data = JSON.parse(this.responseText);
        if (data.clientsdata) {
            edit_clients_form.elements['nom'].value = data.clientsdata.nom;
            edit_clients_form.elements['type'].value = data.clientsdata.type;
            edit_clients_form.elements['addresse'].value = data.clientsdata.addresse;
            edit_clients_form.elements['phone'].value = data.clientsdata.phone;
            edit_clients_form.elements['dob'].value = data.clientsdata.dob;
            edit_clients_form.elements['id'].value = data.clientsdata.id;
        } else {
            console.error("les donnees du Client ne sont pas disponible");
        }
    }
    xhr.send('get_clients='+id);
}
            
edit_clients_form.addEventListener('submit', (e)=>{
    e.preventDefault();
    save_edit_clients();
});

function save_edit_clients(){ 
    let data = new FormData();
    data.append('edit_clients', '');
    data.append('id', edit_clients_form.elements['id'].value);
    data.append('nom', edit_clients_form.elements['nom'].value);
    data.append('type', edit_clients_form.elements['type'].value);
    data.append('addresse', edit_clients_form.elements['addresse'].value);
    data.append('phone', edit_clients_form.elements['phone'].value);
    data.append('dob', edit_clients_form.elements['dob'].value);
    
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/clients_crud.php", true);

    xhr.onload = function() {
        var myModal = document.getElementById('edit_clients');
        var modal = bootstrap.Modal.getInstance(myModal);
        modal.hide();

        if(this.responseText == 1){
            alert('success', 'Client Modifier!');
            edit_clients_form.reset();
            get_all_clients();
        }else{
            alert('error', 'Erreur Server!');
            console.log(this.responseText);
        }

    }

    xhr.send(data);
}

function remove_client(client_id){
    if(confirm("Voulez-vous Vraiment supprimer cet Client?")){
        let data = new FormData();
        data.append('client_id', client_id);
        data.append('remove_client', '');

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/clients_crud.php", true);

        xhr.onload = function() {
            if(this.responseText == 1){
                alert('success', 'Client supprimer!');
                get_clients();
            }else{
                alert('error', 'Erreur de suppression!');
            }
        }
        xhr.send(data);
    }
}

window.onload = function(){
    get_all_clients();
}