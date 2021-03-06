/****************************************
 * Main app
 ****************************************/

angular.module('app', ['ui.bootstrap', 'reeska.storage', 'reeska.panel', 'reeska.panel.remotecontent', 'reeska.logger'])
/**
 * Constantes
 */
.value('baseurl', '/rpi')
.value('dataurl', '/rpi/ws/raspberry/data')
.value('servurl', '/rpi/ws/services/service')
.value('shellurl', '/rpi/ws/raspberry/shell')

/**
 * Main controller
 */
.controller('mainCtrl', function($scope, $http, $storage, $logger, $timeout, $rootScope, baseurl, servurl, shellurl) {
    $scope.log = $storage.get('log', []);
    
    /*
     * init logger with list
     */
    $logger.init($scope.log);

    angular.extend($scope, {
        	/**
        	 * Global config
        	 */
            app : {
                title : 'RPI'
		    },
		    
		    /**
		     * RPI API
		     */
            rpi : {
            	host : window.location.host,
		        action : function(action) {
		            var sl = $logger.log('Action : ' + action.name);
		            
	                $http.get(baseurl + '/' + action.action)
	                .success(function(data) {
	                    sl.append(' [OK]');
	                })
	                .error(function(data) {
	                    sl.append(' [FAIL] : ' + data.error);
	                });
		        },
		        service : {
		            switch : function(service) {
		                if (!service.cmd)
		                    return;
		                
		                var sl = $logger.log((service.enabled ? 'Disabling' : 'Enabling') + ' service `' + service.name + '`...');
		            
		                $http.get(servurl, { params: { service : service.name, action : service.enabled ? 'stop' : 'start'}})
		                .success(function(data) {
		                    service.enabled = data.started;
		                    sl.append(' [OK]');
		                })
		                .error(function(data) {
		                    service.enabled = 255;
		                    sl.append(' [FAIL] : ' + data.error);
		                });
		            }
		        },
		        shell : {
					execute : function(cmd, print) {
						$http.get(shellurl, { params: { cmd : cmd}})
						.success(function(data) {
						    if (data.error) {
		                        print(data.error, -1);				        
						    } else {
		                        print(data.output, data.ret);
						    }
						})
						.error(function(data) {
							print('Ajax error ' + data);
						});
					}            		        
		        },
		        cleanLog : function($event) {
		        	$event.stopPropagation();
		        	$logger.clean();
		        },
		    },
		    
    		/**
	         * RPI Console
	         */
	        console : {
	            login : "pi@rpi",
	        	lines : [],
	        	prompt : '',
	        	prompter : { ret : 0 },
	        	history : [],
	        	hcursor : -1,
	        	hprompt : '',
	        	
	        	execute : function() {
	        		var $console = this;
	        		$scope.rpi.shell.execute(this.prompt, function(text, ret) {
	        		    var line = {
	        			    ret: ret,
	        				cmd: $console.prompt, 
	        				msg: text,
	        				error: ret != 0
	        			};
	        		
	        			/*
	        			 * print result
	        			 */
	        			$console.lines.push(line);
	        			$console.prompter = line;
	        			
	        			/*
	        			 * indent input
	        			 */
        			    $console.fixStyle();
	        			
	        			/*
	        			 * manage history
	        			 */
	        			$console.history.unshift($console.prompt);
	        			$console.prompt = '';
	        			$console.hcursor = -1;
	        		});
	        	},
	        	
                change : function($event) {
                    /*
                     * prevent default behavior :
                     * UP: sentance begin,
                     * DOWN: sentance end
                     */
                    if ($event.keyCode == Arrows.UP ||
                        $event.keyCode == Arrows.DOWN) {
                        $event.preventDefault();
                    }
                    
                    if ($event.keyCode == Arrows.UP) {
                        if (this.hcursor === -1) {
                            this.hprompt = this.prompt;
                        }
                        
                        ++this.hcursor;
                        
                        /*
                         * limit max
                         */
                        if (this.hcursor >= this.history.length) {
                            this.hcursor = this.history.length - 1;
                        }
                        
                        this.prompt = this.history[this.hcursor];
                        
                    } else if ($event.keyCode == Arrows.DOWN) {
                        --this.hcursor;
                        
                        /*
                         * limit min
                         */
                        if (this.hcursor < 0) {
                            this.hcursor = -1;
                        }
                        
                        /*
                         * if down to -1, revert previous taped command
                         */
                        if (this.hcursor === -1) {
                            this.prompt = this.hprompt;
                        } else {
                            this.prompt = this.history[this.hcursor];
                        }
                    }
                },
                
                fixStyle: function(focus) {
                    $timeout(function() {
                        var input = angular.element('.input'),
                            prompter = angular.element('.prompter');
                        
                        input.css({'text-indent' : prompter.width()});
                    });
                }
            }
        }
    );
    
    /**
     * Settings
     */
    $scope.settings = {
        remote : {
            refresh : true,
            refreshTime : 10
        }
    };
    
    /**
     * Refresh looper
     */
    loop(function() {
        if (!$scope.settings.remote.refresh || !$rootScope.remoteContents) return;
        
        angular.forEach($rootScope.remoteContents, function(item) {
            item.refresh();
        });
        
    }, function() { 
        return $scope.settings.remote.refreshTime * 1000; 
    });
})

/**
 * Newline to br html tag filter.
 * From: https://gist.github.com/naoyeye/8220054
 */
.filter('nl2br', function($sce){
    return function(msg,is_xhtml) { 
        var is_xhtml = is_xhtml || true;
        var breakTag = (is_xhtml) ? '<br />' : '<br>';
        var msg = (msg + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
        return $sce.trustAsHtml(msg);
    };
})
;