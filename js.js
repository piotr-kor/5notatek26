function kasowanie() {
    //To wyczyści tylko pierwszą notatkę - tu trzeba dokończyć
    fetch(`http://localhost/5notatek-v25/api.php?id=1`, {
            method: "PUT",
            headers: {
              "Content-Type": "application/json"
            },
            body: JSON.stringify({ content: "" })
          })
          .then(res => res.json())
          .then(resp => {
            console.log("Wykasowano:", resp);
          })
          .catch(err => console.error("Błąd zapisu:", err));
    document.getElementById("textarea1").value = "";
  }
  // 1. Pobierz notatki z API
  fetch("http://localhost/5notatek-v25/api.php")
    .then(res => res.json())
    .then(data => {
      const accordion = document.getElementById("accordionExample");
      accordion.innerHTML = "";

      data.forEach((note, index) => {
        const isFirst = index === 0 ? "show" : "";
        const collapseId = `collapse${note.id}`;
        const headerId = `heading${note.id}`;
        const textareaId = `textarea${note.id}`;

        accordion.innerHTML += `
          <div class="accordion-item">
            <h2 class="accordion-header" id="${headerId}">
              <button class="accordion-button ${isFirst ? "" : "collapsed"}"
                      type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#${collapseId}">
                Notatka ${note.id}
              </button>
            </h2>
            <div id="${collapseId}"
                 class="accordion-collapse collapse ${isFirst}"
                 data-bs-parent="#accordionExample">
              <div class="accordion-body">
                <textarea id="${textareaId}" class="form-control" rows="4">${note.content || ""}</textarea>
              </div>
            </div>
          </div>
        `;
      });

      // 2. zdarzenie zwijania (collapse.bs.collapse)
      const collapses = accordion.querySelectorAll(".accordion-collapse");
      collapses.forEach(collapse => {
        collapse.addEventListener("hide.bs.collapse", () => {
          const noteId = collapse.id.replace("collapse", ""); // np. "5"
          const textarea = document.getElementById(`textarea${noteId}`);
          const newContent = textarea.value;

          // Trochę teorii do kodu poniżej
          // Promise to obiekt, który reprezentuje przyszłą wartość.
          // Ma trzy stany:
          //  - pending (oczekujący, np. kawa się robi),
          //  - fulfilled (zrealizowany, np. kawa gotowa),
          //  - rejected (odrzucony, np. ekspres padł).
          //
          // fetch(...) zwraca Promise z odpowiedzią (Response).
          // Pierwszy .then(...) odbiera ten Response i wywołuje response.json(), które zwraca następny Promise.
          // Drugi .then(...) dostaje już gotowe dane (zdeserializowany JSON).
          // .catch(...) złapie dowolny błąd, który pojawił się w którymkolwiek wcześniejszym then lub fetch
          //
          // Tworzymy tu łańcuch z .then(...) – każdy dostaje wynik z poprzedniego. To tzw. chainowanie Promise’ów.
          // .catch(...) to taki „bezpiecznik” na końcu całego łańcucha.
          
          fetch(`http://localhost/5notatek-v25/api.php?id=${noteId}`, {
            method: "PUT",
            headers: {
              "Content-Type": "application/json"
            },
            body: JSON.stringify({ content: newContent })
          })
          .then(res => res.json())
          .then(resp => {
            console.log("Zapisano:", resp);
          })
          .catch(err => console.error("Błąd zapisu:", err));
        });
      });
    });

async function exportuj(format) {
      try {
        // pobranie danych z API
        const response = await fetch("http://localhost/5notatek-v25/api.php");
        const data = await response.json();

        let zawartosc = "";
        let nazwaPliku = "notatki." + format;

        if (format === "json") {
          zawartosc = JSON.stringify(data, null, 2);
        } 
        else if (format === "csv") {
          const naglowki = Object.keys(data[0]).join(",");
          const wiersze = data.map(item => Object.values(item).map(v => `"${v}"`).join(",")).join("\n");
          zawartosc = naglowki + "\n" + wiersze;
        } 
        else if (format === "txt") {
          zawartosc = data.map(item => `Notatka ${item.id} - ${item.content}`).join("\n");
        } 
        else {
          alert("Nieobsługiwany format!");
          return;
        }

        // tworzenie pliku do pobrania
        const blob = new Blob([zawartosc], { type: "text/plain;charset=utf-8" });
        const url = URL.createObjectURL(blob);

        // tworzymy "niewidoczny" link do pobrania, klikamy w niego i usówamy link
        const a = document.createElement("a"); 
        a.href = url;
        a.download = nazwaPliku;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);

      } catch (error) {
        console.error("Błąd podczas eksportu:", error);
      }
    }
function changeBtnText() {
    const btn = document.getElementById('exe-btn');
    btn.textContent = btn.textContent === "Eksport EXE" ? "Brak wirusa..." : "...a mógł być!";
}