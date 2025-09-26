function kasowanie() {
    alert("Funkcja kasowanie() została uruchomiona!");
    
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

      // 2. Obsłuż zdarzenie zwijania (collapse.bs.collapse)
      const collapses = accordion.querySelectorAll(".accordion-collapse");
      collapses.forEach(collapse => {
        collapse.addEventListener("hide.bs.collapse", () => {
          const noteId = collapse.id.replace("collapse", ""); // np. "5"
          const textarea = document.getElementById(`textarea${noteId}`);
          const newContent = textarea.value;


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