let add_utilisateurs_form = document.getElementById("add_utilisateurs_form");
let edit_utilisateurs_form = document.getElementById("edit_utilisateurs_form");

add_utilisateurs_form.addEventListener('submit', (e)=>{
    e.preventDefault();
    add_utilisateurs(); 
});

function add_utilisateurs(){
    let data = new FormData();
    data.append('add_utilisateurs', '');
    data.append('type', add_utilisateurs_form.elements['type'].value);
    data.append('nom', add_utilisateurs_form.elements['nom'].value);
    data.append('email', add_utilisateurs_form.elements['email'].value);
    data.append('addresse', add_utilisateurs_form.elements['addresse'].value);
    data.append('phone', add_utilisateurs_form.elements['phone'].value);
    data.append('mdp', add_utilisateurs_form.elements['mdp'].value);
    data.append('cmdp', add_utilisateurs_form.elements['cmdp'].value);
    
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/utilisateurs_crud.php", true);

    xhr.onload = function() {
        var myModal = document.getElementById('add_utilisateurs');
        var modal = bootstrap.Modal.getInstance(myModal);
        modal.hide();

        if (this.responseText == 'pass_missmatch') {
            alert('error', 'Les deux mot de passe sont incompatibles!');
        } else if (this.responseText == 'email_already') {
            alert('error', 'L’Email est déjà utilisé !');
        } else if (this.responseText == 'phone_already') {
            alert('error', 'Le numéro de téléphone est déjà utilisé !');
        }else if (this.responseText == 'mail_failed') {
            alert('error', 'Impossible d’envoyer un Email de confirmation ! Erreur Serveur!');
        } else if (this.responseText == 'ins_failed') {
            alert('error', 'L’inscription a échoué! Erreur Server!');
        } else {
            alert('success', 'Inscription réussie.');
            add_utilisateurs_form.reset();
            get_all_utilisateurs();
        }
            

    }

    xhr.send(data);
}

function get_all_utilisateurs(search=''){
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/utilisateurs_crud.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        document.getElementById('utilisateurs-data').innerHTML = this.responseText;
    }
    xhr.send('get_all_utilisateurs&search='+search);
}

function remove_utilisateurs(utilisateurs_id){
    if(confirm("Voulez-vous Vraiment supprimer cet utilisateurs?")){
        let data = new FormData();
        data.append('utilisateurs_id', utilisateurs_id);
        data.append('remove_utilisateurs', '');

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/utilisateurs_crud.php", true);

        xhr.onload = function() {
            if(this.responseText == 1){
                alert('success', 'utilisateurs supprimer!');
                get_all_utilisateurs();
            }else{
                alert('error', 'Erreur de suppression!');
            }
        }
        xhr.send(data);
    }
}

function toggle_status(id, val){
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/utilisateurs_crud.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if(this.responseText == 1){
            alert('success', 'Le statut de l\'utilisateur a été changé!');
            get_all_utilisateurs();
        }else{
            alert('error', 'Erreur Server');
        }
    }

    xhr.send('toggle_status='+id+'&value='+val);
}

function edit_utilisateurs(id){

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/utilisateurs_crud.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        let data = JSON.parse(this.responseText);
        if (data.utilisateursdata) {
            edit_utilisateurs_form.elements['type'].value = data.utilisateursdata.type;
            document.querySelector("#role").value = data.utilisateursdata.type;
            edit_utilisateurs_form.elements['nom'].value = data.utilisateursdata.nom;
            edit_utilisateurs_form.elements['email'].value = data.utilisateursdata.email;
            edit_utilisateurs_form.elements['addresse'].value = data.utilisateursdata.addresse;
            edit_utilisateurs_form.elements['phone'].value = data.utilisateursdata.phone;
            // edit_utilisateurs_form.elements['mdp'].value = data.utilisateursdata.mdp;
            // edit_utilisateurs_form.elements['cmdp'].value = data.utilisateursdata.mdp;
            edit_utilisateurs_form.elements['id'].value = data.utilisateursdata.id;
        } else {
            console.error("les donnees d'utilisateur ne sont pas disponible");
        }
    }
    xhr.send('get_utilisateurs='+id);
}
            
edit_utilisateurs_form.addEventListener('submit', (e)=>{
    e.preventDefault();
    save_edit_utilisateurs();
});

function save_edit_utilisateurs(){
    let data = new FormData();
    data.append('edit_utilisateurs', '');
    data.append('id', edit_utilisateurs_form.elements['id'].value);
    data.append('type', edit_utilisateurs_form.elements['type'].value);
    data.append('nom', edit_utilisateurs_form.elements['nom'].value);
    data.append('email', edit_utilisateurs_form.elements['email'].value);
    data.append('addresse', edit_utilisateurs_form.elements['addresse'].value);
    data.append('phone', edit_utilisateurs_form.elements['phone'].value);
    data.append('mdp', edit_utilisateurs_form.elements['mdp'].value);
    data.append('cmdp', edit_utilisateurs_form.elements['cmdp'].value);
    
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/utilisateurs_crud.php", true);

    xhr.onload = function() {
        var myModal = document.getElementById('edit_utilisateurs');
        var modal = bootstrap.Modal.getInstance(myModal);
        modal.hide();

        if(this.responseText == "mdp_dif"){
            alert('error', 'Les deux mots de passe sont différents!');
        } else if(this.responseText == "email_existe"){
            alert('error', 'Email déjà utilisé dans une autre compte!');
        }else if(this.responseText == "phone_existe"){
            alert('error', 'Téléphone déjà utilisé dans une autre compte!');
        }else if(this.responseText == 1){
            alert('success', 'Donner Utilisateur Modifier avec succes!');
            edit_utilisateurs_form.reset();
            get_all_utilisateurs();
        }else{
            // alert('error', 'Erreur Server!');
            console.log(this.responseText);
        }

    }

    xhr.send(data);
}

window.onload = function(){
    get_all_utilisateurs();
}