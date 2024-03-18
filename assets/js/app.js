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
    slotSelectError,
    shippingSlotConfigSelects,
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
    this.shippingSlotConfigSelects = shippingSlotConfigSelects;
    this.previousSlot = null;
    this.initShippingMethodInputs();
  }

  initShippingMethodInputs() {
    for (let shippingMethodInput of this.shippingMethodInputs) {
      // On the page load, display load slots for selected method
      if (shippingMethodInput.checked) {
        let shippingSlotConfigSelect = this.getShippingSlotConfigSelect(shippingMethodInput.value);
        this.displayInputSlots(shippingMethodInput, true, shippingSlotConfigSelect);
      }
      this.initShippingMethodInput(shippingMethodInput);
    }

    this.shippingSlotConfigSelects.forEach(shippingSlotConfigSelect => {
      shippingSlotConfigSelect.addEventListener("change", function () {
        let checkedShippingMethodInput = Array.from(this.shippingMethodInputs).find(shippingMethodInput => shippingMethodInput.checked);
        if (checkedShippingMethodInput !== null) {
          this.displayInputSlots(checkedShippingMethodInput, false, shippingSlotConfigSelect);
        }
        const event = new CustomEvent('mbiz:shipping-slot:slot-config-selected', {
          element: shippingSlotConfigSelect,
          shippingMethodInput: checkedShippingMethodInput
        });
        document.dispatchEvent(event);
      }.bind(this));
    }, this);
  }

  initShippingMethodInput(shippingMethodInput) {
    let shippingSlotManager = this;
    shippingMethodInput.addEventListener("change", function () {
      shippingSlotManager.changeShippingMethod(shippingMethodInput);
    });
  }

  changeShippingMethod(shippingMethodInput) {
    let shippingSlotManager = this;
    // Find selected shipping slot config select
    let shippingSlotConfigSelect = this.getShippingSlotConfigSelect(shippingMethodInput.value);

    // Reset existing slot if needed
    this.resetSlot(shippingMethodInput, function () {
      // Display load slots for selected method
      shippingSlotManager.displayInputSlots(shippingMethodInput, false, shippingSlotConfigSelect);
    });
  }

  displayInputSlots(shippingMethodInput, resetSlot, shippingSlotConfigSelect = null) {
    this.disableButtons();
    let shippingSlotManager = this;
    this.initCalendarForAMethod(shippingMethodInput.value, shippingSlotConfigSelect, function () {
      if (this.status !== 200) {
        shippingSlotManager.enableButtons();
        return;
      }

      let data = JSON.parse(this.responseText);

      // Hide calendars and shipping slot config selects
      shippingSlotManager.hideCalendars();
      shippingSlotManager.hideShippingSlotConfigSelects();

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
            shippingMethodInput.value,
            shippingSlotConfigSelect
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

  initCalendarForAMethod(shippingMethodCode, shippingSlotConfigSelect, callback) {
    let req = new XMLHttpRequest();
    req.onload = callback;
    let url = this.initUrl
      .replace("__CODE__", shippingMethodCode)
      .replace("__CONFIG__", shippingSlotConfigSelect !== null ? shippingSlotConfigSelect.value : "")
    ;
    req.open("get", url, true);
    req.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    req.send();
  }

  listSlots(shippingMethodCode, from, to, shippingSlotConfigSelect, callback) {
    let req = new XMLHttpRequest();
    req.onload = callback;
    let url = this.listSlotsUrl
      .replace("__CODE__", shippingMethodCode)
      .replace("__FROM__", from)
      .replace("__TO__", to)
      .replace("__CONFIG__", shippingSlotConfigSelect !== null ? shippingSlotConfigSelect.value : "")
    ;
    req.open("get", url, true);
    req.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    req.send();
  }

  saveSlot(slot, shippingMethodInput, callback) {
    let shippingSlotConfigSelect = this.getShippingSlotConfigSelect(shippingMethodInput.value);
    let req = new XMLHttpRequest();
    req.onload = callback;
    req.open("post", this.saveSlotUrl, true);
    req.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    let data = new FormData();
    data.append("event", JSON.stringify(slot.event));
    data.append("shippingMethod", shippingMethodInput.value);
    data.append("shipmentIndex", shippingMethodInput.getAttribute("tabIndex"));
    data.append("shippingSlotConfig", shippingSlotConfigSelect !== null ? shippingSlotConfigSelect.value : '');
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
      button.form.classList.add('loading');
    }
  }

  enableButtons() {
    for (let button of this.nextStepButtons) {
      button.disabled = false;
      button.form.classList.remove('loading');
    }
  }

  hideCalendars() {
    for (let calendarContariner of this.calendarContainers) {
      calendarContariner.style.display = "none";
    }
  }

  hideShippingSlotConfigSelects() {
    for (let shippingSlotConfigSelect of this.shippingSlotConfigSelects) {
      shippingSlotConfigSelect.style.display = "none";
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

  initCalendar(calendarContainer, events, timezone, shippingMethodCode, shippingSlotConfigSelect) {
    calendarContainer.style.display = "block";
    if (shippingSlotConfigSelect) {
      shippingSlotConfigSelect.style.display = "block";
    }
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
            shippingSlotManager.disableButtons();
            shippingSlotManager.listSlots(shippingMethodCode, dateInfo.startStr, dateInfo.endStr, shippingSlotConfigSelect, function () {
              if (this.status !== 200) {
                console.error('Error during slot list');
                return;
              }

              // Remove loading class on the form
              for (let button of shippingSlotManager.nextStepButtons) {
                button.form.classList.remove('loading');
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

  getShippingSlotConfigSelect(shippingMethodCode) {
    return Array.from(this.shippingSlotConfigSelects).find(
      shippingSlotConfigSelect => shippingSlotConfigSelect.name.includes(shippingMethodCode)
    ) ?? null;
  }
};
