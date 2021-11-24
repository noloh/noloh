var printChartPDF = function(id) {
	var chart = $("#" + id).getKendoChart();
	chart.exportPDF({paperSize: "A5", landscape: true}).done(function(data) {
		kendo.saveAs({
			dataURI: data,
			fileName: "chart.pdf"
		});
	});
};

var printChartImage = function(id) {
	var chart = $("#" + id).getKendoChart();
	chart.exportImage().done(function(data) {
		kendo.saveAs({
			dataURI: data,
			fileName: "chart.png"
		})
	});
};