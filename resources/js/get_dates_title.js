function get_title_with_dates(){
    // Obtener los parámetros de la URL
    const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.toString()){
            // Obtener los valores de los parámetros
            const startDate = urlParams.get('start_date');
            const endDate = urlParams.get('end_date'); 

            let titulo = document.querySelector('#title');
            titulo.textContent += ' del ' + format_date(startDate) +' al '+ format_date(endDate);
        }
}
function format_date(fecha) {
    const meses = [
        "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
    ];

    // Convierte el string de fecha en un objeto Date
    const fechaObj = new Date(fecha);

    // Obtén el día, el mes (en nombre) y el año
    const dia = fechaObj.getDate();
    const mes = meses[fechaObj.getMonth()];  // El índice del mes (0-11)
    const año = fechaObj.getFullYear();

    // Devuelve la fecha formateada
    return `${dia} de ${mes} del ${año}`;
}

window.get_title_with_dates = get_title_with_dates;