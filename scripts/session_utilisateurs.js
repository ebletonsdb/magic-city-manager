
function get_all_session_utilisateurs(search='', filter = 'jour'){
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/session_utilisateurs_crud.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        document.getElementById('session_utilisateurs-data').innerHTML = this.responseText;
    }
    xhr.send('get_all_session_utilisateurs&search='+search+ '&filter=' + filter);
}

function applyFilter() {
    let filterValue = document.getElementById('filter-search').value;
    get_all_session_utilisateurs('', filterValue);
}

window.onload = function(){
    get_all_session_utilisateurs();
}