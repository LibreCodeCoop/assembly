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

function changeUrlMeet(url){
	document.getElementById("btn-meet-link").setAttribute("href", url);
}

function loadNewForms(responses){
	const content = document.getElementById("grid-content-forms");
	content.innerHTML = "";
	if (responses.length == 0) {
		content.innerHTML = '<div class="explore-content"><h3>Nenhuma votação aberta ainda</h3></div>';
	} else {
		responses.forEach((arr)=>{
			// if (arr.submission) {
			// 	content.innerHTML = '<div class="explore-content"> <div class="explore-value"><div class="explore-subscribe">Voto registrado</div>'
			// } else {
				content.innerHTML = '<div class="explore-content"> <div class="explore-value"><div class="explore-subscribe"><a class="button" href="'
				+ arr.vote_url +'" target="_blank">'
				+ arr.title+'</a></div></div></div>' + content.innerHTML;
			// }
		});
	}
	return;
}

function loadaNewResults(response){
	const content = document.getElementById("grid-content-result");
	content.innerHTML = "";
	if (response.length == 0) {
		content.innerHTML = '<div class="explore-content"><h3>Nenhuma votação aberta ainda</h3></div>';
	} else {
		response.forEach(arr => {
			// if (arr.submission) {
				content.innerHTML = '<div class="explore-content"><div class="explore-value"><div class="explore-subscribe"><a class="button" href="'
										+ arr.result_url + '" target="_blank">'
										+ arr.title + '</a></div></div></div>' + content.innerHTML;
			// } else {
			// 	content.innerHTML = '<div class="explore-content"> <div class="explore-value"><div class="explore-subscribe">Vote para visualizar a apuração</div>'
			// }
		});
	}
}

function changeData(response){
	loadNewForms(response.data);
	loadaNewResults(response.data);
	changeUrlMeet(response.meetUrl);
}

const url = "/index.php/apps/assembly/api/v1/dashboard";
setInterval( function(){
	const response = httpGet(url);
	changeData(response);
}, 5000);
