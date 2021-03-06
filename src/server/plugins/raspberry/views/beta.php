<?php $engine = false; ?>
<!doctype html>
<html ng-app="app">
    <head>
    	<title>REST in BITA</title>
    
    	<link rel="shortcut icon" href="/rpi/assets/i/favicon.png" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">    
    
        <!-------------------------------------------------
            CSS
          ------------------------------------------------->
    
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="/rpi/plugins/raspberry/assets/c/beta.css">
        
        <!-------------------------------------------------
            LIB
          ------------------------------------------------->        
        
       	<!-- <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script> -->
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.0-rc.1/angular.min.js"></script>        
        <!-- UI Bootstrap (http://angular-ui.github.io/bootstrap/)-->
        <script src="/rpi/plugins/raspberry/assets/j/ui-bootstrap-tpls-0.11.0.min.js"></script>

        <!-------------------------------------------------
            APP
          ------------------------------------------------->

        <!-- Main App Dependencies -->
        <script src="/rpi/plugins/raspberry/assets/j/utils.js"></script>
        <script src="/rpi/plugins/raspberry/assets/j/angular.logger.js"></script>
        <script src="/rpi/plugins/raspberry/assets/j/angular.storage.js"></script>
        <script src="/rpi/plugins/raspberry/assets/j/angular.panel.js"></script>
        <script src="/rpi/plugins/raspberry/assets/j/angular.remotecontent.js"></script>
      
        <!-- Main App -->
    	<script src="/rpi/plugins/raspberry/assets/j/app.js"></script>
    </head>
    <body>
	    <div ng-controller="mainCtrl">
	        <header class="jumbotron">
	            <h1>{{ app.title }} / <small>REST in PI</small></h1>
	        </header>

			<main>
				<tabset>
		        	<tab heading="Infos">
				    	<panel title="Settings" title-icon="glyphicon glyphicon-cog glyphicon-default" imports="rpi, settings">
				    		<div class="list-group">
				    			<div class="list-group-item">
				    				Host : <input type="text" value="{{ rpi.host }}" class="form-control" />
				    			</div>
				    			
				    			<div class="list-group-item">
							    	Refresh all every (seconds)
								    <div class="input-group">
								    	<span class="input-group-addon">
								    		<input type="checkbox" ng-model="settings.remote.refresh" />
								      	</span>
								      	<input type="text" ng-model="settings.remote.refreshTime" class="form-control" />
								    </div>
				    			</div>
				    		</div>
				    	</panel>
				    
				        <panel title="Actions" title-icon="glyphicon glyphicon-tasks glyphicon-primary" imports="rpi">
					        <remotecontent url="/rpi/ws/raspberry/data?zone=action">
					            <div class="panel-body" ng-show="data.length > 0">
					                <span class="rpi-action" ng-repeat="action in data" ng-click="rpi.action(action)">
					                    <img src="{{ action.image }}" class="rpi_button" data-action="{{ action.action }}" title="{{ action.name }}" alt="{{ action.name }}" /> {{ action.name }}
					                </span>
					            </div>    
				            </remotecontent>
				        </panel>
				        
				        <panel title="Infos" title-icon="glyphicon glyphicon-flash glyphicon-success" imports="infos">
					        <remotecontent url="/rpi/ws/raspberry/data?zone=info">
					            <table class="table">
					                <tr ng-repeat="info in data">
					                    <td class="fitcontent">
					                        {{ info.label }}
					                    </td>
					                    <td>
					                    	<!-- Label view -->
                                            <span class="label label-default" 
                                            	ng-show="!info.max && !info.min" 
                                            	ng-class="{'label-danger' : info.error === true, 'label-success' : info.error === false, 'label-warning' : info.warning === true, 'pointer' : info.msg}"
                                            	ng-click="info.display_msg = !info.display_msg">
                                                {{ info.value }} {{ info.unit }}
                                                
                                                <span class="caret" ng-show="info.msg"></span>
					                       	</span>
					                       
					                       <!-- Progressbar view -->
											<div class="progress" ng-show="info.max || info.min">
												<div class="progress-bar" 
													ng-class="{'progress-bar-danger' : info.error === true, 'progress-bar-success' : info.error === false, 'progress-bar-warning' : info.warning === true, 'pointer' : info.msg}" 
							                      	ng-click="info.display_msg = !info.display_msg"
							                      	role="progressbar" 
							                      	aria-valuenow="{{ info.value }}" 
							                      	aria-valuemin="{{ info.min }}" 
							                      	aria-valuemax="{{ info.max }}" 
							                      	style="width: {{ info.value * 100 / info.max }}%">
													{{ info.value }} {{ info.unit }}
												</div>
						                    </div>
					                       	
					                       	<!-- Status -->
											<div class="panel panel-default status" ng-show="info.msg && info.display_msg">
												<div class="panel-body">
													{{ info.msg }}
												</div>
											</div>							                    
					                    </td>
					                </tr>
					            </table>     
				            </remotecontent>
				        </panel>
				        
				        <panel title="Pages" title-icon="glyphicon glyphicon-th-large glyphicon-info" imports="rpi">
					        <remotecontent url="/rpi/ws/raspberry/pages">
					            <table class="table">
					                <tr ng-repeat="page in data">
					                    <td class="">
						                    <a href="{{ page.url }}">
						                    {{ page.name }}
						                    </a>
					                    </td>
					                </tr>
					            </table>     
				            </remotecontent>
				        </panel> 	        
				        
				        <panel title="Services" title-icon="glyphicon glyphicon-hdd glyphicon-warning" imports="rpi" class="services">
				        	<remotecontent url="/rpi/ws/services/list">
					            <div class="list-group">
					                <span ng-repeat="service in data"  class="list-group-item">
					                	<!-- Link version : url not empty -->
					                    <a href="{{ service.url }}" ng-show="service.url">
					                    {{ service.name }}
					                    </a>
					                    
					                    <!-- No link version : url empty -->
					                    <span ng-show="!service.url">
					                    {{ service.name }}
					                    </span>
					                    
					                    <span class="label pull-right" 
					                        ng-class="{'label-success': service.enabled, 'label-warning': !service.enabled, 'label-danger': service.enabled == 255, pointer: service.cmd}" 
					                        ng-click="rpi.service.switch(service)">
					                    {{ service.enabled ? (service.enabled == 255 ? 'KO' : 'On') : 'Off' }}
					                    </span>
					                </span>
					            </div>
				            </remotecontent>
				        </panel>
			
						<panel title="Log" title-icon="glyphicon glyphicon-list-alt glyphicon-danger" imports="log, rpi" class="log">
							<actions>
								<i class="glyphicon glyphicon-trash" ng-click="rpi.cleanLog($event)"></i>
							</actions>
				            <table class="table">
				            	<thead>
					            	<tr>
					            		<th>Date</th>
					            		<th>Message</th>
					            	</tr>
				            	</thead>
				            	<tbody>
					            	<tr ng-repeat="line in log">
					            		<td  class="fitcontent">{{ line.date }}</td>
					            		<td>{{ line.msg }}</td>
					            	</tr>
				            	</tbody>
				            </table>
						</panel>
					</tab>
				
					<tab heading="Console">
						<div class="console well well-sm">
							<div ng-repeat="line in console.lines">
								<strong ng-class="{error: line.error}">[{{ line.ret }}] {{ console.login }} $&nbsp;</strong>{{ line.cmd }}<br />
								<span ng-bind-html="line.msg | nl2br"></span>
							</div>
							<form ng-submit="console.execute()">
								<strong class="prompter" ng-class="{error: console.prompter.error}">[{{ console.prompter.ret }}] {{ console.login }} $&nbsp;</strong>
								<input type="text" class="input" ng-model="console.prompt" ng-keydown="console.change($event)" ng-focus="console.fixStyle(true)" />
							</form>
						</div>
					</tab>
					
					<tab heading="About">
					<pre>
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec commodo eros id scelerisque elementum. Praesent in blandit leo. Sed eu metus eu felis pellentesque tristique. Praesent dictum eros in leo tempor, sit amet imperdiet magna pretium. In commodo gravida vulputate. Fusce feugiat purus a elit tristique vestibulum. Aliquam erat nisi, pharetra ut lacus et, rutrum mollis lorem. Sed in tortor non nunc consequat ullamcorper auctor semper lorem. Curabitur hendrerit semper dignissim. Aliquam rutrum ipsum nec risus rutrum, vitae posuere erat pulvinar.

Curabitur vehicula mi ac elit lacinia accumsan. Aliquam sit amet nisl porttitor, viverra dui eget, commodo tortor. Mauris rhoncus felis a risus condimentum, vel faucibus nunc tristique. Pellentesque mollis erat vitae tempor accumsan. Nullam iaculis felis vel sodales rutrum. Quisque nibh risus, tempus id pharetra eget, consectetur vitae nulla. Mauris in nunc a lectus luctus hendrerit. Donec vitae felis rutrum, fringilla massa ut, sagittis risus. Suspendisse finibus lectus non magna facilisis bibendum eu et leo. In scelerisque porta magna, in laoreet eros fermentum ut. Quisque sed tortor sit amet metus semper egestas consectetur sodales dui. Morbi rhoncus, dolor vel faucibus imperdiet, lectus leo sagittis lectus, nec porttitor augue elit ac enim. Quisque vehicula tellus augue, in vestibulum lorem semper maximus. Quisque viverra, ipsum eget congue volutpat, libero dolor accumsan nunc, eget imperdiet nulla dui ut ex. Morbi dapibus dolor eget volutpat fermentum. Sed efficitur ante nec ante semper sagittis.

Mauris porta est vel sapien condimentum, ut molestie metus ornare. In et faucibus turpis, quis consectetur libero. Praesent sit amet sagittis eros. Aenean ut felis in nunc eleifend blandit vitae nec nunc. Cras aliquam augue velit, vestibulum condimentum magna sagittis vitae. Praesent pharetra augue ut nulla dapibus tincidunt. Aenean ullamcorper sagittis fringilla. In eget nisl efficitur, malesuada dolor at, efficitur ex. Aenean id accumsan nisi. Nullam id nulla vel velit viverra posuere. Aenean id lacus a libero varius elementum vel non velit. Cras rhoncus sit amet enim nec porta.

Nullam ut scelerisque velit, mattis aliquet arcu. Morbi pulvinar nunc sed risus sodales finibus. Nam molestie bibendum nunc, eget rhoncus nunc tincidunt elementum. Donec at lorem ut tortor placerat placerat sit amet et nisi. Nulla ac arcu placerat lectus laoreet facilisis sed ac odio. Donec faucibus maximus nunc. Vivamus turpis tortor, dignissim non aliquam eget, placerat ac eros. Nam interdum enim turpis, nec auctor massa fringilla sit amet. Vivamus tempor justo dui, eu venenatis eros auctor non. Proin eget malesuada enim. Maecenas a enim augue. Phasellus consectetur mi ut ultrices convallis.

Nullam malesuada, diam eget feugiat eleifend, neque lectus dictum sem, dictum pellentesque eros elit in nulla. Donec quis lorem eleifend, sollicitudin sapien in, porttitor lorem. In sit amet nulla turpis. Suspendisse eu pretium erat. Praesent massa lacus, pulvinar ut posuere vitae, venenatis sit amet leo. Duis fermentum et massa quis tristique. Phasellus ac quam ac elit interdum porta. Morbi id libero sagittis, pretium arcu eu, tristique massa. Curabitur eu augue ipsum. Etiam vitae tellus luctus, dapibus nisl sed, aliquet tellus. Fusce vehicula erat sit amet viverra semper. Etiam aliquet, purus ut varius cursus, ex lectus eleifend mi, eget commodo massa lacus eu lorem.						
					</pre>					
					</tab>
		        </tabset>
			</main>
	
        	<footer>
        		&copy; 2014 Reeska / REST in PI
        	</footer>
        </div>
    </body>
</html>