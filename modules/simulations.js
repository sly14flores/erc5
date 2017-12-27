angular.module('simulations-module',[]).factory('appService',function($http,$timeout,$interval) {
	
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
			
			scope.formHolder = {};
			
			scope.rc5 = {};
			scope.rc5.km = '3l337';
			scope.rc5.text = 'ExcelV44';			
			
			scope.erc5 = {};
			scope.erc5.km = '4567812131415165';			
			scope.erc5.text = 'ExcelV44';	
			scope.erc5.rounds = 3;
		
		};
		
		self.rc5 = {
			
			clear: function(scope) {
				
				// scope.rc5 = {};		
				
				$('.rc5 .output').load('cache/rc5-startup.txt',function() {
					
				});
				
			},

			encrypt: function(scope) {
				
				if (validate(scope,'rc5')) return;
				
				$http({
				  method: 'POST',
				  url: 'handlers/rc5.php',
				  data: scope.rc5			  
				}).then(function mySucces(response) {
					
					$timeout(function() {
						$('.rc5 .output').load('cache/rc5.txt',function() {
							
						});
					},500);					
					
				}, function myError(response) {
					//
				});					
				
			}
			
		};
		
		self.erc5 = {
			
			clear: function(scope) {
				
				// scope.erc5 = {};	
				
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
					
					$timeout(function() { $interval.cancel(start); },1000);
					
				}, function myError(response) {

					$timeout(function() { $interval.cancel(start); },1000);

				});					
				
			}					
			
		};
	
	};
	
	return new appService();
	
});