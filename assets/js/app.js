import { Calendar } from "@fullcalendar/core";
import timeGridPlugin from "@fullcalendar/timegrid";
import listPlugin from "@fullcalendar/list";
import allLocales from "@fullcalendar/core/locales-all";
import rrulePlugin from "@fullcalendar/rrule";

import "@fullcalendar/timegrid/main.css";

global.MonsieurBizShippingSlotManager = class {
  constructor(
    shippingMethodInputs,
    nextStepButtons,
    calendarContainers,
    fullCalendarConfig,
    slotStyle,
    selectedSlotStyle,
    listSlotsUrl,
  ) {
    this.shippingMethodInputs = shippingMethodInputs;
    this.nextStepButtons = nextStepButtons;
    this.calendarContainers = calendarContainers;
    this.fullCalendarConfig = fullCalendarConfig;
    this.slotStyle = slotStyle;
    this.selectedSlotStyle = selectedSlotStyle;
    this.listSlotsUrl = listSlotsUrl;
    this.previousSlot = null;
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
      if (this.status !== 200) {
        shippingSlotManager.enableButtons();
        return;
      }

      let data = JSON.parse(this.responseText);
      let rules = new MonsieurBizShippingSlotRules(
        data.rrules,
        data.duration,
        data.startDate
      );

      // Hide calendars
      shippingSlotManager.hideCalendars();

      // Authorize user to go to next step if no slot needed
      if (typeof data.rrules === "undefined") {
        shippingSlotManager.enableButtons();
        return;
      }

      // Init calendar
      for (let calendarContainer of shippingSlotManager.calendarContainers) {
        if (calendarContainer.classList.contains(shippingMethodInput.value)) {
          shippingSlotManager.initCalendar(calendarContainer, rules);
        }
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

  applySlotStyle(slot) {
    slot.el.querySelector('.fc-event-main').style.color = this.slotStyle.textColor;
    slot.el.style.borderColor = this.slotStyle.borderColor;
    slot.el.style.backgroundColor = this.slotStyle.backgroundColor;
  }

  applySelectedSlotStyle(slot) {
    slot.el.querySelector('.fc-event-main').style.color = this.selectedSlotStyle.textColor;
    slot.el.style.borderColor = this.selectedSlotStyle.borderColor;
    slot.el.style.backgroundColor = this.selectedSlotStyle.backgroundColor;
  }

  initCalendar(calendarContainer, rules) {
    calendarContainer.style.display = "block";
    let events = [];
    for (let rrule of rules.getRrules()) {
      events.push({
        rrule: "DTSTART:" + rules.getStartDate() + "\n" + rrule,
        duration: rules.getDuration(),
      });
    }
    let shippingSlotManager = this;
    let calendar = new Calendar(
      calendarContainer,
      Object.assign(
        {
          plugins: [timeGridPlugin, listPlugin, rrulePlugin],
          locales: allLocales,
          initialView: "timeGridWeek",
          contentHeight: "auto",
          allDaySlot: false,
          slotDuration: "00:30",
          headerToolbar: {
            left: "today prev,next",
            center: "title",
            right: "timeGridWeek,timeGridDay,listDay",
          },
          events: events,
          progressiveEventRendering: true,
          eventTextColor: this.slotStyle.textColor,
          eventBackgroundColor: this.slotStyle.backgroundColor,
          eventBorderColor: this.slotStyle.borderColor,
          eventClick: function (info) {
            shippingSlotManager.applySelectedSlotStyle(info);
            if (shippingSlotManager.previousSlot !== null) {
              shippingSlotManager.applySlotStyle(shippingSlotManager.previousSlot);
            }
            shippingSlotManager.previousSlot = info;
          },
          eventContent: function (info) {
            // Will be used to hide non available slots
            // info.event.setProp('display', 'none');
          }
        },
        this.fullCalendarConfig // Merge and override config with the given one
      )
    );
    calendar.render();
  }
};

global.MonsieurBizShippingSlotRules = class {
  constructor(rrules, duration, startDate) {
    this.rrules = rrules;
    this.duration = duration;
    this.startDate = startDate;
  }

  getRrules() {
    return this.rrules;
  }

  /**
   * Return duration on format HH:mm (example : `02:00`)
   */
  getDuration() {
    let duration = this.duration;
    let hours = parseInt(this.duration / 60, 10);
    let minutes = duration - hours * 60;
    return (
      String(hours).padStart(2, "0") + ":" + String(minutes).padStart(2, "0")
    );
  }

  /**
   * Return duration on format YYYYMMDDTHiSZ (example : `20210101T113000Z`)
   */
  getStartDate() {
    let date = new Date(this.startDate);
    let year = date.getFullYear();
    let month = String(date.getMonth() + 1).padStart(2, "0"); // Month is from 0 to 11
    let day = String(date.getDate()).padStart(2, "0");
    let hours = String(date.getHours()).padStart(2, "0");
    let minutes = String(date.getMinutes()).padStart(2, "0");
    let seconds = String(date.getSeconds()).padStart(2, "0");
    return `${year}${month}${day}T${hours}${minutes}${seconds}Z`;
  }

  formatstar;
};
