
let plats_form = document.getElementById("plats_form");
let edit_plats_form = document.getElementById("edit_plats_form");
let boissons_form = document.getElementById("boissons_form");
let edit_boissons_form = document.getElementById("edit_boissons_form");

// Boissons
plats_form.addEventListener('submit', (e)=>{
    e.preventDefault();
    add_plats();
});

function add_plats(){
    let data = new FormData();
    data.append('nom', plats_form.elements['nom'].value);
    data.append('prix', plats_form.elements['prix'].value);
    data.append('add_plats', '');

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/plats_boissons_crud.php", true);

    xhr.onload = function() {
        var myModal = document.getElementById('plats');
        var modal = bootstrap.Modal.getInstance(myModal);
        modal.hide();

        if (this.responseText == 'nom_already') {
            alert('error', 'Ce plat existe déjà!');
        } else {
            alert('success', 'Nouveau Plats Ajouter!');
            plats_form.reset();
            get_plats();
        }
    }
    xhr.send(data);
}

function get_all_plats(search=''){
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/plats_boissons_crud.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        document.getElementById('plats-data').innerHTML = this.responseText;
    }

    xhr.send('get_all_plats&search='+search);
}

function edit_plats(id){

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/plats_boissons_crud.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        let data = JSON.parse(this.responseText);
        if (data.platsdata) {
            edit_plats_form.elements['nom'].value = data.platsdata.nom;
            edit_plats_form.elements['prix'].value = data.platsdata.prix;
            edit_plats_form.elements['id'].value = data.platsdata.id;
        } else {
            console.error("les donnees du plat ne sont pas disponible");
        }
    }
    xhr.send('get_plats='+id);
}

edit_plats_form.addEventListener('submit', (e)=>{
    e.preventDefault();
    save_edit_plats();
});

function save_edit_plats(){ 
    let data = new FormData();
    data.append('edit_plats', '');
    data.append('id', edit_plats_form.elements['id'].value);
    data.append('nom', edit_plats_form.elements['nom'].value);
    data.append('prix', edit_plats_form.elements['prix'].value);
    
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/plats_boissons_crud.php", true);

    xhr.onload = function() {
        var myModal = document.getElementById('edit_plats');
        var modal = bootstrap.Modal.getInstance(myModal);
        modal.hide();

        if(this.responseText == 1){
            alert('success', 'plat Modifier!');
            edit_plats_form.reset();
            get_all_plats();
        }else{
            alert('error', 'Erreur Server!');
            console.log(this.responseText);
        }

    }

    xhr.send(data);
}

function remove_plats(val){
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/plats_boissons_crud.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if(this.responseText == 'package_added'){
            alert('error', 'Le plat est dans une package!');
        }else if(this.responseText == 1){
            alert('success', 'Plat supprimer!');
            get_all_utilisateurs();
        }else{
            alert('error', 'Erreur de suppression!');
        }
    }

    xhr.send('remove_plats= ' + val);
    
}

// Boissons
boissons_form.addEventListener('submit', (e)=>{
    e.preventDefault();
    add_boissons();
});

function add_boissons(){
    let data = new FormData();
    data.append('nom', boissons_form.elements['nom'].value);
    data.append('prix', boissons_form.elements['prix'].value);
    data.append('add_boissons', '');

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/plats_boissons_crud.php", true);

    xhr.onload = function() {
        var myModal = document.getElementById('boissons');
        var modal = bootstrap.Modal.getInstance(myModal);
        modal.hide();

        if (this.responseText == 'nom_already') {
            alert('error', 'Cette boisson existe déjà!');
        } else {
            alert('success', 'Nouveau Boisson Ajouter!');
            boissons_form.reset();
            get_boissons();
        }
    }

    xhr.send(data);
}

function get_all_boissons(search=''){
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/plats_boissons_crud.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        document.getElementById('boissons-data').innerHTML = this.responseText;
    }

    xhr.send('get_all_boissons&search='+search);
}

function edit_boissons(id){

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/plats_boissons_crud.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        let data = JSON.parse(this.responseText);
        if (data.boissonsdata) {
            edit_boissons_form.elements['nom'].value = data.boissonsdata.nom;
            edit_boissons_form.elements['prix'].value = data.boissonsdata.prix;
            edit_boissons_form.elements['id'].value = data.boissonsdata.id;
        } else {
            console.error("les donnees du boisson ne sont pas disponible");
        }
    }
    xhr.send('get_boissons='+id);
}

edit_boissons_form.addEventListener('submit', (e)=>{
    e.preventDefault();
    save_edit_boissons();
});

function save_edit_boissons(){ 
    let data = new FormData();
    data.append('edit_boissons', '');
    data.append('id', edit_boissons_form.elements['id'].value);
    data.append('nom', edit_boissons_form.elements['nom'].value);
    data.append('prix', edit_boissons_form.elements['prix'].value);
    
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/plats_boissons_crud.php", true);

    xhr.onload = function() {
        var myModal = document.getElementById('edit_boissons');
        var modal = bootstrap.Modal.getInstance(myModal);
        modal.hide();

        if(this.responseText == 1){
            alert('success', 'plat Modifier!');
            edit_boissons_form.reset();
            get_all_plats();
        }else{
            alert('error', 'Erreur Server!');
            console.log(this.responseText);
        }

    }

    xhr.send(data);
}

function remove_boissons(val){
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/plats_boissons_crud.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if(this.responseText == 'package_added'){
            alert('error', 'Cette boisson est dans une package!');
        }else if(this.responseText == 1){
            alert('success', 'Boisson supprimer!');
            get_all_utilisateurs();
        }else{
            alert('error', 'Erreur de suppression!');
        }
    }

    xhr.send('remove_boissons= ' + val);
}

window.onload = function(){
    get_all_plats();
    get_all_boissons();
}
