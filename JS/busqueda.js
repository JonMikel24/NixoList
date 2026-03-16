async function searchMedia() {

    const query = document.getElementById("search").value;

    const response = await fetch("/api/buscarMedia.php?query=" + query);

    const data = await response.json();

    console.log(data);
}

const searchInput = document.getElementById("search");
const resultsDiv = document.getElementById("results");

searchInput.addEventListener("input", async function(){

    const query = this.value;

    if(query.length < 3) return;

    const res = await fetch("/api/buscarMedia.php?query="+query);

    const data = await res.json();

    renderResults(data);
});

function renderResults(data){

    resultsDiv.innerHTML = "";

    if(data.tmdb){

        data.tmdb.results.forEach(item=>{

            const div = document.createElement("div");

            const title = item.title || item.name;

            const poster = item.poster_path
            ? "https://image.tmdb.org/t/p/w200"+item.poster_path
            : "";

            div.innerHTML = `
                <img src="${poster}">
                <p>${title}</p>
            `;

            resultsDiv.appendChild(div);

        });
    }

}

div.addEventListener("click", async ()=>{

    await fetch("/api/guardarMedia.php",{
        method:"POST",
        headers:{
            "Content-Type":"application/json"
        },
        body:JSON.stringify({
            titulo:title,
            type:item.media_type,
            tmdb_id:item.id,
            portada:poster,
            descripcion:item.overview
        })
    });

});