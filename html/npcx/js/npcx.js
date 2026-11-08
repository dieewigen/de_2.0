function updateNpcUI(data) {
    document.getElementById("target_npc_accounts").innerHTML = ""; // Tabelle leeren

    const dataList = document.getElementById("target_npc_accounts");
    data.forEach(item => {
        let div = document.createElement("div");
        div.textContent = `ID: ${item.id}, Name: ${item.name}, Sektor: ${item.sector}, System: ${item.system}, Punkte: ${item.points}, Kollektoren: ${item.cols}`;
        dataList.appendChild(div);
        //Linkliste
        div = document.createElement("div");
        div.innerHTML = `<span onclick="openPage(${item.id}, 'resource.php')">Ressourcen</span>`;
        div.innerHTML+= ` - <span onclick="openPage(${item.id}, 'resource.php', {b_col: 1})">baue einen Kollektor</span>`;
        div.innerHTML+= ` - <span onclick="openPage(${item.id}, 'sector.php')">Sektor</span>`;
        div.innerHTML+= ` - <span onclick="openPage(${item.id}, 'military.php')">Militär</span>`;
        div.innerHTML+= ` - <span onclick="getUserFleet(${item.id})">Flotten</span>`;
        div.innerHTML+= ` - <span onclick="getSectorStatus(${item.id})">Sektorstatus</span>`;
        div.innerHTML+= ` - <span onclick="openPage(${item.id}, 'missions.php')">Missionen</span>`;
        div.innerHTML+= ` - <span onclick="openPage(${item.id}, 'production.php')">Produktion</span>`;
        div.innerHTML+= ` - <span onclick="openPage(${item.id}, 'hyperfunk.php')">Hyperfunk</span><br><br>`;
        dataList.appendChild(div);

    });    
}

function updateGameOutputUI(data) {
    document.getElementById("target_gameoutput").innerHTML = data; // Tabelle leeren
}

async function getAllNpcUsers() {
    const response = await fetch('../api/', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-DE-API-KEY': env_api_key // API Key
        },
        body: JSON.stringify({ action: 'getAllNpcUsers' })
    });
    updateNpcUI(await response.json());
    //return await response.json();
}

async function getUserFleet(userId) {
    const response = await fetch('../api/', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-DE-API-KEY': env_api_key // API Key
        },
        body: JSON.stringify({ action: 'getUserFleet', user_id: userId })
    });

    const json = await response.json();

    document.getElementById("target_json_output").innerText = JSON.stringify(json, null, 2);
}

async function getSectorStatus(userId) {
    const response = await fetch('../api/', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-DE-API-KEY': env_api_key // API Key
        },
        body: JSON.stringify({ action: 'getSectorStatus', user_id: userId })
    });

    const json = await response.json();

    document.getElementById("target_json_output").innerText = JSON.stringify(json, null, 2);
}

async function getServerData() {
    const response = await fetch('../api/', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-DE-API-KEY': env_api_key // API Key
        },
        body: JSON.stringify({ action: 'getServerData'})
    });

    const json = await response.json();

    document.getElementById("target_json_output").innerText = JSON.stringify(json, null, 2);
}

async function openPage(user_id, filename, requestData=Array()) {
    const response = await fetch('../api/', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-DE-API-KEY': env_api_key // API Key
        },
        body: JSON.stringify({ action: 'openPage', user_id: user_id, filename: filename, requestData: requestData })
    })
    .then(res => res.text())
    .then(html => {
        const blob = new Blob([html], { type: 'text/html' });
        const url = URL.createObjectURL(blob);
        document.getElementById('target_gameoutput').src = url;
    });
}

/*
// Hauptschleife
async function mainLoop() {
  try {
    const data = await getAllNpcUsers();

  } catch (err) {
    console.error("Fehler im Loop:", err);
  }
}

// Alle 3 Sekunden wiederholen
setInterval(mainLoop, 3000);

// Optional: sofort starten
mainLoop();
*/

getAllNpcUsers();