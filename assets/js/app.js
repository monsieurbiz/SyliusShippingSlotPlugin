import { Calendar } from '@fullcalendar/core';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import allLocales from "@fullcalendar/core/locales-all";
import momentTimezonePlugin from '@fullcalendar/moment-timezone';

global.MonsieurBizShippingSlotManager = class {
  constructor(
    shippingMethodInputs,
    nextStepButtons,
    calendarContainers,
    fullCalendarConfig,
    gridSlotStyle,
    selectedGridSlotStyle,
    listSlotStyle,
    selectedListSlotStyle,
    initUrl,
    listSlotsUrl,
    saveSlotUrl,
    resetSlotUrl,
    slotSelectError
  ) {
    this.shippingMethodInputs = shippingMethodInputs;
    this.nextStepButtons = nextStepButtons;
    this.calendarContainers = calendarContainers;
    this.fullCalendarConfig = fullCalendarConfig;
    this.gridSlotStyle = gridSlotStyle;
    this.selectedGridSlotStyle = selectedGridSlotStyle;
    this.listSlotStyle = listSlotStyle;
    this.selectedListSlotStyle = selectedListSlotStyle;
    this.initUrl = initUrl;
    this.listSlotsUrl = listSlotsUrl;
    this.saveSlotUrl = saveSlotUrl;
    this.resetSlotUrl = resetSlotUrl;
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
    this.initCalendarForAMethod(shippingMethodInput.value, function () {
      if (this.status !== 200) {
        shippingSlotManager.enableButtons();
        return;
      }

      let data = JSON.parse(this.responseText);

      // Hide calendars
      shippingSlotManager.hideCalendars();

      // Authorize user to go to next step if no slot needed
      if (typeof data.events === "undefined") {
        if (resetSlot) {
          shippingSlotManager.resetSlot(shippingMethodInput, function () { shippingSlotManager.enableButtons() });
        } else {
          shippingSlotManager.enableButtons();
        }
        return;
      }

      // Init calendar
      for (let calendarContainer of shippingSlotManager.calendarContainers) {
        if (
          calendarContainer.classList.contains(shippingMethodInput.value)
        ) {
          shippingSlotManager.initCalendar(
            calendarContainer,
            data.events,
            data.timezone,
            shippingMethodInput.value
          );
        }
      }
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

  initCalendarForAMethod(shippingMethodCode, callback) {
    let req = new XMLHttpRequest();
    req.onload = callback;
    let url = this.initUrl;
    req.open("get", url.replace("__CODE__", shippingMethodCode), true);
    req.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    req.send();
  }

  listSlots(shippingMethodCode, from, to, callback) {
    let req = new XMLHttpRequest();
    req.onload = callback;
    let url = this.listSlotsUrl
      .replace("__CODE__", shippingMethodCode)
      .replace("__FROM__", from)
      .replace("__TO__", to)
    ;
    req.open("get", url, true);
    req.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    req.send();
  }

  saveSlot(slot, shippingMethodInput, callback) {
    let req = new XMLHttpRequest();
    req.onload = callback;
    req.open("post", this.saveSlotUrl, true);
    req.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    let data = new FormData();
    data.append("event", JSON.stringify(slot.event));
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
    if (slot.el.querySelector(".fc-event-main") !== null) {
      // Timegrid view
      slot.el.querySelector(".fc-event-main").style.color =
        this.gridSlotStyle.textColor;
      slot.el.style.borderColor = this.gridSlotStyle.borderColor;
      slot.el.style.backgroundColor = this.gridSlotStyle.backgroundColor;
    } else if (slot.el.querySelector(".fc-list-event-time") !== null) {
      // List view
      slot.el.querySelector(".fc-list-event-time").style.color =
        this.listSlotStyle.textColor;
      slot.el.style.borderColor = this.listSlotStyle.borderColor;
      slot.el.style.backgroundColor = this.listSlotStyle.backgroundColor;
    }
  }

  applySelectedSlotStyle(slot) {
    if (slot.el.querySelector(".fc-event-main") !== null) {
      // Timegrid view
      slot.el.querySelector(".fc-event-main").style.color =
        this.selectedGridSlotStyle.textColor;
      slot.el.style.borderColor = this.selectedGridSlotStyle.borderColor;
      slot.el.style.backgroundColor = this.selectedGridSlotStyle.backgroundColor;
    } else if (slot.el.querySelector(".fc-list-event-time") !== null) {
      // List view
      slot.el.querySelector(".fc-list-event-time").style.color =
        this.selectedListSlotStyle.textColor;
      slot.el.style.borderColor = this.selectedListSlotStyle.borderColor;
      slot.el.style.backgroundColor = this.selectedListSlotStyle.backgroundColor;
    }
  }

  hideSlot(slot) {
    slot.el.style.display = 'none';
  }

  initCalendar(calendarContainer, events, timezone, shippingMethodCode) {
    calendarContainer.style.display = "block";
    let shippingSlotManager = this;
    let calendar = new Calendar(
      calendarContainer,
      Object.assign(
        {
          timeZone: timezone,
          plugins: [timeGridPlugin, listPlugin, momentTimezonePlugin],
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
          eventTextColor: this.gridSlotStyle.textColor,
          eventBackgroundColor: this.gridSlotStyle.backgroundColor,
          eventBorderColor: this.gridSlotStyle.borderColor,
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
            // Display selected the current slot
            if (
              info.event !== null &&
              info.event.extendedProps.isCurrent === true
            ) {
              shippingSlotManager.applySelectedSlotStyle(info);
              shippingSlotManager.previousSlot = info;
              shippingSlotManager.enableButtons();
            } else {
              shippingSlotManager.applySlotStyle(info);
            }
          },
          datesSet(dateInfo) {
            let calendar = this;
            shippingSlotManager.listSlots(shippingMethodCode, dateInfo.startStr, dateInfo.endStr, function () {
              if (this.status !== 200) {
                console.error('Error during slot list');
                return;
              }

              let events = JSON.parse(this.responseText);
              // Use batch rendering to improve events loading
              calendar.batchRendering(function() {
                for (const event of calendar.getEvents()) {
                  event.remove();
                }
                for (const event of events) {
                  calendar.addEvent(event);
                }
              });
            });
          },
        },
        this.fullCalendarConfig // Merge and override config with the given one
      )
    );
    calendar.render();
  }
};
