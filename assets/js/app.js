import { Calendar } from "@fullcalendar/core";
import timeGridPlugin from "@fullcalendar/timegrid";
import allLocales from "@fullcalendar/core/locales-all";

import '@fullcalendar/timegrid/main.css';

global.MonsieurBizShippingSlotManager = class {
  constructor(
    shippingMethodInputs,
    nextStepButtons,
    calendarContainers,
    listSlotsUrl
  ) {
    this.shippingMethodInputs = shippingMethodInputs;
    this.nextStepButtons = nextStepButtons;
    this.listSlotsUrl = listSlotsUrl;
    this.calendarContainers = calendarContainers;
    this.initShippingMethodInputs();
  }

  initShippingMethodInputs() {
    for (let shippingMethodInput of this.shippingMethodInputs) {
      // On the page load, display load slots for selected method
      if (shippingMethodInput.checked) {
        this.displayInputSlots(shippingMethodInput);
      }
      this.initShippingMethodInput(shippingMethodInput);
    }
  }

  initShippingMethodInput(shippingMethodInput) {
    let shippingSlotManager = this;
    shippingMethodInput.addEventListener("change", function () {
      // On shipping method change, display load slots for selected method
      shippingSlotManager.displayInputSlots(shippingMethodInput);
    });
  }

  displayInputSlots(shippingMethodInput) {
    this.disableButtons();
    let shippingSlotManager = this;
    this.listShippingSlotsForAMethod(shippingMethodInput.value, function () {
      // this = req
      if (this.status === 200) {
        let data = JSON.parse(this.responseText);
        shippingSlotManager.hideCalendars();
        // Authorize user to go to next step if no slot needed
        if (typeof data.form_html === "undefined") {
          shippingSlotManager.enableButtons();
          return;
        }

        for (let calendarContainer of shippingSlotManager.calendarContainers) {
          if (calendarContainer.classList.contains(shippingMethodInput.value)) {
            shippingSlotManager.initCalendar(calendarContainer);
          }
        }

        // Display form
        console.log(data);
      } else {
        shippingSlotManager.enableButtons();
      }
    });
  }

  listShippingSlotsForAMethod(shippingMethodCode, callback) {
    let req = new XMLHttpRequest();
    req.onload = callback;
    let url = this.listSlotsUrl;
    req.open("get", url.replace("__CODE__", shippingMethodCode), true);
    req.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    req.send();
  }

  disableButtons() {
    for (let button of this.nextStepButtons) {
      button.disabled = true;
    }
  }

  enableButtons() {
    for (let button of this.nextStepButtons) {
      button.disabled = false;
    }
  }

  hideCalendars() {
    for (let calendarContariner of this.calendarContainers) {
      calendarContariner.style.display = "none";
    }
  }

  initCalendar(calendarContainer) {
    calendarContainer.style.display = "block";
    let calendar = new Calendar(calendarContainer, {
      plugins: [timeGridPlugin],
      initialView: "timeGridWeek",
      contentHeight: "auto",
      slotMinTime: "06:00:00",
      slotMaxTime: "22:00:00",
      locales: allLocales,
      locale: "fr",
      firstDay: 1,
      allDaySlot: false,
      selectable: true,
      headerToolbar: {
        left: 'today prev,next',
        center: 'title',
        right: 'timeGridWeek,timeGridDay',
      },
      events:  [
        {
            start: '2021-05-27T10:30:00',
            end: '2021-05-27T11:30:00',
        }
      ],
      eventClick: function (info) {
        console.log(info.event);
      }
    });
    calendar.render();
  }
};
