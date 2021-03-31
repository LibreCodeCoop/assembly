function replaceResponses(responses){
	var newArr = Object.keys(responses).map(function(key){return [(key), responses[key]];});
	const grid = document.getElementById("gridVotes");
	grid.innerHTML = "";
	newArr.forEach((arr)=>{
			grid.innerHTML = '<div class="grid-item-content"><div class="result-content"><h1>'
				+ arr[1].text + '</h1><div class="result-value"><h3>'
				+ arr[1].total + '</h3></div></div></div>' + grid.innerHTML;
		});
	return;
}

function httpGet(url){
	try{
		const xmlHttp = new XMLHttpRequest();
		xmlHttp.open("GET", url, false);
		xmlHttp.send(null);
		return JSON.parse(xmlHttp.responseText);
	}catch(err){
		console.error(err);
	}
}

function changeValues(totalValue, aptosValue){
	document.getElementById("total").children[0].innerText = totalValue;
	document.getElementById("aptos").children[0].innerText = aptosValue;
}

function getUrl(){
	return "/index.php/apps/assembly/api/v1/"
				+ window.location.href.split("assembly")[1];
}

function updateValuesByTheTime(){
	const response = httpGet(getUrl()).metadata;
	replaceResponses(httpGet(getUrl()).responses);
	changeValues(response.total, response.available);
}

setInterval( function(){
	updateValuesByTheTime()
}, 10000);
