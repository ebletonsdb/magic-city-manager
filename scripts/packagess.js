
let add_packages_form = document.getElementById('add_packages_form');
let edit_packages_form = document.getElementById('edit_packages_form');
let add_qte_form = document.getElementById('add_qte_form');

add_packages_form.addEventListener('submit', (e)=>{
    e.preventDefault();
    add_packages();
}); 


function add_packages(){ 
    let data = new FormData();
    data.append('add_packages', '');
    data.append('nom', add_packages_form.elements['nom'].value);
    
    let prix_plats = [];
    let id_plats = [];

    let qteplatElements = add_packages_form.elements['id_plats'];
    if (qteplatElements) {
        qteplatElements.forEach(el => {
            if (el.checked) {
                id_plats.push(el.value);
                prix_plats.push(add_packages_form.elements['prix_plats'][Array.from(qteplatElements).indexOf(el)].value);
            }
        });
    }

    let id_boissons = [];
    let prix_boissons = [];
    
    let boissonElements = add_packages_form.elements['id_boissons'];
    if (boissonElements) {
        boissonElements.forEach(el => {
            if (el.checked) {
                id_boissons.push(el.value);
                prix_boissons.push(add_packages_form.elements['prix_boissons'][Array.from(boissonElements).indexOf(el)].value);
            }
        });
    }

    data.append('id_plats', JSON.stringify(id_plats));
    data.append('prix_plats', JSON.stringify(prix_plats));
    data.append('id_boissons', JSON.stringify(id_boissons));
    data.append('prix_boissons', JSON.stringify(prix_boissons));

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/packages_crud.php", true);

    xhr.onload = function() {
        var myModal = document.getElementById('add_package');
        var modal = bootstrap.Modal.getInstance(myModal);
        modal.hide();

        if(this.responseText == 1){
            alert('success', 'Nouveau package Ajouter!');
            add_packages_form.reset();
            refreshpage();
            get_all_packages();
        }else{
            alert('error', 'Erreur Server!');
        }
    }
    xhr.send(data);
}

function add_qte_details_plats(id) {
    window.location.href = window.location.pathname + "?id=" + id + "#add_qte";
}
document.addEventListener("DOMContentLoaded", function () {
    var params = new URLSearchParams(window.location.search);
    if (params.has('id')) {
        var modal = new bootstrap.Modal(document.getElementById('add_qte'));
        modal.show();
    }
});

function refreshpage() {
    window.location.href = window.location.pathname
}

add_qte_form.addEventListener('submit', (e)=>{
    e.preventDefault();
    add_qte();
});

function add_qte(){
    let data = new FormData();
    data.append('add_qte', '');
    data.append('id_package', add_qte_form.elements['id_package'].value);
    data.append('qte_pers', add_qte_form.elements['qte_pers'].value);

    let qtePlats = [];
    let idPlats = [];
    let prixPlats = [];
    document.querySelectorAll('input[name="qte_plats[]"]').forEach((input, index) => {
        qtePlats.push(input.value);
        idPlats.push(document.querySelectorAll('input[name="id_plats[]"]')[index].value);
        prixPlats.push(document.querySelectorAll('input[name="prix_plats[]"]')[index].value);
    });

    let qteBoissons = [];
    let idBoissons = [];
    let prixBoissons = [];
    document.querySelectorAll('input[name="qte_boissons[]"]').forEach((input, index) => {
        qteBoissons.push(input.value);
        idBoissons.push(document.querySelectorAll('input[name="id_boissons[]"]')[index].value);
        prixBoissons.push(document.querySelectorAll('input[name="prix_boissons[]"]')[index].value);
    });

    data.append('qte_plats', JSON.stringify(qtePlats));
    data.append('id_plats', JSON.stringify(idPlats));
    data.append('prix_plats', JSON.stringify(prixPlats));
    data.append('qte_boissons', JSON.stringify(qteBoissons));
    data.append('id_boissons', JSON.stringify(idBoissons));
    data.append('prix_boissons', JSON.stringify(prixBoissons));

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/packages_crud.php", true);

    xhr.onload = function() {
        var myModal = document.getElementById('add_qte');
        var modal = bootstrap.Modal.getInstance(myModal);
        modal.hide();

        if(this.responseText == 1){
            add_qte_form.reset();
            get_all_packages();
            alert('success','Les quantités ont été ajoutées avec succès !"');
        }else{
            alert('error', 'Erreur Server!');
            console.log(this.responseText);
        }
    }
    xhr.send(data);
}

function get_all_packages(search=''){
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/packages_crud.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        document.getElementById('packages-data').innerHTML = this.responseText;
    }
    xhr.send('get_all_packages&search='+search);
} 

function edit_package(id) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/packages_crud.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
        let data = JSON.parse(this.responseText);
        if (data.packagedata) {
            document.querySelector("#edit_packages_form input[name='id']").value = id;
            
            document.querySelectorAll("#edit_packages_form input[name='id_plats']").forEach(el => el.checked = false);
            document.querySelectorAll("#edit_packages_form input[name='id_boissons']").forEach(el => el.checked = false);
            
            data.plats.forEach(id_plat => {
                let checkbox = document.querySelector(`#edit_packages_form input[name='id_plats'][value='${id_plat}']`);
                if (checkbox) checkbox.checked = true;
            });
            
            data.boissons.forEach(id_boisson => {
                let checkbox = document.querySelector(`#edit_packages_form input[name='id_boissons'][value='${id_boisson}']`);
                if (checkbox) checkbox.checked = true;
            });
        } else {
            console.error("Les données du package ne sont pas disponibles");
        }
    }
    xhr.send('get_package=' + id);
}

edit_packages_form.addEventListener('submit', (e) => {
    e.preventDefault();
    save_edit_packages();
});

function save_edit_packages() {
    let data = new FormData();
    data.append('edit_packages', '');
    data.append('id', edit_packages_form.elements['id'].value);
    
    let plats = [];
    document.querySelectorAll("#edit_packages_form input[name='id_plats']:checked").forEach(el => plats.push(el.value));
    
    let boissons = [];
    document.querySelectorAll("#edit_packages_form input[name='id_boissons']:checked").forEach(el => boissons.push(el.value));
    
    data.append('plats', JSON.stringify(plats));
    data.append('boissons', JSON.stringify(boissons));

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/packages_crud.php", true);

    xhr.onload = function() {
        var myModal = document.getElementById('edit_package');
        var modal = bootstrap.Modal.getInstance(myModal);
        modal.hide();

        if (this.responseText == 1) {
            alert('success', 'Package modifié !');
            edit_packages_form.reset();
            get_all_packages();
        } else {
            alert('error', 'Erreur serveur !');
            console.log(this.responseText);
        }
    }

    xhr.send(data);
}


function remove_package(id){
    if(confirm("Voulez-vous Vraiment supprimer ce package ?")){
        let data = new FormData();
        data.append('id', id);
        data.append('remove_package', '');

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/packages_crud.php", true);

        xhr.onload = function() {
            if(this.responseText == 1){
                alert('success', 'Package supprimer!');
                get_all_packages();
            }else{
                alert('error', 'Erreur de suppression!');
            }
        }
        xhr.send(data);
    }
}

window.onload = function(){
    get_all_packages();
}