global.MonsieurBizShippingSlotManager = class {
    constructor(
        shippingMethodInputs,
        listSlotUrl
    ) {
        this.shippingMethodInputs = shippingMethodInputs;
        this.listSlotUrl = listSlotUrl;
        this.initShippingMethodInputs();
    }

    initShippingMethodInputs() { 
        this.shippingMethodInputs.forEach
        for (let shippingMethodInput of this.shippingMethodInputs) {
            // On the page load, display load slots for selected method
            if (shippingMethodInput.checked) {
                this.listShippingSlotsForAMethod(shippingMethodInput);
            }
            this.initShippingMethodInput(shippingMethodInput);
        }
    }

    initShippingMethodInput(shippingMethodInput) { 
        let shippingSlotManager = this;
        shippingMethodInput.addEventListener('change', function() {
            // On shipping method change, display load slots for selected method
            shippingSlotManager.listShippingSlotsForAMethod(shippingMethodInput);
        })
    }

    listShippingSlotsForAMethod(shippingMethodInput) { 
        console.log(shippingMethodInput);
    }
}
