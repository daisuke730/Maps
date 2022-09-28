window.onload = function() {
    $('#url-input').on('input', function () {
        parseURL()
    })

    $('#start-point-name').on('input', function () {
        regenerateRouteName()
    })

    $('#end-point-name').on('input', function () {
        regenerateRouteName()
    })

    parseURL()
}

function parseURL() {
    let url = $('#url-input').val()
    if (validateURL(url)) {
        let { startPointName, endPointName } = parseNameFromURL(url)
        $('#start-point-name').val(startPointName)
        $('#end-point-name').val(endPointName)
        regenerateRouteName()
    }
}

function regenerateRouteName() {
    let startPointName = $('#start-point-name').val()
    let endPointName = $('#end-point-name').val()
    let name = `${startPointName} から ${endPointName} まで`
    $('#route-name').val(name)
}