let add_reservations_form = document.getElementById("add_reservations_form");
let edit_reservations_form = document.getElementById("edit_reservations_form");

add_reservations_form.addEventListener('submit', (e)=>{
    e.preventDefault();
    add_reservations();
});

function add_reservations(){
    let data = new FormData(); 
    data.append('add_reservations', '');
    data.append('nomclient', add_reservations_form.elements['nomclient'].value);
    data.append('package', add_reservations_form.elements['package'].value);
    data.append('date_r', add_reservations_form.elements['date_r'].value);
    data.append('heure_r', add_reservations_form.elements['heure_r'].value);
    data.append('montant', add_reservations_form.elements['montant'].value);
    data.append('versement', add_reservations_form.elements['versement'].value);
    data.append('f_reservation', add_reservations_form.elements['f_reservation'].value);
    
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/reservations_crud.php", true);

    xhr.onload = function() {
        var myModal = document.getElementById('add_reservations');
        var modal = bootstrap.Modal.getInstance(myModal);
        modal.hide();

        if(this.responseText == 1){
            alert('success', 'Nouveau Reservation ajouter!');
            add_reservations_form.reset();
            get_all_reservations();
        }else{
            alert('error', 'Erreur Server!');
        }

    }

    xhr.send(data);
}

function get_all_reservations(search=''){
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/reservations_crud.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        document.getElementById('reservations-data').innerHTML = this.responseText;
    }
    xhr.send('get_all_reservations&search='+search);
}

function edit_reservations(id){
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/reservations_crud.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        let data = JSON.parse(this.responseText);
        if (data.reservationsdata) {
            edit_reservations_form.elements['client'].value = data.reservationsdata.client;
            edit_reservations_form.elements['package'].value = data.reservationsdata.package;
            edit_reservations_form.elements['date_r'].value = data.reservationsdata.date_r;
            edit_reservations_form.elements['heure_r'].value = data.reservationsdata.heure_r;
            edit_reservations_form.elements['versement'].value = data.reservationsdata.versement;
            edit_reservations_form.elements['id'].value = data.reservationsdata.id;
        } else {
            console.error("les donnees du reservation ne sont pas disponible");
        }
    }
    xhr.send('get_reservations='+id);
}
            
edit_reservations_form.addEventListener('submit', (e)=>{
    e.preventDefault();
    save_edit_reservations();
});

function save_edit_reservations(){
    let data = new FormData();
    data.append('edit_reservations', '');
    data.append('id', edit_reservations_form.elements['id'].value);
    data.append('client', edit_reservations_form.elements['client'].value);
    data.append('package', edit_reservations_form.elements['package'].value);
    data.append('date_r', edit_reservations_form.elements['date_r'].value);
    data.append('heure_r', edit_reservations_form.elements['heure_r'].value);
    data.append('versement', edit_reservations_form.elements['versement'].value);
    
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/reservations_crud.php", true);

    xhr.onload = function() {
        var myModal = document.getElementById('edit_reservations');
        var modal = bootstrap.Modal.getInstance(myModal);
        modal.hide();

        if(this.responseText == 1){
            alert('success', 'Reservation Modifier!');
            edit_reservations_form.reset();
            get_all_reservations();
        }else{
            alert('error', 'Erreur Server!');
            console.log(this.responseText);
        }
    }

    xhr.send(data);
}


function remove_reservations(reservations_id){
    if(confirm("Voulez-vous Vraiment supprimer cette Reservation?")){
        let data = new FormData();
        data.append('reservations_id', reservations_id);
        data.append('remove_reservations', '');

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/reservations_crud.php", true);

        xhr.onload = function() {
            if(this.responseText == 1){
                alert('success', 'Reservation supprimer!');
                get_all_reservations();
            }else{
                alert('error', 'Erreur de suppression!');
            }
        }
        xhr.send(data);
    }
}

function print_reservations(id) {
    if(confirm("Voulez-vous vraiment générer le PDF pour cette réservation ?")){
        let formData = new FormData();
        formData.append("id", id);
        formData.append('print_reservations', '');

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/print_reservations_crud1.php", true);
        xhr.responseType = "blob"; 
        
        xhr.onload = function () {
            if (xhr.status === 200) {
                let blob = xhr.response;
                let url = URL.createObjectURL(blob);
                window.open(url); 
                URL.revokeObjectURL(url);

                alert('success', 'PDF généré avec succès!');
                get_all_reservations();
            } else {
                alert("error","Erreur lors de la génération du PDF !");
            }
        };

        xhr.onerror = function () {
            alert("error","Une erreur réseau s'est produite.");
        };

        xhr.send(formData);
    }
}

window.onload = function(){
    get_all_reservations();
}
