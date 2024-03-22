$(document).ready(function() {
  const daysTag = $("ul.jours");
  const currentDateElement = $(".date_actuelle");
  const prevNextIcon = $(".icones span");
  const redirectionBtn = $("#redirection-btn");

  let date = new Date();
  let currYear = date.getFullYear();
  let currMonth = date.getMonth();
  let selectedDate;

  const months = [
    "Janvier",
    "Février",
    "Mars",
    "Avril",
    "Mai",
    "Juin",
    "Juillet",
    "Août",
    "Septembre",
    "Octobre",
    "Novembre",
    "Décembre",
  ];

  const renderCalendar = () => {
    let firstDayofMonth = new Date(currYear, currMonth, 1).getDay(),
      lastDateofMonth = new Date(currYear, currMonth + 1, 0).getDate(),
      lastDayofMonth = new Date(currYear, currMonth, lastDateofMonth).getDay(),
      lastDateofLastMonth = new Date(currYear, currMonth, 0).getDate();
    let liTag = "";

    if (firstDayofMonth === 0) {
      firstDayofMonth = 6;
    } else {
      firstDayofMonth--;
    }

    for (let i = firstDayofMonth; i > 0; i--) {
      liTag += `<li class="inactive">${lastDateofLastMonth - i + 1}</li>`;
    }

    for (let i = 1; i <= lastDateofMonth; i++) {
      let isToday =
        selectedDate &&
        i === selectedDate.getDate() &&
        currMonth === selectedDate.getMonth() &&
        currYear === selectedDate.getFullYear()
          ? "active"
          : "";

      let dayOfWeek = new Date(currYear, currMonth, i).getDay();

      if (dayOfWeek === 1 || dayOfWeek === 3 || dayOfWeek === 5) {
        liTag += `<li class="inactive">${i}</li>`;
      } else if (dayOfWeek === 6 || dayOfWeek === 0) {
        liTag += `<li class="weekend inactive">${i}</li>`;
      } else {
        const currentDate = new Date();
        if (currentDate <= new Date(currYear, currMonth, i)) {
          liTag += `<li class="${isToday}">${i}</li>`;
        } else {
          liTag += `<li class="inactive">${i}</li>`;
        }
      }
    }

    for (let i = lastDayofMonth; i < 6; i++) {
      liTag += `<li class="inactive">${i - lastDayofMonth + 1}</li>`;
    }
    currentDateElement.text(`${months[currMonth]} ${currYear}`);
    daysTag.html(liTag);

    daysTag.find("li").not(".inactive").on("click", function() {
      const day = $(this).text();
      const clickedElement = $(this); 
      selectDate(parseInt(day), clickedElement);
    });
  };

  const selectDate = (day, element) => {
    const currentDate = new Date();
    if (currentDate <= new Date(currYear, currMonth, day )) {
      daysTag.find("li").removeClass("active");
      element.addClass("active");
      selectedDate = new Date(currYear, currMonth, day);
      enableRedirectionButton();
    } else {
      disableRedirectionButton();
    }
  };

  const enableRedirectionButton = () => {
    redirectionBtn.prop("disabled", false).addClass("bouton");
  };

  const disableRedirectionButton = () => {
    redirectionBtn.prop("disabled", true).removeClass("bouton");
  };

  const goToPreviousMonth = () => {
    currMonth -= 1;
    if (currMonth < 0) {
      currMonth = 11;
      currYear--;
    }
    date = new Date(currYear, currMonth, new Date().getDate());
    renderCalendar();
    disableRedirectionButton();
  };
  
  const goToNextMonth = () => {
    currMonth += 1;
  
    if (currMonth > 11) {
      currMonth = 0;
      currYear++;
    }
    date = new Date(currYear, currMonth, new Date().getDate());
    renderCalendar();
    disableRedirectionButton();
  };
  
  const initializeCalendar = () => {
    const currentDate = new Date();
    selectedDate = currentDate;
    currYear = currentDate.getFullYear();
    currMonth = currentDate.getMonth();
    renderCalendar();
  };
  
  initializeCalendar();
  
  $("#prev").on("click", function() {
    goToPreviousMonth();
  });
  
  $("#next").on("click", function() {
    goToNextMonth();
  });
  
  redirectionBtn.on("click", function() {
    const formattedDate = selectedDate.toLocaleDateString();
    const url = `page_redirection.php?date=${formattedDate}&service=${encodeURIComponent(service)}&lieu=${encodeURIComponent(lieu)}`;
    window.location.href = url;
  });
  
  
});
