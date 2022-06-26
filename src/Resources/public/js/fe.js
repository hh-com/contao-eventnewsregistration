let prepareRegisterForm = function(event) {
    var e = document.getElementById('regform');
    var d = document.createElement('form');
    d.setAttribute("id", "regform");
    d.setAttribute("action", "");
    d.setAttribute("method", "post");
    d.innerHTML = e.innerHTML;
    e.parentNode.replaceChild(d, e);

    var placesInput = document.getElementById("places");
    var calculatedTotal = document.getElementById("calculatedTotal");
    
    let updatePriceAndInput = function(event) {

        if (this.value > total_places_free)
            this.value = total_places_free;

        if (normalPrice > 0) {
            let totalPrice = normalPrice * this.value;
            calculatedTotal.innerHTML = parseFloat(totalPrice).toFixed(2);  
        }
    }

    placesInput.addEventListener('keyup', updatePriceAndInput, false);
    placesInput.addEventListener('click', updatePriceAndInput, false);
};

if (typeof userstatus !== 'undefined') {
    if (userstatus == 'loggedin') {
        document.addEventListener("DOMContentLoaded", prepareRegisterForm, false);
    } else {
        document.getElementById("registernow").addEventListener("click", function(event) {
            prepareRegisterForm();
            this.remove();
        }, false);
    }

    /**
     * Todo: Fade in Submit Button after X Seconds
     */
}
