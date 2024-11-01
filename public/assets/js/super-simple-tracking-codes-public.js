window.addEventListener("load", function() {
    var options = {
		onStatusChange: function () {
            window.location.reload();
        }
    };

    var new_options = {};
    Object.keys(options).forEach(key => new_options[key] = options[key]);
    Object.keys(sstc_cookieconsent).forEach(key => new_options[key] = sstc_cookieconsent[key]);

    cookieconsent.initialise(new_options);
});