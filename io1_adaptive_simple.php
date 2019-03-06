<!DOCTYPE>

<html>

	<head>
		<title>io.1</title>
		<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1">
		<link  rel="stylesheet" type="text/css" href="io1_stylesheet.css">
		<script src="https://code.jquery.com/jquery-2.0.3.min.js"></script>
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/qtip2/2.2.0/jquery.qtip.js"></script>
		<script src="cytoscape.js"></script>
		<script src="cytoscape-node-html-label.js"></script>
	</head>

	<body>
		<div id="cy"></div>

		<div id="newPathAlert"> Are you sure you want to start a new path?
			<div id="newPathButton">Yes</div>
			<div id="continuePathButton">No</div>
		</div>


		<div id="sidebar">
			<div id="routes">
				Origin: <div id="originDiv"></div>
				<br />
				<br />
				Destination: <div id="destinationDiv"></div>
			</div>

			<div id="tripSummary">
				<p style="font-size: 16px"><u>Trip Summary</u></p>
				<div id="selectedPath"></div>
				<u>Time to Destination</u>
				<div id="travelTime">0</div></br>
				<u>Money Earned on Previous Trial</u>
				<div id="lastEarn">N/A</div>
				<br />
				<u>Total Money Earned (CAD)</u>
				<div id="totalEarn">0.00 CAD</div>
				<br />
				<u>Trials Remaining</u>
				<div id="trialsRemaining">20</div>
			</div>

			<p><input type="button" value="Submit Route" id="routeSubmitButton"/> </p>
			<p><input type="button" value="Submit Route" id="routeSubmitPlaceholder"/> </p>
		</div>

		<div id="controlMenu">
			<div id="menuIcon"></div>
			<p style="font-size: 20px"><b><u>Objective:</u></b></p>
			Try to find the fastest route between the origin station (blue circle) and the destination station (red circle)<br />
			<p style="font-size: 20px"><b><u>Controls:</u></b></p>
			- To inspect a station: Click and hold<br />
			- To zoom: Scroll with your cursor<br />
			- To pan: Click and drag with your mouse<br />
			- To select a line: Just click on it. You must start your route at the origin station and can only select lines that are connected to previously selected lines.
		</div>


		<!--
		 _____   __ __   _____
		/\  o \ /\ \\ \ /\  o \
		\ \   _\\ \    \\ \   _\
		 \ \__\/ \ \_\\_\\ \__\/
		  \/__/   \/_//_/ \/__/

		-->


		<!--PHP FORMS FOR SQL DATABASE-->


		<div id="endCard">
			End of Experiment <br />
			<p style="font-size: 32">You earned:
				<p style="font-size: 24; text-decoration: underline; color: rgb(250,150,0)" id="finalEarnings"></p>
			</p>
			<p style="font-size: 24; padding: 0 200 0 200">
			 Remember, 10 CAD of your earnings comes from the original hit. I will send you a bonus for the remaining amount, which will be between 0 and 5 CAD.
			 <br />
			 <br />
			 Please enter your MTurk ID below so that we can link your earnings to your account.
			 <br />
			 <br />
			 Once you submit your ID you will be taken to a page that will provide you with a code that you can enter on the page for the original HIT.
			</p>
			<form id ="logging_form" name="logging_form" method="post" action="io_php.php">
				<!-- SCREEN STATE INFORMATION -->
					<input type="hidden" name="logTime_php" id="logTime_php" value="" />
					<input type="hidden" name="cyBottom_php" id="cyBottom_php" value="" />
					<input type="hidden" name="cyTop_php" id="cyTop_php" value="" />
					<input type="hidden" name="cyLeft_php" id="cyLeft_php" value="" />
					<input type="hidden" name="cyRight_php" id="cyRight_php" value="" />
					<input type="hidden" name="mCX_php" id="mCX_php" value="" />
					<input type="hidden" name="mCY_php" id="mCY_php" value="" />
					<input type="hidden" name="mWX_php" id="mWX_php" value="" />
					<input type="hidden" name="mWY_php" id="mWY_php" value="" />
					<input type="hidden" name="screenWidth" id="screenWidth" value="" />
					<input type="hidden" name="screenHeight" id="screenHeight" value="" />

				<!-- ROUTE SELECTION & PERFORMANCE METRICS -->

					<input type="hidden" name="allSelected" id="allSelected" value="" />
					<input type="hidden" name="finalSelection" id="finalSelection" value="" />
					<input type="hidden" name="timeArray_php" id="timeArray_php" value="" />
					<input type="hidden" name="finalEarnings_php" id="finalEarnings_php" value="" />


					<input type="hidden" name="routeArray_php" id="routeArray_php" value="" />
					<input type="hidden" name="conArray_php" id="conArray_php" value="" />

				<!-- TLX -->

					<input type="hidden" name="tlx_php" id="tlx_php" value="" />

				<!-- MISC	 -->
						<input type="text" id="trialID" name="trialID" value="MTurk ID" />
						<input type="submit" value="Submit" class="buttons" id="submitButton"/>
			</form>
			<div id="idCheck">Set ID!</div>
		</div>
	</body>

		<script>

//this is all stuff to just set up the network

endCard=0;

function shuffle(o){
	for (var j, x, i=o.length; i; j = Math.floor(Math.random()*i),x=o[--i], o[i]=o[j], o[j] =x);
	return o;
}

var optimal_times = {};
	optimal_times['9p|4l'] = 184;//


var condition_array = shuffle([0]);
	var curCon=0;

var route_array = shuffle([
						'9p|4l',
					]);
	var curRoute=0;

trialCount=0;

$('#routeSubmitButton').click(function(){
	var path_array_replace = path_array.join(',').replace(/,/g, ';').split();
	var time_array_replace = time_array.join(',').replace(/,/g, ';').split();

	finalSelection.push((curCon) + '/' + route_array[(curRoute)] + '/' + path_array_replace);
	timeArray_php.push(time_array_replace);

	routeLog();

	trialCount++;
	$('.response').prop('checked', false);
	$('.individualSlider').hide();

	curCon++;
	curRoute++;
	route = route_array[curRoute];
	condition = condition_array[curCon];

	$('#tlx').show();
	$('#mentalDiv').show();
	$('#nextTrialButton').hide();
	window.scrollTo(0, 0);

	function calculate_earnings(){
		money_earned = 0.25 -(0.0025*(timeSum - optimal));
		if ((money_earned < 0.25)&&(money_earned > 0)){
			money_earned=0.5 + money_earned;
		}else if (money_earned>0.25){
			money_earned=0.5 + 0.25;
		}else{
			money_earned=0.5;
		}
		earnings_array.push(money_earned);
	}
	calculate_earnings();


	function add(a, b) {
		    return a + b;
	}

	earningsTotal = earnings_array.reduce(add, 0);
	totalEarn.innerHTML = earningsTotal.toFixed(2) + ' CAD';
	lastEarn.innerHTML = earnings_array[earnings_array.length-1].toFixed(2) + ' / 0.75 CAD';
	trialsRemaining.innerHTML = 20 - trialCount;
});



$('#idCheck').click(function(){
	if ($('#trialID').val() != 'MTurk ID'){
		$('#submitButton').show();
	}else{
		alert('Input your MTurk ID before advancing');
	}
});

//pulls the optimal time for the current route
var optimal = optimal_times[route_array[curRoute]];


timeArray_php =[];
finalSelection =[];

function nextTrial(){
	focus=1;
	timesRecorded=[];
	breaks=[];
	breakLength=0;
	if (curCon < condition_array.length){
		$('#routeSubmitButton').hide();
		loadGraph();
		$('#routeSubmitPlaceholder').show();
		cy.nodes("[type='single']").css({
			'background-color':'white',
			'border-color':'white'
		});
		cy.nodes("[type='connecting']").css({
			'background-color':'black',
			'border-color':'white'
		});
		setTimeout(function(){$(document).ready(routeUpdate)},1000);
	}else{
		$('#endCard').show();
		endCard = 1;
		$('#sidebar').hide();
		$('#minimap').hide();
		$('#clear').hide();
		cy.destroy();
		function calculate_earnings(){
			money_earned = 0.25 -(0.0025*(timeSum - optimal));
			if ((money_earned < 0.25)&&(money_earned > 0)){
				money_earned=0.5 + money_earned;
			}else if (money_earned>0.25){
				money_earned=0.5 + 0.25;
			}else{
				money_earned=0.5;
			}
			earnings_array.push(money_earned);
		}
		calculate_earnings();
		function add(a, b) {
			    return a + b;
		}
		earningsTotal = earnings_array.reduce(add, 0);
		finalEarnings.innerHTML = earningsTotal.toFixed(2) + ' CAD!';
		$('#finalEarnings_php').val(earningsTotal);
	}
};

function routeUpdate(){
	console.log("current condition: " + condition_array[curCon]);
	console.log("current route: " + route_array[curRoute]);
	trialStartTime = new Date().getTime();
	origin = route_array[curRoute].split('|', 1)[0];
	destination = route_array[curRoute].substring(route_array[curRoute].indexOf('|') + 1);
	originName = cy.nodes("[id = " +"'"+ origin +"'" + "]").data('label');
	destinationName = cy.nodes("[id = " +"'"+ destination +"'" + "]").data('label');

	originDiv.innerHTML = originName;
	destinationDiv.innerHTML = destinationName;
	cy.nodes("[id = " +"'"+ origin +"'" + "]").css({
		'background-color':'rgb(100, 150, 255)',
		'border-color':'rgb(50, 80, 200)',
		'width':'60px',
		'height':'60px'
	});
	cy.nodes("[id = " +"'"+ destination +"'" + "]").css({
		'background-color':'rgb(255, 100, 150)',
		'border-color':'rgb(200, 40, 80)',
		'width':'60px',
		'height':'60px'
	});

	optimal = optimal_times[route_array[curRoute]];
};


//Eof experimental setup


/*
cytoscape start
  ______   ___ ___  _______  ______
 /\   __\ /\  \\  \/\__   _\/\     \
 \ \  \___\/`.   .'\/_/\  \/\ \  o  \
  \ \_____\ `./\__\   \ \__\ \ \_____\ A\
   \/_____/   \/__/    \/__/  \/_____/ V/

*/

function loadGraph(){
	$.getJSON("content.js", function(data){

//change the style of Cytoscape based on Exp Condition
		var cy = window.cy = cytoscape({
			container: document.getElementById('cy'),
			zoom: 0.2,
			maxZoom: 2.5,
			minZoom: 0.2,
			motionBlur: false,
			wheelSensitivity: 1,
			selectionType: 'additive',
			elements:  data,
			boxSelectionEnabled: false,
			motionBlur: false,
			layout: {
				name: 'preset',
				animate: false,
				fit: false
			},
			style: [
				{
					selector: 'node',
					style: {
						'content': 'data(id)',
						'background-color':'white',
						'border-color':'white',
						'border-width':'5px',
						'color':'black',
						'text-outline-color':'white',
						'text-outline-width':'7px',
						'font-size':'50px',
						'label':'',
						'width':'20px',
						'height':'20px',
						'text-valign':'top',
					}
				},
				{
					selector: 'node[type = "connecting"]',
					style: {
						'content': 'data(id)',
						'background-color':'white',
						'border-color':'white',
						'border-width':'5px',
						'color':'black',
						'text-outline-color':'white',
						'text-outline-width':'7px',
						'font-size':'50px',
						'label':'',
						'width':'20px',
						'height':'20px',
						'text-valign':'top',
					}
				},
				{
					selector: 'node[type = "hub"]',
					style: {
						'content': 'data(id)',
						'background-color':'rgb(250,100,100)',
						'border-color':'white',
						'border-width':'10px',
						'color':'black',
						'text-outline-color':'white',
						'text-outline-width':'3px',
						'label':'',
						'width':'40px',
						'height':'40px',
						'text-valign':'top',
					}
				},
				{
					selector: 'edge',
					style: {
						// 'line-color':'rgb(255,150,0)',
						'width': '8px',
						'content':'data(line_name)',
						'label':'data(speed)',
						'curve-style': 'haystack',
						'haystack-radius': '0',
						'edge-text-rotation':'autorotate',
						'opacity':'0.7',
						'color':'white'
					}
				},
				{
					selector: 'edge[line_no = "hub"]',
					style:{
						'line-color': 'rgb(255,250,255)',
						'opacity':'1.0',
						'width': '20px',
						'color':'black',
						'curve-style':'haystack',
						'haystack-radius':'0'
					}
				},
				{
					selector: 'edge[line_no = "express"]',
					style:{
						'line-color': 'rgb(255,100,100)',
						'opacity':'0.8',
						'width': '15px',
						'color':'white',
						'curve-style':'haystack',
						'haystack-radius':'0'
					}
				},
				{
					selector: 'edge[line_no = "1"]',
					style:{
						'line-color': 'rgb(255,150,0)',
					}
				},
				{
					selector: 'edge[line_no = "2"]',
					style:{
						'line-color': 'rgb(15,250,200)',
					}
				},
				{
					selector: 'edge[line_no = "3"]',
					style:{
						'line-color': 'rgb(250,15,200)',
					}
				},
				{
					selector: 'edge[line_no = "4"]',
					style:{
						'line-color': 'rgb(250,250,0)',
					}
				},
				{
					selector: 'edge[line_no = "5"]',
					style:{
						'line-color': 'rgb(100,250,250)',
					}
				},
				{
					selector: 'edge[line_no = "6"]',
					style:{
						'line-color': 'rgb(50,255,50)',
					}
				},
				{
					selector: 'edge[line_no = "7"]',
					style:{
						'line-color': 'rgb(105,105,180)',
					}
				},
				{
					selector: 'edge[line_no = "8"]',
					style:{
						'line-color': 'rgb(250,250,180)',
					}
				},
				{
					selector: 'edge[line_no = "9"]',
					style:{
						'line-color': 'rgb(170,20,20)',
					}
				},
				{
					selector: 'edge[line_no = "10"]',
					style:{
						'line-color': 'rgb(100,200,250)',
					}
				},
				{
					selector: 'edge[line_no = "11"]',
					style:{
						'line-color': 'rgb(250,250,180)',
					}
				},
				{
					selector: 'edge[line_no = "12"]',
					style:{
						'line-color': 'rgb(250,200,100)',
					}
				},
				{
					selector: 'edge[type = "bridge"]',
					style: {
						'line-color': 'rgb(150,150,150)'
					}
				},
				{
					selector: 'edge[type = "distractor"]',
					style: {
						'line-color': 'rgb(250,250,250)'
					}
				},
				{
					selector: 'edge:selected',
					style: {
						'line-color': 'rgb(0,100,255)',
						'opacity':'1.0',
						'text-outline-color':'black',
						'text-outline-width':'5px',
						'font-size':'15px',
						'color':'white'
					}
				},
				{
					selector: 'core',
					style: {
						'active-bg-color': '#fff',
						'active-bg-opacity': '0.333'
					}
				}
			]
		});

		//create custom labels
		position_array =[];
		nodes = cy.nodes();

		for (var i = 0; i < nodes.length; i++) {
			var position_x = nodes[i].position().x;
			var position_y = nodes[i].position().y;
			var node_id = nodes[i].id();
			var node_position_array = [node_id, position_x, position_y];
			position_array.push(node_position_array);
		}

		cy.nodes().ungrabify();
		cy.nodes().unselectify();

	});

};

menuCount = 0;

	$('#menuIcon').click(function(){
		if (menuCount == 0){
			$('#controlMenu').css({
				'transform':'translateX(0%)'
			});
			$('#menuIcon').css({
				'transform':'rotate(180deg)'
			})
			menuCount++;
		}else{
			$('#controlMenu').css({
				'transform':'translateX(-100%)'
			});
			$('#menuIcon').css({
				'transform':'translateX(120%)'
			})
			menuCount=0;
		}
	})

	cont=1;
	cur_array = new Array();


	function start_window(){
		sWidth = $(window).width();
		sHeight = $(window).height();
	}
	$(document).ready(start_window);


	var cursorX;
	var cursorY;

	first_move = 0;
	document.onmousemove = function(e){
		cursorX = e.pageX;
		cursorY = e.pageY;
		if ((first_move ===0)){
			start_time = (new Date()).getTime();
			log();
		};
		if (first_move<5){
			first_move++;
		}
	}

	//define Arrays
	var logTime_php_array=[]
	var condition_php_array=[]
	var route_php_array=[]
	var cyBottom_php_array=[] // distance from bottom
	var cyTop_php_array=[] // distance from top, left, right, etc.
	var cyLeft_php_array=[] // calculate width and height using these
	var cyRight_php_array=[]
	var mCX_php_array=[] // mouse position relative to canvas's x
	var mCY_php_array=[] // mouse position relative to canvas's y
	var mWX_php_array=[] // mouse position reative to window's x
	var mWY_php_array=[]
	var screenWidth_array=[] // right -left
	var screenHeight_array=[] // top - bottom
	// with*height to get screen.  Ratio between screen size and map gets smaller.
	var viewPercent_array=[] // a NEW array to store the values of the calculated view percentage

	function log(){
		if (endCard < 1){
		setTimeout((function(){
			var sWidth = $(window).width();
		  var sHeight = $(window).height();
			var sSize = sWidth*sHeight;
			//Get screen state & mouse coordinates
				var mX = cursorX;
				var mY = cursorY;

				var cyLeft = (cy.elements().renderedBoundingBox().x1).toFixed(2);
				var cyRight = (cy.elements().renderedBoundingBox().x2).toFixed(2);
				var cyWidth = cyRight - cyLeft;

				var cyTop = (cy.elements().renderedBoundingBox().y1).toFixed(2);
				var cyBottom = (cy.elements().renderedBoundingBox().y2).toFixed(2);
				var cyHeight = cyBottom - cyTop;
				// view percent is canvas size relative to screen size

				var mCX = (((mX-cyLeft)/cyWidth)*4000).toFixed(2);//spread of x positions of stations
				var mCY = (((mY-cyTop)/cyHeight)*2400).toFixed(2);//spread of y positions of stations
				var mWX = (mX/sWidth).toFixed(2);//% of screen width
				var mWY = (mY/sHeight).toFixed(2);//% of screen height
				var percent = (cyHeight*cyWidth/(sSize))

				cyBottom_php_array.push(cyBottom);
				cyTop_php_array.push(cyTop);
				cyLeft_php_array.push(cyLeft);
				cyRight_php_array.push(cyRight);
				mCX_php_array.push(mCX);
				mCY_php_array.push(mCY);
				mWX_php_array.push(mWX);
				mWY_php_array.push(mWY);
				screenWidth_array.push(sWidth);
				screenHeight_array.push(sHeight);
				viewPercent_array.push(percent);
			log();
			adapt();
		}), 200);
		}
	}

	node_radius_array =[2000]

	function adapt(){
		//assume a state of non-information overload
		overload=0;
		mc_array1 = [];
		mc_array2 = [];


			document.onmousemove = function(e){
				cursorX = e.pageX;
				cursorY = e.pageY;
			};

			mc_array1 = mCX_php_array.slice(Math.max(mCX_php_array.length - 75, 1));

			//finds the difference between successive cursor x coordintes
			mc_diff = Math.abs(mc_array1[mc_array1.length-1]-mc_array1[mc_array1.length-2]);
			//pushes absolute value of the differences to mc_array2
			mc_array2.push(mc_diff);

			//calculates the sum of differences over last 15 seconds
			total_mouse=0;
			for (var i = 0; i < mc_array2.length; i++) {
				total_mouse += mc_array2[i] << 0;
			};

			//simple decision tree
			if (total_mouse<50){
				overload = 1;
			}else{
				overload = 0;
			};

			//checks for the attentional state
			//updates the radius
			if (overload==0){
				cur_radius = node_radius_array[0]-150;
				if (cur_radius<900){
					cur_radius = 800;
					node_radius_array.push(cur_radius);
				}else{
					node_radius_array.push(cur_radius);
				}
				node_radius_array.shift();
			}else{
				if (node_radius_array[0]<1500){
					cur_radius = node_radius_array[0]+100;
				}else{
					cur_radius = 1625;
				}
				node_radius_array.push(cur_radius);
				node_radius_array.shift();
			};

			var cyLeft = (cy.elements().renderedBoundingBox().x1).toFixed(2);
			var cyRight = (cy.elements().renderedBoundingBox().x2).toFixed(2);
			var cyWidth = cyRight - cyLeft;

			var cyTop = (cy.elements().renderedBoundingBox().y1).toFixed(2);
			var cyBottom = (cy.elements().renderedBoundingBox().y2).toFixed(2);
			var cyHeight = cyBottom - cyTop;

			//gets cursor positions relative to graph
			var mCX = ((cursorX-cyLeft)/cyWidth)*4000;//spread of x positions of stations
			var mCY = ((cursorY-cyTop)/cyHeight)*2400;//spread of y positions of stations

			//determines what stations will have labels displayed
			for (var i = 0; i< position_array.length; i++){
				var distance_x = mCX - position_array[i][1];
				var distance_y = mCY - position_array[i][2];
				var distance = (((distance_x**2) + (distance_y**2))**0.5);

				if (distance > cur_radius){
					nodes[i].addClass('downNode').removeClass('activeNode');
				}else{
					nodes[i].addClass('activeNode').removeClass('downNode');
				}
			};
			update_node_labels();
	};

	function update_node_labels(){
		cy.nodeHtmlLabel([{
			query: '.activeNode',
			halign: 'left',
			valignBox: 'top',
			halignBox: 'left',
			tpl: function(data){
				return '<p class="labelWhite" style="opacity:1.0; font-size: 30px">'
				+ data.delay + '</p>'
				}
		}]);
	}

	$(document).ready(function(){
		loadGraph();
		start_window();
		setTimeout(function(){
			routeUpdate();
			adapt();
		},1000)
	});


		</script>
</html>
