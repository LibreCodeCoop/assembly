const timeForUpdate = 5000;
/** http://yourdomain.com.br/ */
const mainDomain = "localhost";

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

function getUrl(domain){
	return "/index.php/apps/assembly/api/v1/"
				+ window.location.href.split("assembly")[1];
}

function updateValuesByTheTime(domain){
	const response = httpGet(getUrl(domain)).metadata;
	replaceResponses(httpGet(getUrl(domain)).responses);
	changeValues(response.total, response.available);
}

setInterval( function(){
	updateValuesByTheTime(mainDomain)
}, timeForUpdate);
