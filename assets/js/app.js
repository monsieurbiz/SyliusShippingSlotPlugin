global.MonsieurBizShippingSlotManager = class {
  constructor(shippingMethodInputs, nextStepButtons, listSlotsUrl) {
    this.shippingMethodInputs = shippingMethodInputs;
    this.nextStepButtons = nextStepButtons;
    this.listSlotsUrl = listSlotsUrl;
    this.initShippingMethodInputs();
  }

  initShippingMethodInputs() {
    this.shippingMethodInputs.forEach;
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
        // Authorize user to go to next step if no slot needed
        if (typeof data.form_html === "undefined") {
          shippingSlotManager.enableButtons();
          return;
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
};
