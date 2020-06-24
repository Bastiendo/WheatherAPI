function maPosition(position) {
    var infopos = "Position déterminée :\n";
    infopos += "Latitude : "+position.coords.latitude +"\n";
    infopos += "Longitude: "+position.coords.longitude+"\n";
    infopos += "Altitude : "+position.coords.altitude +"\n";
    document.getElementById("form_lon").value = position.coords.longitude;
    document.getElementById("form_lat").value = position.coords.latitude;
  }

function checkInput(event) {
    console.log("* ** ***     check input *** ** *");
    isCheck = document.querySelector("#form_position").checked;
    console.log("is check " + isCheck);
    if(isCheck) {
        if(navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(maPosition);
            //document.getElementById("form_lon").className = "";
            //document.getElementById("form_lat").className = "";
            document.getElementById("form_ville").required = false;
        }
        
    }
    else {
        document.getElementById("form_lon").className = "d-none";
        document.getElementById("form_lat").className = "d-none";
        document.getElementById("form_ville").required = true;
    }
}

document.querySelector("#form_position").addEventListener("change", checkInput);
checkInput();