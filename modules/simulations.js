angular.module('simulations-module',['jspdf-module']).factory('appService',function($http,$timeout,$interval,jspdf) {
	
	function appService() {
	
		var self = this;
		
		function validate(scope,form) {
			
			var controls = scope.formHolder[form].$$controls;
			
			angular.forEach(controls,function(elem,i) {
				
				if (elem.$$attr.$attr.required) elem.$touched = elem.$invalid;
									
			});

			return scope.formHolder[form].$invalid;
			
		};		
		
		self.start = function(scope) {
			
			jspdf.init();
			
			scope.formHolder = {};
			
			scope.rc5 = {};
			scope.rc5.km = '4567812131415165';
			scope.rc5.text = 'ExcelV44';
			scope.rc5.rounds = 14;
			scope.rc5.results = [];
			
			scope.erc5 = {};
			scope.erc5.km = '4567812131415165';			
			scope.erc5.text = 'ExcelV44';	
			scope.erc5.rounds = 14;
			scope.erc5.results = [];
		
		};
		
		self.rc5 = {
			
			clear: function(scope) {
				
				// scope.rc5 = {};		
				
				$('.rc5 .output').load('cache/rc5-startup.txt',function() {
					
				});
				
			},

			encrypt: function(scope) {
				
				if (validate(scope,'rc5')) return;
				
				var start = $interval(function() {
					
					$timeout(function() {				
						$('.rc5 .output').load('cache/rc5.txt',function() {
							$('.rc5 .output').scrollTop(($('.rc5 .output')[0]).scrollHeight);								
						});						
					},300);				
					
				},500);
				
				$http({
				  method: 'POST',
				  url: 'handlers/rc5.php',
				  data: scope.erc5			  
				}).then(function mySucces(response) {
					
					scope.rc5.results = angular.copy(response.data);
					$timeout(function() { $interval.cancel(start); },1000);
					
				}, function myError(response) {

					$timeout(function() { $interval.cancel(start); },1000);

				});				
				
			}
			
		};
		
		self.erc5 = {
			
			clear: function(scope) {
				
				// scope.erc5 = {};	
				scope.erc5.results = [];
				
				$('.erc5 .output').load('cache/erc5-startup.txt',function() {
					
				});	
				
			},

			encrypt: function(scope) {
				
				if (validate(scope,'erc5')) return;
				
				var start = $interval(function() {
					
					$timeout(function() {				
						$('.erc5 .output').load('cache/erc5.txt',function() {
							$('.erc5 .output').scrollTop(($('.erc5 .output')[0]).scrollHeight);								
						});						
					},300);				
					
				},500);
				
				$http({
				  method: 'POST',
				  url: 'handlers/erc5.php',
				  data: scope.erc5			  
				}).then(function mySucces(response) {
					
					scope.erc5.results = angular.copy(response.data);
					$timeout(function() { $interval.cancel(start); },1000);
					
				}, function myError(response) {

					$timeout(function() { $interval.cancel(start); },1000);

				});					
				
			},

			print: function(scope) {
				
				var doc = new jsPDF({
					orientation: 'portrait',
					unit: 'pt',
					format: [792, 612]
				});
				
				doc.setFontSize(14);				
				doc.setTextColor(40,40,40);				
				doc.setFontType('bold');
				doc.myText('Enhanced RC5',{align: "center"},0,50);
				
				doc.setFontSize(12);
				doc.setFontType('normal');				
				doc.myText('Simulation',{align: "center"},0,70);
				
				doc.line(50, 100, 562, 100);				
				
				doc.setFontSize(16);
				doc.setFontType('normal');				
				doc.myText('Results',{align: "center"},0,130);				
				
				var columns = [
					{title: "Round", dataKey: "round"},
					{title: "Encrypted Text", dataKey: "encrypted"},
					{title: "Execution Time", dataKey: "time"}
				];
				
				var rows = scope.erc5.results;

				doc.autoTable(columns, rows, {
					// tableLineColor: [189, 195, 199],
					// tableLineWidth: 0.75,
					margin: {top: 160, left: 60},
					tableWidth: 500,
					columnStyles: {
						round: {columnWidth: 100},
						encrypted: {columnWidth: 230},
						time: {columnWidth: 150}
					},
					styles: {
						lineColor: [75, 75, 75],
						lineWidth: 0.50,
						cellPadding: 3
					},
					headerStyles: {
						halign: 'center',		
						fillColor: [191, 191, 191],
						textColor: 50,
						fontSize: 10
					},
					bodyStyles: {
						halign: 'center',
						fillColor: [255, 255, 255],
						textColor: 50,
						fontSize: 10
					},
					alternateRowStyles: {
						fillColor: [255, 255, 255]
					}
				});
				
				var blob = doc.output("blob");
				window.open(URL.createObjectURL(blob));				
				
			}
			
		};
	
	};
	
	return new appService();
	
});