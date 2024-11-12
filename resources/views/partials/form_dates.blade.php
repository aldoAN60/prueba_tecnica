<h2 class="text-xl font-semibold">Filtrar por fecha</h2>
<form action="{{ $action }}" method="GET" class="mb-6" id="dateForm">
    <div class="flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
            <label for="start_date" class="block text-sm font-medium text-gray-700">Inicio</label>
            <input type="text" id="start_date" name="start_date" placeholder="Selecciona una fecha"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                   onfocus="(this.type='date'); this.valueAsDate = new Date('2022-01-01')" onblur="(this.type='text')">
        </div>
        <div class="flex-1">
            <label for="end_date" class="block text-sm font-medium text-gray-700">Fin</label>
            <input type="text" id="end_date" name="end_date" placeholder="Selecciona una fecha"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                   onfocus="(this.type='date'); this.valueAsDate = new Date('2022-01-01')" onblur="(this.type='text')">
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" id="searchButton" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:bg-gray-400 disabled:cursor-not-allowed" disabled>
                Buscar
            </button>
            <a href="#" id="clearFilters" class="ml-2 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Limpiar
            </a>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const searchButton = document.getElementById('searchButton');
    const form = document.getElementById('dateForm');

    // Verifica si se deben habilitar o deshabilitar el botón de búsqueda
    function checkButtonState() {
        if (!startDateInput.value || !endDateInput.value) {
            searchButton.disabled = true; // Deshabilitar si faltan fechas
        } else {
            searchButton.disabled = false; // Habilitar si ambas fechas están presentes
        }
    }

    startDateInput.addEventListener('input', checkButtonState);
    endDateInput.addEventListener('input', checkButtonState);

    form.addEventListener('submit', function(event) {
        const startDateValue = new Date(startDateInput.value);
        const endDateValue = new Date(endDateInput.value);

        if (startDateValue > endDateValue) {
            event.preventDefault();
            alert('La fecha de inicio no puede ser mayor que la fecha de fin.');
        }
    });

    // Limpiar los filtros y deshabilitar el botón de búsqueda
    document.getElementById('clearFilters').addEventListener('click', function(event) {
        event.preventDefault();
        startDateInput.value = '';
        endDateInput.value = '';
        searchButton.disabled = true; // Deshabilitar el botón
    });
});

</script>
