$(document).ready(() => {
    // initialising context for doughnut chart
    let ctx = $("#progress-chart").get(0).getContext("2d");

    // initialising datepicker
    $("#date-picker").datepicker({
        minDate: 0,
        dateFormat: "dd/mm/yy",
    });

    // creating doughnut chart
    progressChart = new Chart(ctx, {
        type: "doughnut",
        data: {
            // labelling data
            labels: ["In Progress", "Completed", "Overdue"],
            // instantiating default data to be added later, setting colours
            datasets: [
                {
                    data: [0,0,0],
                    backgroundColor: ["#888", "#D9D9D9", "#FF7A00"],
                    borderColor: getComputedStyle(document.body).getPropertyValue("--window-background"),
                },
            ],
        },
        options: {
            // setting responsive-ness values in order to make sure the chart is appropriately sized
            responsive: false,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: getComputedStyle(document.body).getPropertyValue("--text-color"),
                    },
                },
            },
        },
    });

    // function to resize chart, usually called on update of a chart
    function resizeChart(){
        progressChart.resize();
        progressChart.update();
    }

    $(window).on('resize', resizeChart);
    // fixes an issue that causes the chart to disappear on resizing a window
    $(document).on("visibilitychange", function() {
        if (document.visibilityState === 'visible') {
            resizeChart();
        }
    });

    // on-the-fly switching between themes for the chart
    const isDarkMode = window.matchMedia('(prefers-color-scheme: dark)');
    isDarkMode.addEventListener("change", function() {
        progressChart.data.datasets[0].borderColor = getComputedStyle(document.body).getPropertyValue("--window-background");
        progressChart.options.plugins.legend.labels.color = getComputedStyle(document.body).getPropertyValue("--text-color");
    });
});

let progressChart = null;

// updates all the charts and tables on the page with values from the database
function updateChartsTables() {
    let selectedProject = $("#project-dropdown").get(0).value; // retrieving seleceted project's ID
    $.ajax({
        type: "POST",
        url: "dashboard/get-project-data.php",
        data: { projectId: selectedProject }, // passing project ID to retrieve data from
        success: function(response) {
            let jsonObject = JSON.parse(response);
            populateTable("#overdue-table", jsonObject.overdue);
            populateTable("#imminent-table", jsonObject.imminent);
            $("#date-picker").datepicker("setDate", jsonObject.deadline);

            progressChart.data.datasets[0].data = jsonObject.data;
            progressChart.update(); // refreshes progress chart to reflect new data
        }
    });
};

// fills a table with a given ID with data
function populateTable(tableId, data) {
    let table = $(tableId).get(0);
    // clear table body
    $(tableId).find("td").remove();

    // iterating through data adding rows
    for (let i = 0; i < data.length; i++){
        let datum = data[i];
        let row = document.createElement("tr");
        row.setAttribute("class", "table-data");

        // iterating through datum adding columns/cells
        for (let key in datum) {
            if (datum.hasOwnProperty(key)){
                let cell = document.createElement("td");
                cell.textContent = datum[key];
                console.log(datum[key]);
                row.appendChild(cell);
            }
        }

        table.appendChild(row);
    }
}
