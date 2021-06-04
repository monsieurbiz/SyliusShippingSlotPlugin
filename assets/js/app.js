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
    saveSlotUrl,
    resetSlotUrl,
    getSlotUrl,
    slotSelectError
  ) {
    this.shippingMethodInputs = shippingMethodInputs;
    this.nextStepButtons = nextStepButtons;
    this.calendarContainers = calendarContainers;
    this.fullCalendarConfig = fullCalendarConfig;
    this.slotStyle = slotStyle;
    this.selectedSlotStyle = selectedSlotStyle;
    this.listSlotsUrl = listSlotsUrl;
    this.saveSlotUrl = saveSlotUrl;
    this.resetSlotUrl = resetSlotUrl;
    this.getSlotUrl = getSlotUrl;
    this.slotSelectError = slotSelectError;
    this.previousSlot = null;
    this.initShippingMethodInputs();
  }

  initShippingMethodInputs() {
    for (let shippingMethodInput of this.shippingMethodInputs) {
      // On the page load, display load slots for selected method
      if (shippingMethodInput.checked) {
        this.displayInputSlots(shippingMethodInput, true);
      }
      this.initShippingMethodInput(shippingMethodInput);
    }
  }

  initShippingMethodInput(shippingMethodInput) {
    let shippingSlotManager = this;
    shippingMethodInput.addEventListener("change", function () {
      shippingSlotManager.changeShippingMethod(shippingMethodInput);
    });
  }

  changeShippingMethod(shippingMethodInput) {
    let shippingSlotManager = this;
    // Reset existing slot if needed
    this.resetSlot(shippingMethodInput, function () {
      // Display load slots for selected method
      shippingSlotManager.displayInputSlots(shippingMethodInput, false);
    });
  }

  displayInputSlots(shippingMethodInput, resetSlot) {
    this.disableButtons();
    let shippingSlotManager = this;
    this.listShippingSlotsForAMethod(shippingMethodInput.value, function () {
      if (this.status !== 200) {
        shippingSlotManager.enableButtons();
        return;
      }

      let data = JSON.parse(this.responseText);

      // Hide calendars
      shippingSlotManager.hideCalendars();

      // Authorize user to go to next step if no slot needed
      if (typeof data.rrules === "undefined") {
        if (resetSlot) {
          shippingSlotManager.resetSlot(shippingMethodInput, function () { shippingSlotManager.enableButtons() });
        } else {
          shippingSlotManager.enableButtons();
        }
        return;
      }

      let rules = new MonsieurBizShippingSlotRules(
        data.rrules,
        data.duration,
        data.startDate,
        data.unavailableDates
      );

      // Retrieve current slot and manage display
      shippingSlotManager.getSlot(
        shippingMethodInput.getAttribute("tabIndex"),
        function () {
          let currentSlot = null;
          if (this.status === 200) {
            let data = JSON.parse(this.responseText);
            if (
              typeof data.startDate !== "undefined" &&
              typeof data.duration !== "undefined"
            ) {
              currentSlot = new MonsieurBizShippingSlotSlot(
                data.startDate,
                data.duration
              );
            }

            // Init calendar
            for (let calendarContainer of shippingSlotManager.calendarContainers) {
              if (
                calendarContainer.classList.contains(shippingMethodInput.value)
              ) {
                shippingSlotManager.initCalendar(
                  calendarContainer,
                  rules,
                  currentSlot
                );
              }
            }
          }
        }
      );
    });
  }

  selectSlot(slot) {
    this.disableButtons();
    let shippingSlotManager = this;
    for (let shippingMethodInput of this.shippingMethodInputs) {
      if (shippingMethodInput.checked) {
        this.saveSlot(slot, shippingMethodInput, function () {
          if (this.status !== 200) {
            alert(shippingSlotManager.slotSelectError);
            return;
          }
          shippingSlotManager.enableButtons();
        });
      }
    }
  }

  listShippingSlotsForAMethod(shippingMethodCode, callback) {
    let req = new XMLHttpRequest();
    req.onload = callback;
    let url = this.listSlotsUrl;
    req.open("get", url.replace("__CODE__", shippingMethodCode), true);
    req.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    req.send();
  }

  saveSlot(slot, shippingMethodInput, callback) {
    let req = new XMLHttpRequest();
    req.onload = callback;
    req.open("post", this.saveSlotUrl, true);
    req.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    let data = new FormData();
    data.append("slot", JSON.stringify(slot));
    data.append("shippingMethod", shippingMethodInput.value);
    data.append("shipmentIndex", shippingMethodInput.getAttribute("tabIndex"));
    req.send(data);
  }

  resetSlot(shippingMethodInput, callback) {
    let req = new XMLHttpRequest();
    req.onload = callback;
    req.open("post", this.resetSlotUrl, true);
    req.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    let data = new FormData();
    data.append("shipmentIndex", shippingMethodInput.getAttribute("tabIndex"));
    req.send(data);
  }

  getSlot(shippingMethodIndex, callback) {
    let req = new XMLHttpRequest();
    req.onload = callback;
    let url = this.getSlotUrl;
    req.open(
      "get",
      url.replace("__INDEX__", Number.parseInt(shippingMethodIndex)),
      true
    );
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
    slot.el.querySelector(".fc-event-main").style.color =
      this.slotStyle.textColor;
    slot.el.style.borderColor = this.slotStyle.borderColor;
    slot.el.style.backgroundColor = this.slotStyle.backgroundColor;
  }

  applySelectedSlotStyle(slot) {
    slot.el.querySelector(".fc-event-main").style.color =
      this.selectedSlotStyle.textColor;
    slot.el.style.borderColor = this.selectedSlotStyle.borderColor;
    slot.el.style.backgroundColor = this.selectedSlotStyle.backgroundColor;
  }

  hideSlot(slot) {
    slot.el.style.display = 'none';
  }

  initCalendar(calendarContainer, rules, currentSlot) {
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
          headerToolbar: {
            left: "today prev,next",
            center: "title",
            right: "timeGridWeek,timeGridDay",
          },
          events: events,
          progressiveEventRendering: true,
          eventTextColor: this.slotStyle.textColor,
          eventBackgroundColor: this.slotStyle.backgroundColor,
          eventBorderColor: this.slotStyle.borderColor,
          eventClick: function (info) {
            // Apply slot selected display
            shippingSlotManager.applySelectedSlotStyle(info);

            // Remove old selected slot style if it's different of the current one
            if (
              shippingSlotManager.previousSlot !== null &&
              shippingSlotManager.previousSlot.event !== null &&
              shippingSlotManager.previousSlot.event.start.valueOf() !==
                info.event.start.valueOf()
            ) {
              shippingSlotManager.applySlotStyle(
                shippingSlotManager.previousSlot
              );
            }
            shippingSlotManager.previousSlot = info;

            // Save selected slot
            shippingSlotManager.selectSlot(info);
          },
          eventDidMount: function (info) {
            // Hide non available slots
            for (let unavailableDate of rules.getUnavailableDates()) {
              if (
                info.event !== null &&
                unavailableDate.valueOf() === info.event.start.valueOf()
              ) {
                shippingSlotManager.hideSlot(info);
              }
            }

            // Display selected the current slot
            if (
              info.event !== null &&
              currentSlot !== null &&
              currentSlot.getStartDate() !== null &&
              currentSlot.getStartDate().valueOf() ===
                info.event.start.valueOf()
            ) {
              shippingSlotManager.applySelectedSlotStyle(info);
              shippingSlotManager.enableButtons();
            }
          },
        },
        this.fullCalendarConfig // Merge and override config with the given one
      )
    );
    calendar.render();
  }
};

global.MonsieurBizShippingSlotRules = class {
  constructor(rrules, duration, startDate, unavailableDates) {
    this.rrules = rrules;
    this.duration = duration;
    this.startDate = startDate;
    this.unavailableDates = [];
    for (let unavailableDate of unavailableDates) {
      this.unavailableDates.push(new Date(unavailableDate));
    }
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

  getUnavailableDates() {
    return this.unavailableDates;
  }
};

global.MonsieurBizShippingSlotSlot = class {
  constructor(startDate, duration) {
    this.startDate = startDate;
    this.duration = duration;
  }

  getStartDate() {
    let date = new Date(this.startDate);
    return date;
  }

  getDuration() {
    return this.duration;
  }
};
