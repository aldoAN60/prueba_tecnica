function toggle_list_and_chart(toggle_name){
    let toggleSwitch = document.getElementById("toggleSwitch");
    const toggleIsCheck = localStorage.getItem(toggle_name) === 'true'; // Convertir a booleano
    toggleSwitch.checked = toggleIsCheck;

    const lista = document.getElementById("lista");
    const grafica = document.getElementById("grafica");

    // Escuchar el cambio del switch
    toggleSwitch.addEventListener("change", function() {
        localStorage.setItem(toggle_name, toggleSwitch.checked); // Guardar como cadena de texto
        if (toggleSwitch.checked) {
            grafica.classList.remove('hidden');
            lista.classList.add('hidden');
        } else {
            grafica.classList.add('hidden');
            lista.classList.remove('hidden');
        }
    });

    // Establecer el estado inicial al cargar la página
    if (toggleSwitch.checked) {
        lista.classList.add('hidden');
        grafica.classList.remove('hidden');
    } else {
        lista.classList.remove('hidden');
        grafica.classList.add('hidden');
    }
}
window.toggle_list_and_chart = toggle_list_and_chart;