var printChartPDF = function() {
	var chart = $(".k-chart").getKendoChart();
	chart.exportPDF({paperSize: "A5", landscape: true}).done(function(data) {
		kendo.saveAs({
			dataURI: data,
			fileName: "chart.pdf"
		});
	});
};

var printChartImage = function() {
	var chart = $(".k-chart").getKendoChart();
	chart.exportImage().done(function(data) {
		kendo.saveAs({
			dataURI: data,
			fileName: "chart.png"
		})
	});
};