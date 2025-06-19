import './bootstrap';
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";

// For range mode
flatpickr("#date_range", {
    mode: "range",
    dateFormat: "Y-m-d",
});


// DataTables setup
import $ from 'jquery';
window.$ = $;
window.jQuery = $;
import 'datatables.net';
import 'datatables.net-dt';

import 'datatables.net-dt/css/dataTables.dataTables.min.css'; // ✅ الصحيح



$(document).ready(function () {
    const table1 = document.getElementById('categories-table');
    if (table1) {
        $('#categories-table').DataTable();
    }

    const table2 = document.getElementById('brands-table');
    if (table2) {
        $('#brands-table').DataTable();
    }
});